# Shared Library Analysis: Migrating LifeHub to Rust

## Context

LifeHub is currently two Laravel apps — an API (Sanctum + Fortify, modular with nwidart/laravel-modules) and a web client (Inertia/Vue 3 + daisyUI). The goal is to migrate to a Rust stack (Axum API, Dioxus for web/desktop/mobile, Ratatui for TUI) with a shared core library (`lifehub-core`) to avoid duplicating domain logic across five clients.

This document analyzes the existing codebase to identify what belongs in that shared library vs. what stays in each application crate.

---

## What Goes in the Shared Library (`lifehub-core`)

### 1. Domain Types & Structs

Every client needs to know what a Marker, a HomepageSection, or a User looks like. These are the Rust structs that mirror your current Eloquent models — without the database layer.

**From the API project (models + fields):**
- `User` — id, name, email, roles, 2FA state
- `Marker` — url, title, domain, description, summary, notes, priority, status, category, tags
- `Category` — slug, title, active, order
- `HomepageSection` — slug, name, active, order, items
- `HomepageItem` — slug, title, url, bg_color, icon, active, order, tags
- `SearchProvider` — name, url, icon, icon_color, active, default, order
- `Reminder` — title, notes, due_at, completed_at, snoozed_until (polymorphic owner)
- `AiProvider` — code, name, driver, enabled, url, api_version, deployment configs
- `AiModel` — name, enabled, capability flags (text, images, tts, stt, embeddings, reranking, files)
- `SearchHistory` — module, type, hash, query
- `Tag` — name, slug, type
- `Invitation` — email, token, accepted_at, expires_at
- `UserSetting` — key-value with typed payload (currently JSON)
- `EntityLink` — generic entity relationship

**Why shared:** The API serializes these. Every client deserializes them. If the struct definition lives in one place, you get compile-time guarantees that they match.

### 2. Enums

All of these are used for both API responses and client-side rendering/logic:

- `MarkerStatus` — active, archived, hidden (+ scopes like `active()`, `archived()`)
- `ModuleKey` — BOOKMARKS, CORE, DASHBOARD, etc.
- `ModuleStatus` — enabled, disabled, pending
- `ModuleAccessLevel` — read, write, admin
- `ModuleVisibility` — public, private
- `ModuleEndpointType` — action, command
- `MorphTypes` — CORE_REMINDER, DASHBOARD_PIN, BOOKMARKS_MARKER, etc. (used for polymorphic relationships)
- `AiModelFeatures` — capability flags enum

**Why shared:** Enums that travel over the wire must match on both sides. Defining them once eliminates deserialization mismatches.

### 3. Validation Rules

Your current FormRequests encode business constraints. These become shared validation functions:

- **Marker validation:** URL format, title length, status must be valid enum, category must exist, priority range
- **Pin/HomepageItem validation:** title required, section must exist, URL optional but validated, icon constraints
- **SearchProvider validation:** name + url required, URL format
- **AI Provider validation:** code + driver + name required, API key format, capability flags
- **User profile validation:** name + email required, email uniqueness, password strength rules
- **Bulk import validation:** CSV/JSON format constraints, per-row validation

**Why shared:** The API validates on write. But clients should validate before sending (for instant feedback). If validation logic lives in the shared library, a TUI and a mobile app enforce the same rules without re-implementing them.

### 4. DTOs / API Contract Types

These are the "wire format" types — what gets serialized/deserialized over HTTP:

- `MarkerItem`, `BulkMarkerImportItem`
- `PinCreateItem`, `PinUpdateItem`
- `HomepageItemDto`, `HomepageSectionItem`
- `ProviderItem`, `ResolvedUserAiProvider`
- `ApiErrorItem`
- `SearchHistoryItem`
- `NewUserItem`
- `ModuleRecordItem`, `MorphTypesItems`
- `NavigationItem`, `ModuleManifest`, `ManifestItem`

**Why shared:** These are literally the API contract. With `serde` derive macros, both the Axum handlers and the Dioxus/Ratatui clients use the same struct for serialization and deserialization.

### 5. Error Types

Centralized error definitions:

- Validation errors (field-level, with messages)
- Auth errors (invalid credentials, expired token, 2FA required)
- Permission errors (module access denied, role insufficient)
- Not-found errors (per entity type)
- Business logic errors (duplicate marker hash, invalid import format)

**Why shared:** Clients need to pattern-match on error types for UI rendering. A shared `LifeHubError` enum lets every client handle errors consistently.

### 6. Business Logic (Framework-Agnostic)

This is the most valuable part. Logic that currently lives in Laravel Services/Actions but doesn't depend on HTTP or database:

- **Marker hash generation** — `MD5(url + ":" + user_id)` — used by both `Marker::getHash()` and `Marker::found()`
- **Slug generation** — from title, with uniqueness rules (currently via spatie/laravel-sluggable)
- **Module access resolution** — given a user's roles and a module key, can they read/write?
- **AI provider resolution** — given user settings, which provider + model to use? (fallback logic in `UserAiResolver`)
- **Search document projection** — how to transform a model into a searchable document (currently `SearchDocumentProjector`)
- **Text chunking for embeddings** — `TokenTextChunker` splits text by token count
- **Manifest building** — assembling module actions/commands/navigation into a manifest structure
- **Tag normalization** — tag name cleaning, dedup

