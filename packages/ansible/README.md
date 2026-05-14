# LifeHub Deployment

Ansible-based deployment for the LifeHub monorepo. Four independent processes can be run individually or all at once via `make`.

---

## How it works

Each deployment follows a capistrano-style release model:

1. The repo is cloned (or pulled) locally to `packages/ansible/deploy/lifehub/`
2. The production `.env` is fetched from 1Password
3. On the remote host a versioned folder structure is maintained:

```
/opt/data1/lifehub/<app>/
├── .meta/
│   └── latest_release      ← tracks the current release number
├── current -> releases/3   ← symlink, always points to the live release
├── releases/
│   ├── 1/
│   ├── 2/
│   └── 3/                  ← newly deployed release
│       └── apps/<app>/
│           ├── .env -> shared/.env
│           ├── storage -> shared/storage
│           └── bootstrap/cache/
└── shared/
    ├── .env                 ← persistent, never overwritten
    └── storage/             ← persistent across all releases
```

A maximum of **3 releases** are kept. The oldest is deleted automatically after each successful deploy.

---

## First-time setup

### 1. Install the required Ansible collection

```bash
cd packages/ansible
ansible-galaxy collection install -r requirements.yml
```

### 2. Create your local inventory file

The inventory file is **not committed** to the repo because it contains private server hostnames and usernames. Create it from the provided template:

```bash
cp packages/ansible/inventory/hosts.yml.example packages/ansible/inventory/hosts.yml
```

Then open `packages/ansible/inventory/hosts.yml` and replace the placeholder values:

```yaml
all:
  hosts:
    YOUR_WEB_SERVER_HOSTNAME:    # ← your actual hostname or IP
      ansible_user: YOUR_WEB_SSH_USER
    YOUR_WORKER_HOSTNAME:
      ansible_user: YOUR_WORKER_SSH_USER
```

### 3. Verify SSH access

```bash
ansible all -m ping
```

All hosts should return `pong` before running any deployment.

---

## Prerequisites on target servers

| Tool | api-web | api-worker | front-web | front-worker |
|------|:-------:|:----------:|:---------:|:------------:|
| PHP 8.5 | ✓ | ✓ | ✓ | ✓ |
| Composer | ✓ | ✓ | ✓ | ✓ |
| Node / npm | | | ✓ | |
| supervisord | | ✓ | | ✓ |
| bun | | ✓ | | ✓ |

Missing tools are installed automatically during deployment (Composer and supervisord via `apt`; bun via the official install script; Node via nvm).

**PHP 8.5 must be installed before running any deployment.** The playbook aborts if it is not found.

**supervisord programs** (`lifehub-api`, `lifehub-web`) must already be configured on the worker server before the first deploy. The playbook restarts them but does not create them.

---

## Deployment commands

All commands are run from the **project root**.

```bash
make deploy-api-web       # API → webeloper.lan
make deploy-api-worker    # API worker → horizon.lan  (prompts for sudo password)
make deploy-front-web     # Frontend → webeloper.lan
make deploy-front-worker  # Frontend worker → horizon.lan  (prompts for sudo password)

make deploy-all           # Runs all four in sequence
```

> `deploy-api-worker` and `deploy-front-worker` prompt for the remote user's sudo password once at the start of the run.

---

## What each deployment does

### API Web (`webeloper.lan`)
- Syncs `apps/api/` to the new release directory
- Uploads `.env` to `shared/` and symlinks it
- Symlinks `shared/storage/` into the release
- Runs `composer install` and `php artisan optimize`

### API Worker (`horizon.lan`)
- Syncs `apps/api/` (as `apps/worker/`) and `packages/tools/scripts/` to the new release
- Same `.env` and storage setup as API Web
- Runs `composer install` and `php artisan optimize`
- Restarts the `lifehub-api:*` supervisord process

### Frontend Web (`webeloper.lan`)
- Syncs `apps/web/` and `packages/sdk-web/` to the new release
- Uploads `.env` to `shared/` and symlinks it
- Symlinks `shared/storage/` into the release
- Runs `composer install`, `php artisan optimize`, `npm install`, and `npm run build`

### Frontend Worker (`horizon.lan`)
- Same files as Frontend Web
- Same `.env` and storage setup
- Runs `composer install` and `php artisan optimize`
- Restarts the `lifehub-web:*` supervisord process

---

## Secrets

`.env` files are never stored in this repository. They are fetched at deploy time from 1Password using the `op` CLI:

| Deployment | 1Password item |
|------------|---------------|
| API (web + worker) | `op://LifeHub/EnvProdApi/.env` |
| Frontend (web + worker) | `op://LifeHub/EnvProdWeb/.env` |

The `op` CLI must be installed and authenticated locally before running any deployment.

---

## Project structure

```
packages/ansible/
├── ansible.cfg                   ← Ansible configuration
├── requirements.yml              ← Collection dependencies (ansible.posix)
├── inventory/
│   ├── hosts.yml                 ← gitignored — create from .example
│   └── hosts.yml.example         ← template to copy and fill in
├── group_vars/
│   └── all.yml                   ← shared variables (repo URL, max releases, etc.)
├── roles/
│   ├── local_repo_sync/          ← clone or pull the repo locally
│   ├── release_setup/            ← create release directory, compute release number
│   ├── release_finalize/         ← flip the `current` symlink
│   ├── release_cleanup/          ← prune releases beyond the max (3)
│   ├── php_verify/               ← assert PHP 8.5, expose php_bin
│   ├── composer_verify/          ← install composer if absent
│   ├── supervisor_verify/        ← install supervisord if absent
│   ├── bun_verify/               ← install bun if absent
│   └── node_verify/              ← install Node 24 via nvm if absent
├── deploy_api_web.yml
├── deploy_api_worker.yml
├── deploy_front_web.yml
└── deploy_front_worker.yml
```
