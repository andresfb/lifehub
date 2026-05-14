Make a plan to create a deployment process for this mono repo using Ansible.

There needs to be four separate processes that can be called individually or with a single command (we'll use `make` for this, details below).

# Common tasks among processes

- Store all the Ansible files in `./packages/ansible/`
- Use the `./packages/ansible/deploy/` folder as a temporary/work folder.
- When a process starts, check if the repository has already been cloned to `./packages/ansible/deploy/lifehub` on the local machine. If not, clone it from `git@github.com:andresfb/lifehub.git`. If it was cloned, pull the latest changes from the `main` branch.
- Once the process connects to each host, it needs to create a folder structure in the `deployment_path` that allows for keeping up to 3 release versions.
- The folder structure should be like this:
```
|-- .meta
|   |-- latest_release [has the number of the latest release]
|-- current -> releases/2
|-- releases
|   |-- 1
|   |   |-- [uploaded files]
|   |-- 2
|   |   |-- [uploaded files]
|-- shared
```

- When uploading the new files, the process needs to read the `[deployment path]/.meta/latest_release`. If the file doesn't exist, create it with the number 0.
- Read the number from the file and increment it by one. Then, create a new folder inside the `[deployment path]/releases` folder with that number and upload all files there. This folder is referenced here as `[release number]` 
- All necessary commands must be run in this newly created folder.
- Once all uploads are done and all commands run successfully, the process needs to:
	- Change the `[deployment path]/current` symlink to point to the newly created release folder: `[deployment path]/current -> [deployment path]/releases/[release number]`
	- Update the `[deployment path]/.meta/latest_release` file with the new release number

# Processes

## API Web

### Servers
webeloper.lan
Access via `ssh root@webeloper.lan`

### Tooling
- The process needs to confirm that PHP v8.5 is installed. If not, abort the process. Otherwise, get the binary path `which php` and store the value in a reusable variable, e.g., `$php_bin`
- Confirm that the command `composer` is installed on the server. Use `apt` if it isn't.

### Deployment path
/opt/data1/lifehub/api/web

### Deployment folder structure
```
|-- .meta
|   |-- latest_release
|-- current -> [deployment path]/releases/[release number]
|-- releases
|   |-- 1
|   |   |-- apps
|   |   |	|-- api
|   |   |   |   |-- .env -> [deployment path]/shared/.env
|   |   |   |   |-- ...
|-- shared
    |-- .env
```

### Files to Upload
After cloning/pulling the repo, these are the files needed from the local monorepo structure to the deployment path

- /packages/ansible/deploy/lifehub/apps/api -> `[deployment path]/releases/[release number]/apps/api`
- Skip from uploading files and folders:
	* .agents
	* .claude
	* .bruno
	* .codex
	* bootstrap/cache
	* deployer
	* docker
	* storage
	* stubs
	* tests
	* vendor
	* .*
	* AGENTS.md
	* CLAUDE.md
	* README.md
	* LICENSE
	* boost.json
	* deploy.php
	* docker-compose*
	* phpstan*
	* phpunit.xml
	* pint.json
	* rector.php
- .env
	* The production `.env` file can be fetched using 1Password CLI:
		`op read "op://LifeHub/EnvProdApi/.env" --out-file ./packages/ansible/deploy/api/.env`
	* The `.env` file needs to be uploaded to the `[deployment path]/shared/.env` and create a symlink on `[deployment path]/releases/[release number]/apps/api/.env`

### Commands
Once all files are uploaded, the process needs to run these commands in the `[deployment path]/releases/[release number]/apps/api` folder:

- `mkdir -p bootstrap/cache`
- `chmod -R 777 bootstrap/cache`
- `mkdir -p storage/app/private`
- `mkdir -p storage/app/public`
- `mkdir -p storage/framework/cache/data`
- `mkdir -p storage/framework/sessions`
- `mkdir -p storage/framework/testing`
- `mkdir -p storage/framework/views`
- `mkdir -p storage/logs`
- `chmod -R 777 storage`
- `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`
- `$php_bin artisan optimize`

## API Worker

### Servers
horizon.lan
Access via `ssh bastidas@horizon.lan`
This server will need sudo access to run commands

### Tooling
- The process needs to confirm that PHP v8.5 is installed. If not, abort the process. Otherwise, get the binary path `which php` and store the value in a reusable variable, e.g., `$php_bin`
- Confirm that the command `composer` is installed on the server. Use `apt` if it isn't.
- Confirm `supervisord` is installed and install it with `apt` if not.
- Confirm `bun` is installed. Use `curl -fsSL https://bun.sh/install | bash` if it's not.

### Deployment path
/opt/data1/lifehub/api/worker/

### Deployment folder structure
```
|-- .meta
|   |-- latest_release
|-- current -> [deployment path]/releases/[release number]
|-- releases
|   |-- 1
|   |   |-- apps
|   |   |	|-- worker
|   |   |   |   |-- .env -> [deployment path]/shared/.env
|   |   |   |   |-- ...
|   |   |-- packages
|   |   |   |-- tools
|   |   |   |   |-- scripts
|-- shared
    |-- .env
```

### Files to Upload
- /packages/ansible/deploy/lifehub/apps/api -> `[deployment path]/releases/[release number]/apps/worker`
- /packages/ansible/deploy/lifehub/packages/tools/scripts -> `[deployment path]/releases/[release number]/packages/tools/scripts`
- Skip from uploading files and folders:
	* .agents
	* .claude
	* .bruno
	* .codex
	* bootstrap/cache
	* deployer
	* docker
	* storage
	* stubs
	* tests
	* vendor
	* .*
	* AGENTS.md
	* CLAUDE.md
	* README.md
	* LICENSE
	* boost.json
	* deploy.php
	* docker-compose*
	* phpstan*
	* phpunit.xml
	* pint.json
	* rector.php
- .env
	* The production `.env` file can be fetched using 1Password CLI:
	  `op read "op://LifeHub/EnvProdApi/.env" --out-file ./packages/ansible/deploy/api/.env`
	* The `.env` file needs to be uploaded to the `[deployment path]/shared/.env` and create a symlink on `[deployment path]/releases/[release number]/apps/worker/.env`

### Commands
Once all files are uploaded, the process needs to run these commands in the `[deployment path]/releases/[release number]/apps/worker` folder:

- `mkdir -p bootstrap/cache`
- `chmod -R 777 bootstrap/cache`
- `mkdir -p storage/app/private`
- `mkdir -p storage/app/public`
- `mkdir -p storage/framework/cache/data`
- `mkdir -p storage/framework/sessions`
- `mkdir -p storage/framework/testing`
- `mkdir -p storage/framework/views`
- `mkdir -p storage/logs`
- `chmod -R 777 storage`
- `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`
- `$php_bin artisan optimize`
- Grab the output of `sudo supervisorctl status`
- Look for a process that starts with `lifehub-api` and get the full name, it should be something like: `lifehub-api:lifehub-api_00`
- With this name run `sudo supervisorctl restart [found name]`

## Frontend Web

### Servers
webeloper.lan
Access via `ssh root@webeloper.lan`

### Tooling
- The process needs to confirm that PHP v8.5 is installed. If not, abort the process. Otherwise, get the binary path `which php` and store the value in a reusable variable, e.g., `$php_bin`
- Confirm that the command `composer` is installed on the server. Use `apt` if it isn't.
- Confirm `node` and `npm` are installed. If not, use:
```
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.4/install.sh | bash
\. "$HOME/.nvm/nvm.sh"
nvm install 24
node -v # Should print "v24.15.0"
npm -v # Should print "11.12.1"
```

### Deployment path
/opt/data1/lifehub/front/web/

### Deployment folder structure
```
|-- .meta
|   |-- latest_release
|-- current -> [deployment path]/releases/[release number]
|-- releases
|   |-- 1
|   |   |-- apps
|   |   |	|-- web
|   |   |   |   |-- .env -> [deployment path]/shared/.env
|   |   |   |   |-- ...
|   |   |-- packages
|   |   |   |-- sdk-web
|-- shared
    |-- .env
```

### Files to Upload
- ./packages/ansible/deploy/lifehub/apps/web -> `[deployment path]/releases/[release number]/apps/web`
- ./packages/ansible/deploy/lifehub/packages/sdk-web -> `[deployment path]/releases/[release number]/packages/sdk-web`
- Skips:
	* .agents
    * .claude
    * .bruno
    * .codex
    * bootstrap/cache
    * deployer
    * docker
    * storage
    * stubs
    * tests
    * vendor
    * .*
    * AGENTS.md
    * CLAUDE.md
    * README.md
    * LICENSE
    * boost.json
    * deploy.php
    * docker-compose*
    * phpstan*
    * phpunit.xml
    * pint.json
    * rector.php
    * node_modules
    * skills-lock.json
- .env
	* The production `.env` file can be fetched using 1Password CLI:
		`op read "op://LifeHub/EnvProdWeb/.env" --out-file ./packages/ansible/deploy/web/.env`
	* The `.env` file needs to be uploaded to the `[deployment path]/shared/.env` and create a symlink on `[deployment path]/releases/[release number]/apps/web/.env`

### Commands
Once all files are uploaded, the process needs to run these commands in the `[deployment path]/releases/[release number]/apps/web` folder:

- `mkdir -p bootstrap/cache`
- `chmod -R 777 bootstrap/cache`
- `mkdir -p storage/app/private`
- `mkdir -p storage/app/public`
- `mkdir -p storage/framework/cache/data`
- `mkdir -p storage/framework/sessions`
- `mkdir -p storage/framework/testing`
- `mkdir -p storage/framework/views`
- `mkdir -p storage/logs`
- `chmod -R 777 storage`
- `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`
- `$php_bin artisan optimize`
- `npm install`
- `npm run build`

## Frontend Worker

### Servers
horizon.lan
Access via `ssh bastidas@horizon.lan`
This server will need sudo access to run commands

### Tooling
- The process needs to confirm that PHP v8.5 is installed. If not, abort the process. Otherwise, get the binary path `which php` and store the value in a reusable variable, e.g., `$php_bin`
- Confirm that the command `composer` is installed on the server. Use `apt` if it isn't.
- Confirm `supervisord` is installed and install it with `apt` if not.
- Confirm `bun` is installed. Use `curl -fsSL https://bun.sh/install | bash` if it's not.

### Deployment path
/opt/data1/lifehub/front/worker/

### Deployment folder structure
```
|-- .meta
|   |-- latest_release
|-- current -> [deployment path]/releases/[release number]
|-- releases
|   |-- 1
|   |   |-- apps
|   |   |	|-- web
|   |   |   |   |-- .env -> [deployment path]/shared/.env
|   |   |   |   |-- ...
|   |   |-- packages
|   |   |   |-- sdk-web
|-- shared
    |-- .env
```

### Files to Upload
- ./packages/ansible/deploy/lifehub/apps/web -> `[deployment path]/releases/[release number]/apps/web`
- ./packages/ansible/deploy/lifehub/packages/sdk-web -> `[deployment path]/releases/[release number]/packages/sdk-web`
- Skips:
	* .agents
	* .claude
	* .bruno
	* .codex
	* bootstrap/cache
	* deployer
	* docker
	* storage
	* stubs
	* tests
	* vendor
	* .*
	* AGENTS.md
	* CLAUDE.md
	* README.md
	* LICENSE
	* boost.json
	* deploy.php
	* docker-compose*
	* phpstan*
	* phpunit.xml
	* pint.json
	* rector.php
	* node_modules
	* skills-lock.json
- .env
	* The production `.env` file can be fetched using 1Password CLI:
	  `op read "op://LifeHub/EnvProdWeb/.env" --out-file ./packages/ansible/deploy/web/.env`
	* The `.env` file needs to be uploaded to the `[deployment path]/shared/.env` and create a symlink on `[deployment path]/releases/[release number]/apps/web/.env`

### Commands
Once all files are uploaded, the process needs to run these commands in the `[deployment path]/releases/[release number]/apps/web` folder:

- `mkdir -p bootstrap/cache`
- `chmod -R 777 bootstrap/cache`
- `mkdir -p storage/app/private`
- `mkdir -p storage/app/public`
- `mkdir -p storage/framework/cache/data`
- `mkdir -p storage/framework/sessions`
- `mkdir -p storage/framework/testing`
- `mkdir -p storage/framework/views`
- `mkdir -p storage/logs`
- `chmod -R 777 storage`
- `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`
- `$php_bin artisan optimize`
- Grab the output of `sudo supervisorctl status`
- Look for a process that starts with `lifehub-web` and get the full name, it should be something like: `lifehub-web:lifehub-web_00`
- With this name, run `sudo supervisorctl restart [found name]`

## Cleanup
Once the deployment is done on each process, check the number of releases in the `[deployment path]/releases` folder and delete the oldest ones to match the three max releases.

# Make

Lastly, create a Makefile on the root of this repo that will allow me to run the ansible deployments with these commands:

- make deploy-api-web
- make deploy-api-worker
- make deploy-front-web
- make deploy-front-worker
- make deploy-all
