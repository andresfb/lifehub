You can use the OpenAPI spec to generate a typed API client for your Vue SPA, but not your `vue-router` pages directly.

That distinction matters:

* OpenAPI describes backend endpoints like `GET /users`, `POST /orders`
* Vue Router describes frontend URLs like `/dashboard`, `/users/:id`

So the usual flow is:

1. generate a client from OpenAPI
2. call that client from your Vue composables / stores / components
3. optionally build a small mapping layer if you want to organize SPA features around backend endpoints

## Best approach

For a Vue SPA, a very solid setup is:

* `openapi-typescript` for TypeScript types
* `openapi-fetch` or `axios` for runtime requests

Or use an all-in-one generator like:

* `orval`
* `@hey-api/openapi-ts`

My recommendation for Vue today: `@hey-api/openapi-ts` or `orval` ✔️

---

# Option 1: Generate a typed API client

## Install

```bash
npm install -D @hey-api/openapi-ts
```

## Generate from your Swagger/OpenAPI file

If your backend exposes:

```txt
http://localhost:8080/v3/api-docs
```

run:

```bash
npx @hey-api/openapi-ts \
  --input http://localhost:8080/v3/api-docs \
  --output src/api/generated
```

That will create typed client code based on your backend API.

---

# Example Vue project structure

```txt
src/
  api/
    generated/
      client/
      models/
      sdk/
    http.ts
  composables/
    useUsers.ts
  views/
    UsersView.vue
```

---

# Create a shared HTTP client

Depending on the generator output, you usually configure the base URL and auth once.

Example:

```ts
// src/api/http.ts
import axios from 'axios'

export const apiHttp = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

apiHttp.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})
```

---

# Example using generated SDK in a composable

Assume your OpenAPI generator creates a function like `getUsers`.

```ts
// src/composables/useUsers.ts
import { ref, onMounted } from 'vue'
import { getUsers } from '@/api/generated/sdk.gen'

type User = {
  id: number
  name: string
  email: string
}

export function useUsers() {
  const users = ref<User[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const loadUsers = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await getUsers()
      users.value = response.data ?? []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to load users'
    } finally {
      loading.value = false
    }
  }

  onMounted(loadUsers)

  return {
    users,
    loading,
    error,
    loadUsers,
  }
}
```

---

# Use it in a Vue component

```vue
<!-- src/views/UsersView.vue -->
<script setup lang="ts">
import { useUsers } from '@/composables/useUsers'

const { users, loading, error, loadUsers } = useUsers()
</script>

<template>
  <section>
    <h1>Users</h1>

    <button @click="loadUsers">Reload</button>

    <p v-if="loading">Loading...</p>
    <p v-else-if="error">{{ error }}</p>

    <ul v-else>
      <li v-for="user in users" :key="user.id">
        {{ user.name }} — {{ user.email }}
      </li>
    </ul>
  </section>
</template>
```

---

# Option 2: Generate TypeScript types only

If you only want types and prefer writing your own fetch logic:

## Install

```bash
npm install -D openapi-typescript
```

## Generate

```bash
npx openapi-typescript http://localhost:8080/v3/api-docs -o src/api/schema.d.ts
```

Then use the generated types in your own API wrapper.

Example:

```ts
// src/api/client.ts
import axios from 'axios'
import type { paths } from './schema'

const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
})

export async function getUsers() {
  type Response =
    paths['/users']['get']['responses']['200']['content']['application/json']

  const { data } = await http.get<Response>('/users')
  return data
}
```

This gives you full control, but more manual work.

---

# Can you generate Vue Router routes from OpenAPI?

Not automatically in a meaningful way.

Because backend routes are not the same as SPA routes.

For example:

* API route: `GET /users/{id}`
* SPA route: `/users/:id`

You *can* create a derived router config from OpenAPI, but it is usually the wrong abstraction. Your UI pages should be based on screens/features, not raw API endpoints.

That said, if you really want a generated feature map, you can transform OpenAPI paths into route-like metadata.

Example:

```ts
// src/api/openapi-route-map.ts
export type ApiRouteMeta = {
  path: string
  method: 'get' | 'post' | 'put' | 'patch' | 'delete'
  operationId?: string
  tag?: string
}

export const apiRoutes: ApiRouteMeta[] = [
  { path: '/users', method: 'get', operationId: 'getUsers', tag: 'Users' },
  { path: '/users/{id}', method: 'get', operationId: 'getUserById', tag: 'Users' },
  { path: '/orders', method: 'post', operationId: 'createOrder', tag: 'Orders' },
]
```

