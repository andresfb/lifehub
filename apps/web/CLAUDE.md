# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```sh
npm run dev          # Start dev server with HMR
npm run build        # Type-check + build for production
npm run build-only   # Build without type-checking
npm run type-check   # Run vue-tsc type checking
npm run lint         # Run oxlint then eslint (both with --fix)
npm run format       # Format src/ with Prettier
```

There are no tests configured yet.

## Architecture

This is a Vue 3 SPA using Vite, Vue Router, and Pinia. It is the frontend app in a monorepo (`lifehub`) that is being split into separate API and web applications (see the `split-api-web` branch).

**Entry point:** `src/main.ts` — creates the Vue app, registers Pinia and the router, mounts to `#app`.

**Routing:** `src/router/index.ts` — uses `createWebHistory`. Routes live here; non-home routes use lazy imports for code splitting.

**State:** `src/stores/` — Pinia stores using the Composition API style (`defineStore` with a setup function returning refs/computeds).

**Path alias:** `@` resolves to `src/`.

## Linting & Formatting

Two-stage linting: oxlint runs first (fast Rust linter, configured in `.oxlintrc.json`), then eslint picks up Vue/TS-specific rules. ESLint is configured to skip formatting rules (prettier handles those). Prettier config: no semicolons, single quotes, 100-char print width.


## Code Exploration Policy

Always use jCodemunch-MCP tools for code navigation. Never fall back to Read, Grep, Glob, or Bash for code exploration.
**Exception:** Use `Read` when you need to edit a file — the agent harness requires a `Read` before `Edit`/`Write` will succeed. Use jCodemunch tools to *find and understand* code, then `Read` only the specific file you're about to modify.

**Start any session:**
1. `resolve_repo { "path": "." }` — confirm the project is indexed. If not: `index_folder { "path": "." }`
2. `suggest_queries` — when the repo is unfamiliar

**Finding code:**
- symbol by name → `search_symbols` (add `kind=`, `language=`, `file_pattern=`, `decorator=` to narrow)
- decorator-aware queries → `search_symbols(decorator="X")` to find symbols with a specific decorator (e.g. `@property`, `@route`); combine with set-difference to find symbols *lacking* a decorator (e.g. "which endpoints lack CSRF protection?")
- string, comment, config value → `search_text` (supports regex, `context_lines`)
- database columns (dbt/SQLMesh) → `search_columns`

**Reading code:**
- before opening any file → `get_file_outline` first
- one or more symbols → `get_symbol_source` (single ID → flat object; array → batch)
- symbol + its imports → `get_context_bundle`
- specific line range only → `get_file_content` (last resort)

**Repo structure:**
- `get_repo_outline` → dirs, languages, symbol counts
- `get_file_tree` → file layout, filter with `path_prefix`

**Relationships & impact:**
- what imports this file → `find_importers`
- where is this name used → `find_references`
- is this identifier used anywhere → `check_references`
- file dependency graph → `get_dependency_graph`
- what breaks if I change X → `get_blast_radius`
- what symbols actually changed since last commit → `get_changed_symbols`
- find unreachable/dead code → `find_dead_code`
- class hierarchy → `get_class_hierarchy`

## Session-Aware Routing

**Opening move for any task:**
1. `plan_turn { "repo": "...", "query": "your task description" }` — get confidence + recommended files
2. Obey the confidence level:
   - `high` → go directly to recommended symbols, max 2 supplementary reads
   - `medium` → explore recommended files, max 5 supplementary reads
   - `low` → the feature likely doesn't exist. Report the gap to the user. Do NOT search further hoping to find it.

**Interpreting search results:**
- If `search_symbols` returns `negative_evidence` with `verdict: "no_implementation_found"`:
  - Do NOT re-search with different terms hoping to find it
  - Do NOT assume a related file (e.g. auth middleware) implements the missing feature (e.g. CSRF)
  - DO report: "No existing implementation found for X. This would need to be created."
  - DO check `related_existing` files — they show what's nearby, not what exists
- If `verdict: "low_confidence_matches"`: examine the matches critically before assuming they implement the feature

**After editing files:**
- If PostToolUse hooks are installed (Claude Code only), edited files are auto-reindexed
- Otherwise, call `register_edit` with edited file paths to invalidate caches and keep the index fresh
- For bulk edits (5+ files), always use `register_edit` with all paths to batch-invalidate

**Token efficiency:**
- If `_meta` contains `budget_warning`: stop exploring and work with what you have
- If `auto_compacted: true` appears: results were automatically compressed due to turn budget
- Use `get_session_context` to check what you've already read — avoid re-reading the same files

## Output Rules

Apply these rules to every response. No exceptions.

- Lead with the answer. No preamble, no restating the question.
- Use contractions. "It's" not "it is". "Don't" not "do not".
- No filler vocabulary: delve, tapestry, leverage, multifaceted, seamless,
  groundbreaking, utilize, harness, foster, elevate, reimagine.
- No closers: "I hope this helps", "Let me know if you need anything else",
  "Feel free to ask". Just stop when done.
- No openers: "Great question!", "That's interesting!", "Absolutely!".
  Start with substance.
- One qualifier per claim maximum. No hedge-stacking.
- Short sentences. If it has three commas, split it.
- Do not narrate what you are about to do. Do it.
- Do not summarize what you just did. The diff is visible.
- Do not re-quote file contents from tool results. Reference by line number.
- Return JSON tool results with no indentation. Dense format only.
- Do not echo back parameters the user already passed.
- Omit empty fields, null values, and derived counts from structured output.
