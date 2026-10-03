# CineBox

Full-stack movie & TV platform with community ratings and real-time chat.
A Laravel REST API serves data from [TMDB](https://www.themoviedb.org/) to a Next.js frontend.

> **Status:** work in progress — see [`TODO.md`](TODO.md) for the roadmap.

## Tech Stack

| Layer    | Technologies |
|----------|--------------|
| Backend  | Laravel 12, PHP 8.5, Spatie Laravel Data, Sanctum (SPA auth), Reverb (WebSocket) |
| Frontend | Next.js 16 (App Router, React Compiler), React 19, TypeScript, Tailwind CSS v4, shadcn/ui, TanStack Query v5, Zustand |
| Data     | MySQL 8.4, Redis 8 (cache, sessions, queues) |
| Infra    | Docker Compose |

## Architecture

```
Browser ──► web (Next.js) ──► server (Laravel API) ──► TMDB API
   │                              ├──► database (MySQL)
   └──── client-side requests ───►├──► redis
                                  └──► reverb (WebSocket)
```

| Service    | URL / Port                 |
|------------|----------------------------|
| `web`      | http://localhost:3000      |
| `server`   | http://localhost:8000/api/v1 |
| `database` | localhost:3308             |
| `redis`    | localhost:6379             |
| `reverb`   | localhost:8080             |

## Getting Started

### Prerequisites
- Docker with Docker Compose
- A TMDB **API Read Access Token** ([get one here](https://www.themoviedb.org/settings/api))

### Setup

```bash
# 1. Clone the repository
git clone git@github.com:Lorenzo-lesage/CIneBox.git cinebox
cd cinebox

# 2. Create the environment files, then set TMDB_TOKEN in server/.env
cp server/.env.example server/.env
cp web/.env.example web/.env.local

# 3. Build and start all services
docker compose up -d --build

# 4. Install PHP dependencies, generate the app key and run migrations
docker compose exec server composer install
docker compose exec server php artisan key:generate
docker compose exec server php artisan migrate
```

Open http://localhost:3000.

## Useful Commands

```bash
docker compose ps                      # services status
docker compose logs -f <service>       # follow logs (server, web, reverb, ...)
docker compose exec server sh          # shell inside the Laravel container
docker compose exec web sh             # shell inside the Next.js container
docker compose exec server php artisan route:list --path=api
docker compose down                    # stop all services
```

## Project Structure

```
cinebox/
├── server/              # Laravel API
├── web/                 # Next.js frontend
├── logo/                # brand assets
├── docker-compose.yml
├── CLAUDE.md            # collaboration, coding and git conventions
└── TODO.md              # roadmap
```

## Contributing

Branching model, Conventional Commits and coding conventions are documented in
[`CLAUDE.md`](CLAUDE.md#git-workflow).

## Troubleshooting

**Permission errors on `storage/` or `bootstrap/cache/`** (bind-mounted volume):

```bash
cd server
sudo chown -R "$USER":"$USER" .
sudo chmod -R 775 storage bootstrap/cache
```