**Why shared:** This logic has no reason to vary by client. The API applies it on writes; clients can apply it for previews, offline mode, or optimistic updates.

### 7. API Client SDK

Currently you have `packages/sdk-web` auto-generated from Scramble's OpenAPI output. In Rust:

- Typed request/response builders for every endpoint
- Auth token management (Sanctum equivalent — bearer tokens)
- Error handling with typed error responses
- Retry/backoff policies

**Why shared:** Every client except the API itself needs this. Dioxus web, Dioxus desktop, Dioxus mobile, and Ratatui TUI all use the same HTTP client crate.

---

## What Stays in Each Application Crate

### API Crate (`lifehub-api`, Axum)

- **Route handlers** — the Axum extractors, response builders, middleware chain
- **Database layer** — SeaORM/Diesel models, migrations, queries, connection pool
- **Auth middleware** — token validation, 2FA enforcement, rate limiting
- **Background jobs** — marker screenshot capture, AI summary generation, bulk import processing, search index sync
- **External service integrations** — AI provider HTTP clients, Meilisearch/Typesense sync, S3 uploads, Browsershot equivalent
- **Caching layer** — response caching, query caching, search term caching
- **Observers/hooks** — model lifecycle events (MarkerObserver, HomepageItemObserver, etc.)
- **Queue management** — Horizon equivalent (Tokio tasks or dedicated job runner)
- **OpenAPI generation** — Scramble equivalent (utoipa or similar)

### Web/Desktop/Mobile Crate (`lifehub-ui`, Dioxus)

- **Components** — AppShell, Sidebar, Header, forms, modals (replaces Vue components)
- **Routing** — client-side navigation (replaces Inertia)
- **State management** — reactive signals (replaces Vue reactivity)
- **Theme/appearance** — dark mode, daisyUI equivalent styling
- **Platform-specific** — desktop window management, mobile navigation patterns, PWA manifest

### TUI Crate (`lifehub-tui`, Ratatui)

- **Widgets** — dashboard view, marker list, search UI, settings panels
- **Key bindings** — vim-style or custom navigation
- **Terminal state** — screen management, alternate buffer, event loop

---

## Suggested Crate Layout

```
lifehub/
├── crates/
│   ├── lifehub-core/          # Shared library
│   │   ├── src/
│   │   │   ├── models/        # Domain structs (User, Marker, etc.)
│   │   │   ├── enums/         # MarkerStatus, ModuleKey, etc.
│   │   │   ├── dto/           # API wire types (serde)
│   │   │   ├── validation/    # Field & business rule validators
│   │   │   ├── errors/        # LifeHubError enum
│   │   │   ├── logic/         # Framework-agnostic business logic
│   │   │   └── lib.rs
│   │   └── Cargo.toml
│   │
│   ├── lifehub-client/        # API client SDK (reqwest-based)
│   │   ├── src/
│   │   │   ├── auth.rs        # Token management
│   │   │   ├── endpoints/     # Typed endpoint methods
│   │   │   └── lib.rs
│   │   └── Cargo.toml         # depends on lifehub-core
│   │
│   ├── lifehub-db/            # Database layer (SeaORM/Diesel)
│   │   ├── src/
│   │   │   ├── entities/      # ORM models
│   │   │   ├── migrations/
│   │   │   ├── queries/       # Complex query builders
│   │   │   └── lib.rs
│   │   └── Cargo.toml         # depends on lifehub-core
│   │
│   ├── lifehub-api/           # Axum API server
│   │   └── Cargo.toml         # depends on lifehub-core, lifehub-db
│   │
│   ├── lifehub-ui/            # Dioxus (web + desktop + mobile)
│   │   └── Cargo.toml         # depends on lifehub-core, lifehub-client
│   │
│   └── lifehub-tui/           # Ratatui terminal UI
│       └── Cargo.toml         # depends on lifehub-core, lifehub-client
│
└── Cargo.toml                 # Workspace root
```

### Dependency Graph

```
lifehub-core          ← no dependencies on other lifehub crates
    ↑
    ├── lifehub-client    (core + reqwest)
    ├── lifehub-db        (core + sea-orm/diesel)
    │       ↑
    │       └── lifehub-api   (core + db + axum + tokio)
    │
    ├── lifehub-ui        (core + client + dioxus)
    └── lifehub-tui       (core + client + ratatui)
```

---

## What You Gain vs. the Laravel Approach

In Laravel, the "shared library" problem was invisible because both apps are PHP and the web app consumed the API via an auto-generated SDK. The business logic lived entirely in the API, and the web app was a thin Inertia frontend.

In the Rust approach, the shared library makes explicit what was implicit:
- **Types that were duplicated** between Eloquent models, API Resources, and the SDK client → one struct in `lifehub-core`
- **Validation that lived only server-side** (FormRequests) → shared, so clients get instant feedback
- **Business logic buried in Services** → extracted into pure functions that any crate can call
- **Enums scattered across modules** → one source of truth with `serde` serialization

The tradeoff: you need to be disciplined about what goes in `lifehub-core`. It should have **zero** framework dependencies (no Axum, no Dioxus, no SeaORM). Only `serde`, `chrono`, `url`, `validator`, and similar utility crates. The moment it depends on a framework, it stops being shareable.
