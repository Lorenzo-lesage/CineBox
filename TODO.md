# CineBox — Roadmap

Legend: `[ ]` todo · `[x]` done. Pace: ~10 h/week.

## Phase 0 — Setup & hygiene
- [x] Align `dev` with `main`
- [x] Add `CLAUDE.md`, track `TODO.md`, remove legacy tool files
- [x] Protect `main` and `dev` on GitHub
- [x] Align `server/.env.example` with MySQL / Redis / Sanctum
- [x] Fix README (ports, setup, git workflow)
- [x] Install Larastan and configure Pint

## Phase 1 — Backend consolidation
- [ ] `MediaType` and `SortOption` enums; routes rewritten with enum binding, all under `v1`
- [ ] `TmdbService`: single configured HTTP client, smart retry, `TmdbApiException` rendered as JSON
- [ ] Fix `watch/providers` append and `checkIfUpcoming` null access
- [ ] Cache raw TMDB payloads; language from config; memoized genre map
- [ ] Unified `PaginatedMediaData`; explicit `HomeFeedData`; filter out `person` results
- [ ] Thin controllers: `BuildHomeFeed` action, FormRequests, remove CRUD stubs
- [ ] Migration: `tmdb_id` as unsigned big int + `media_type`, composite unique; models use `casts()`
- [ ] Clean `bootstrap/app.php` for Sanctum SPA (CSRF on API)

## Phase 2 — Frontend consolidation
- [ ] API layer: `server-only` fetch client + browser axios client (credentials, XSRF)
- [ ] Types aligned with backend Data objects (no `any`)
- [ ] Home: `type` in URL, single SSR fetch, `useInfiniteQuery` with `initialData`, working sentinel
- [ ] Search: server page reading `searchParams` + client input island
- [ ] Fixes: `"use clent"` typos, player URL, Zustand selectors, `tmdbImage` + `next/image`,
      Link-based pagination, a11y buttons
- [ ] `loading` / `error` / `not-found` + route param validation + `generateMetadata`
- [ ] Feature-based folder structure, colocated props

## Phase 3 — Typed contract
- [ ] Generate TS types from Data classes (Spatie TypeScript Transformer)

## Phase 4 — Authentication
- [ ] Sanctum SPA config (stateful domains, session domain, CORS credentials)
- [ ] Register / login / logout with React Hook Form + Zod
- [ ] Authenticated Server Component requests (cookie forwarding)
- [ ] Route protection with `proxy.ts`
- [ ] Socialite (Google, GitHub)

## Phase 5 — Detail page & community
- [ ] Media detail page with `generateMetadata`
- [ ] Favorites with optimistic UI
- [ ] Rating 1–5 (upsert, average via events) with optimistic UI + Policies
- [ ] Rate limiting
- [ ] Decide: "Continue watching" vs "Recently viewed"

## Phase 6 — Real-time
- [ ] Reverb + Echo setup
- [ ] Comments / chat per title, input sanitization
- [ ] Real-time notifications

## Phase 7 — Polish, tests, deploy
- [ ] Framer Motion animations, WCAG pass, performance
- [ ] Pest (+ `Http::fake()`) and frontend tests
- [ ] Production build and deploy