Then you could build admin/debug pages from that metadata. But for normal app navigation, I would not use backend paths as frontend routes.

---

# A practical setup for Vue + OpenAPI

Here is the setup I would actually use:

## `package.json`

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vue-tsc -b && vite build",
    "generate:api": "npx @hey-api/openapi-ts --input http://localhost:8080/v3/api-docs --output src/api/generated"
  }
}
```

Then whenever the backend changes:

```bash
npm run generate:api
```

---

# Add an environment variable

```env
VITE_API_BASE_URL=http://localhost:8080
```

---

# Good workflow

* backend updates OpenAPI
* frontend runs generator
* new endpoints and models appear in `src/api/generated`
* Vue components/stores/composables use generated functions
* TypeScript catches mismatches immediately 💡

---

# If your backend only gives Swagger UI

Sometimes you have Swagger UI at something like:

```txt
http://localhost:8080/swagger-ui/index.html
```

That is not the spec itself.

You need the actual OpenAPI JSON or YAML, often one of these:

```txt
/v3/api-docs
/swagger/v1/swagger.json
/openapi.json
/openapi.yaml
```

Use that URL as the generator input.

---

# Best tools summary

## Best all-around

`@hey-api/openapi-ts`

Good when you want generated clients and types with modern TS ergonomics.

## Great for Vue + API hooks

`orval`

Especially nice if you want generated API hooks and good organization.

## Minimal and flexible

`openapi-typescript`

Best when you only want types and prefer hand-written API logic.

---

# Recommendation

For your Vue SPA, do this:

1. point a generator at your OpenAPI spec
2. generate a typed client into `src/api/generated`
3. wrap generated calls in composables like `useUsers`, `useOrders`
4. keep `vue-router` separate from the backend routes

That gives you clean Vue architecture and avoids coupling your frontend navigation to the raw API.

Here is the simplest starter command again:

```bash
npx @hey-api/openapi-ts \
  --input http://localhost:8080/v3/api-docs \
  --output src/api/generated
```

Then import the generated functions in your Vue code.

If you want, I can give you a complete Vue 3 + Vite example wired to a generated OpenAPI client.

---

Yes — but the API should not send raw “routes”. It should send a **capability/navigation schema** that every client can interpret in its own way.

That is the scalable pattern for Web + TUI + Desktop + Mobile.

## The core idea

Your backend already exposes:

* **data contract** via OpenAPI
* **behavior contract** via endpoints

What you are missing is a third contract:

* **application capability contract**: “what can this user do, what screens exist, what actions are available, and how are they connected?”

That contract can be served by the API and used by:

* Vue SPA → build router + menus
* TUI → build commands / screens / flows
* Desktop app → build windows/views/navigation
* Mobile app → build tabs/stacks/screens

So yes, this is possible ✔️
But I would not model it as “frontend routes from backend”. I would model it as **capabilities + navigation metadata**.

---

# Recommended architecture

## 1. Keep OpenAPI for endpoint generation

Use OpenAPI only for:

* typed request/response models
* client SDK generation
* endpoint discovery for developers/tools

Do **not** try to stretch OpenAPI into a UI definition system.

---

## 2. Add a separate UI/Capability manifest endpoint

Example:

```http
GET /app-manifest
```

or

```http
GET /ui-schema
GET /navigation
GET /capabilities
```

This endpoint returns things like:

* available features
* navigation structure
* permissions
* labels/icons
* action definitions
* screen IDs
* optional layout hints
* endpoint bindings

---

# Example manifest

Here is a practical JSON format:

```json
{
  "appName": "Acme Platform",
  "version": "1.0.0",
  "features": [
    {
      "id": "dashboard",
      "title": "Dashboard",
      "kind": "screen",
      "nav": {
        "web": { "path": "/" },
        "mobile": { "stack": "main", "tab": "home" },
        "desktop": { "window": "main" },
        "tui": { "command": "dashboard" }
      },
      "permissions": [],
      "dataSources": [
        {
          "operationId": "getDashboardSummary"
        }
      ]
    },
    {
      "id": "users.list",
      "title": "Users",
      "kind": "screen",
      "nav": {
        "web": { "path": "/users" },
        "mobile": { "stack": "users", "tab": "users" },
        "desktop": { "window": "users" },
        "tui": { "command": "users" }
      },
      "permissions": ["users.read"],
      "dataSources": [
        {
          "operationId": "getUsers"
        }
      ],
      "children": [
        {
          "id": "users.detail",
          "title": "User Detail",
          "kind": "screen",
          "nav": {
            "web": { "path": "/users/:id" },
            "mobile": { "stack": "users" },
            "desktop": { "window": "user-detail" },
            "tui": { "command": "user view <id>" }
          },
          "permissions": ["users.read"],
          "dataSources": [
            {
              "operationId": "getUserById",
              "params": {
                "id": "$route.params.id"
              }
            }
          ],
          "actions": [
            {
              "id": "users.delete",
              "title": "Delete User",
              "kind": "mutation",
              "operationId": "deleteUser",
              "permissions": ["users.delete"]
            }
          ]
        }
      ]
    }
  ]
}
```

This is much better than saying “here are all frontend routes”, because it works across platforms.

---

# Why this is better than backend-defined routes

A route is only one platform’s navigation expression.

The real cross-platform concept is:

* **feature**
* **screen**
* **action**
* **permission**
* **data binding**
* **navigation target**

Those are universal.

For example:

* Vue uses `path: /users/:id`
* Mobile uses `stack: users`
* TUI uses `command: user view <id>`

Same feature, different navigation surface.

---

# Best design: backend sends metadata, frontend owns rendering

This is the sweet spot.

Backend should define:

* what features exist
* who can access them
* labels
* screen identifiers
* operation bindings
* navigation hierarchy
* visibility rules

Frontend should still decide:

* component implementation
* layout details
* animations
* platform-specific interaction patterns

That prevents your API from becoming a brittle “remote UI engine”.

---

# In Vue: build router from manifest

Here is a complete example.

## Types

```ts
// src/app-manifest/types.ts
export type AppManifest = {
  appName: string
  version: string
  features: FeatureNode[]
}

