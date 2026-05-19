# LaBella

Monorepo com vitrine React e API Laravel (Filament).

## Estrutura

- **`backend/`** — Laravel, Filament, API REST, filas, MySQL, Redis (via Docker).
- **`frontend/`** — React (Vite + TanStack).

## Rodar localmente

### Backend

```bash
cd backend
cp .env.example .env
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

- API e painel: **http://localhost:8000** — Filament: **http://localhost:8000/admin**
- phpMyAdmin: **http://localhost:8080**
- MySQL no host: **localhost:3300** (usuário/senha conforme `.env`)

### Frontend

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

Ajuste `VITE_API_BASE_URL` no `.env` do frontend (padrão: `http://localhost:8000`).
