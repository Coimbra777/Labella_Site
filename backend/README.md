# Backend (LaBella)

Laravel, Filament, API pública `/api/v1`, fila de notificações.

## Requisitos

Docker e Docker Compose.

## Subir o ambiente

```bash
cp .env.example .env
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

| Serviço        | URL / porta        |
|----------------|--------------------|
| App (Nginx)    | http://localhost:8000 |
| Filament      | http://localhost:8000/admin |
| phpMyAdmin    | http://localhost:8080 |
| MySQL (host)  | localhost:3300     |

Credenciais padrão do `.env.example`: alinhar `DB_*` com o serviço `db` do compose (`DB_HOST=db` dentro do container; use `localhost:3300` a partir da máquina host com o mesmo usuário/senha).

Na primeira vez, crie um administrador (Filament):

```bash
docker compose exec app php artisan labella:make-super-admin
```

## Comandos úteis

```bash
docker compose exec app bash
php artisan test
composer phpstan       # Larastan; fora do Docker use --memory-limit se o PHP estiver em 128M
php artisan queue:work   # já roda no serviço `queue`
```

Se testes falharem com erro do guard **Sanctum**, rode: `php artisan package:discover`.

## Problemas comuns

**502 / app não sobe:** `docker compose logs app` e `docker compose logs nginx`.

**Permissões em `storage`:** `docker compose exec app chown -R www-data:www-data storage bootstrap/cache`

**Fila de e-mail não dispara:** confira `QUEUE_CONNECTION` no `.env` e se o container `queue` está em execução.