export type FeatureNode = {
  id: string
  title: string
  kind: 'screen' | 'action' | 'group'
  nav?: {
    web?: {
      path?: string
    }
    mobile?: {
      stack?: string
      tab?: string
    }
    desktop?: {
      window?: string
    }
    tui?: {
      command?: string
    }
  }
  permissions?: string[]
  dataSources?: DataSource[]
  actions?: FeatureAction[]
  children?: FeatureNode[]
}

export type DataSource = {
  operationId: string
  params?: Record<string, string>
}

export type FeatureAction = {
  id: string
  title: string
  kind: 'mutation' | 'navigation'
  operationId?: string
  permissions?: string[]
}
```

---

## Manifest loader

```ts
// src/app-manifest/api.ts
import type { AppManifest } from './types'

export async function fetchAppManifest(): Promise<AppManifest> {
  const response = await fetch('/api/app-manifest', {
    headers: {
      'Content-Type': 'application/json'
    }
  })

  if (!response.ok) {
    throw new Error(`Failed to fetch app manifest: ${response.status}`)
  }

  return response.json() as Promise<AppManifest>
}
```

---

## Component registry

You need a local mapping from backend feature IDs to Vue components.

```ts
// src/app-manifest/componentRegistry.ts
import type { Component } from 'vue'

import DashboardView from '@/views/DashboardView.vue'
import UsersListView from '@/views/UsersListView.vue'
import UserDetailView from '@/views/UserDetailView.vue'
import NotImplementedView from '@/views/NotImplementedView.vue'

const registry: Record<string, Component> = {
  dashboard: DashboardView,
  'users.list': UsersListView,
  'users.detail': UserDetailView
}

export function resolveFeatureComponent(featureId: string): Component {
  return registry[featureId] ?? NotImplementedView
}
```

This is important: the backend describes **what exists**, but the frontend still maps that to actual code.

---

## Router builder

```ts
// src/app-manifest/routerBuilder.ts
import type { RouteRecordRaw } from 'vue-router'
import type { FeatureNode } from './types'
import { resolveFeatureComponent } from './componentRegistry'

function flattenFeatures(nodes: FeatureNode[]): FeatureNode[] {
  const result: FeatureNode[] = []

  for (const node of nodes) {
    result.push(node)

    if (node.children?.length) {
      result.push(...flattenFeatures(node.children))
    }
  }

  return result
}

function isWebScreen(node: FeatureNode): boolean {
  return node.kind === 'screen' && typeof node.nav?.web?.path === 'string'
}

export function buildRoutesFromManifest(features: FeatureNode[]): RouteRecordRaw[] {
  const flat = flattenFeatures(features)

  return flat
    .filter(isWebScreen)
    .map((feature) => ({
      path: feature.nav!.web!.path!,
      name: feature.id,
      component: resolveFeatureComponent(feature.id),
      meta: {
        title: feature.title,
        permissions: feature.permissions ?? [],
        featureId: feature.id,
        dataSources: feature.dataSources ?? [],
        actions: feature.actions ?? []
      }
    }))
}
```

---

## Router setup

```ts
// src/router/index.ts
import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { fetchAppManifest } from '@/app-manifest/api'
import { buildRoutesFromManifest } from '@/app-manifest/routerBuilder'

const staticRoutes: RouteRecordRaw[] = [
  {
    path: '/forbidden',
    name: 'forbidden',
    component: () => import('@/views/ForbiddenView.vue')
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue')
  }
]

export const router = createRouter({
  history: createWebHistory(),
  routes: staticRoutes
})

let manifestLoaded = false

export async function installManifestRoutes() {
  if (manifestLoaded) return

  const manifest = await fetchAppManifest()
  const dynamicRoutes = buildRoutesFromManifest(manifest.features)

  for (const route of dynamicRoutes) {
    if (!router.hasRoute(route.name!)) {
      router.addRoute(route)
    }
  }

  manifestLoaded = true
}
```

---

## App bootstrap

```ts
// src/main.ts
import { createApp } from 'vue'
import App from './App.vue'
import { router, installManifestRoutes } from './router'

async function bootstrap() {
  await installManifestRoutes()

  const app = createApp(App)
  app.use(router)

  await router.isReady()
  app.mount('#app')
}

bootstrap().catch((error) => {
  console.error('Failed to bootstrap app', error)
})
```

---

# Build menus from the same manifest

That is one of the biggest wins.

```ts
// src/app-manifest/menuBuilder.ts
import type { FeatureNode } from './types'

export type MenuItem = {
  id: string
  title: string
  to?: string
  children?: MenuItem[]
}

export function buildMenu(features: FeatureNode[]): MenuItem[] {
  return features
    .filter((feature) => feature.kind === 'screen' || feature.kind === 'group')
    .map((feature) => ({
      id: feature.id,
      title: feature.title,
      to: feature.nav?.web?.path,
      children: feature.children?.length ? buildMenu(feature.children) : undefined
    }))
}
```

Now your sidebar, breadcrumbs, command palette, mobile navigation, and TUI commands can all come from the same source.

---

# For TUI, Desktop, Mobile

This same manifest can drive each client.

## TUI

Map:

* `feature.id`
* `title`
* `nav.tui.command`
* `actions`

into:

* commands
* subcommands
* keyboard menus
* contextual actions

Example:

```json
{
  "id": "users.list",
  "title": "Users",
  "nav": {
    "tui": {
      "command": "users"
    }
  }
}
```

---

## Desktop

Map into:

* side nav items
* tabs
* native menu items
* view registry

---

## Mobile

Map into:

* tabs
* navigation stacks
* screen registry
* hidden/visible screens based on capability

---

# Important warning: avoid full server-driven UI unless you really need it

There are levels here.

## Good

Server-driven **navigation/capability metadata**

## Risky

Server-driven **screen layouts**

## Very risky

Server-driven **arbitrary component tree rendering**

The deeper you go, the more problems you get:

* hard to test
* hard to version
* poor type safety
* weak native UX on mobile/desktop
* debugging pain
* backend becomes tightly coupled to all clients

So I strongly recommend:

* server defines features/capabilities/navigation
* clients own concrete UI implementation

That gives you reuse without turning everything into a mini low-code platform ❗

---

# Best model: Capability-Based UI

Think in terms of:

* `features`: screens/modules available
* `actions`: what operations are allowed
* `permissions`: who can do them
* `navigation`: how features are reached on each platform
* `bindings`: which API operations power the feature

This is more future-proof than “routes from backend”.

---

# Relationship with OpenAPI

A nice pattern is this:

## OpenAPI

Defines:

* `getUsers`
* `getUserById`
* `deleteUser`

## App Manifest

Defines:

* `users.list` screen uses `getUsers`
* `users.detail` screen uses `getUserById`
* `users.delete` action uses `deleteUser`

That means your manifest references **operationId** values from OpenAPI.

Example:

```json
{
  "id": "users.list",
  "dataSources": [
    {
      "operationId": "getUsers"
    }
  ]
}
```

That is excellent because:

* backend stays source of truth
* frontend can call generated client methods
* manifest remains stable and human-readable

---

# Even better: generate types for the manifest too

You can define your manifest schema in JSON Schema or TypeScript and validate it.

Example with Zod:

```ts
// src/app-manifest/schema.ts
import { z } from 'zod'

export const DataSourceSchema = z.object({
  operationId: z.string(),
  params: z.record(z.string()).optional()
})

export const FeatureActionSchema = z.object({
  id: z.string(),
  title: z.string(),
  kind: z.enum(['mutation', 'navigation']),
  operationId: z.string().optional(),
  permissions: z.array(z.string()).optional()
})

export const FeatureNodeSchema: z.ZodType<any> = z.lazy(() =>
  z.object({
    id: z.string(),
    title: z.string(),
    kind: z.enum(['screen', 'action', 'group']),
    nav: z.object({
      web: z.object({
        path: z.string().optional()
      }).optional(),
      mobile: z.object({
        stack: z.string().optional(),
        tab: z.string().optional()
      }).optional(),
      desktop: z.object({
        window: z.string().optional()
      }).optional(),
      tui: z.object({
        command: z.string().optional()
      }).optional()
    }).optional(),
    permissions: z.array(z.string()).optional(),
    dataSources: z.array(DataSourceSchema).optional(),
    actions: z.array(FeatureActionSchema).optional(),
    children: z.array(FeatureNodeSchema).optional()
  })
)

export const AppManifestSchema = z.object({
  appName: z.string(),
  version: z.string(),
  features: z.array(FeatureNodeSchema)
})

export type AppManifest = z.infer<typeof AppManifestSchema>
```

Then validate on fetch.

---

# A very practical backend response shape

If you want something simpler, start here:

```json
{
  "features": [
    {
      "id": "dashboard",
      "title": "Dashboard",
      "webPath": "/",
      "mobileScreen": "Dashboard",
      "desktopView": "dashboard",
      "tuiCommand": "dashboard",
      "permissions": [],
      "operationIds": ["getDashboardSummary"]
    },
    {
      "id": "users.list",
      "title": "Users",
      "webPath": "/users",
      "mobileScreen": "UsersList",
      "desktopView": "users",
      "tuiCommand": "users",
      "permissions": ["users.read"],
      "operationIds": ["getUsers"]
    }
  ]
}
```

That is enough to prove the pattern before you build a richer manifest.

---

# What not to do

## Do not make the API return Vue-specific things

Bad:

```json
{
  "component": "UsersView.vue",
  "beforeEnter": "authGuard"
}
```

That couples backend to Vue.

---

## Do not expose raw OpenAPI paths as app features

Bad:

```json
{
  "path": "/api/users/{id}",
  "method": "GET"
}
```

That is an endpoint, not a feature.

---

## Do not assume every endpoint deserves a screen

Many endpoints are:

* background lookups
* autocomplete
* internal workflow helpers
* batch operations
* health/status endpoints

So features should reference endpoints, not be derived 1:1 from them.

---

# My recommendation

Use **two contracts**:

## Contract 1: OpenAPI

For API client generation

## Contract 2: App Manifest

For cross-platform feature/navigation discovery

That is the clean architecture.

---

# Best mental model

Think of your backend providing:

* **what exists**
* **who can access it**
* **which operations power it**

And each client decides:

* **how it is presented**

That separation scales beautifully across Vue, TUI, Desktop, and Mobile 💡

---

# Recommendation for your stack

For your case, I would implement:

* OpenAPI spec for SDK generation
* `/app-manifest` endpoint for feature/navigation metadata
* frontend registry mapping `feature.id -> platform view/component/screen`
* manifest references OpenAPI `operationId`
* permissions included per feature/action
* clients render navigation dynamically from manifest

That gives you one backend-defined “function catalog” without over-coupling all UIs.

I can sketch a full end-to-end example next: backend manifest shape + generated OpenAPI client + Vue router/menu builder + a TUI command mapper.

