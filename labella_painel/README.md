### Passo a passo

```sh
cd labella_painel
```

Construa e suba os containers do projeto

```sh
docker compose build
docker compose up -d
```

> **Nota:** O `build` garante que a imagem inclua a extensão PHP `ext-zip`, necessária para o Filament/Composer.

Crie o Arquivo .env

```sh
cp .env.example .env
```

Acesse o container app

```sh
docker compose exec -u yourusername app bash
```

Instale as dependências do projeto

```sh
composer install
```

Gere a key do projeto Laravel

```sh
php artisan key:generate
```

Rodar as migrations

```sh
php artisan migrate
```

Acesse o projeto

| URL | Descrição |
|-----|-----------|
| [http://localhost:8000](http://localhost:8000) | Painel Laravel |
| [http://localhost:8000/admin](http://localhost:8000/admin) | Painel Filament (admin) |
| [http://localhost:3000](http://localhost:3000) | Site da loja (labella_site) |

---

### Solução de problemas

**Erro: `ext-zip * -> it is missing from your system`**

A extensão zip não está instalada no container. Reconstrua a imagem:

```sh
docker compose build --no-cache app
docker compose up -d
docker compose exec -u yourusername app composer install
```

**Permission denied em storage/framework/views**

Reconstrua a imagem (o entrypoint ajusta as permissões automaticamente):

```sh
docker compose build --no-cache app
docker compose up -d
```

Ou ajuste manualmente: `docker compose exec app chown -R www-data:www-data storage bootstrap/cache`

**502 Bad Gateway**

O PHP-FPM não está respondendo. Verifique:

```sh
# Status dos containers
docker compose ps

# Logs do app (PHP-FPM)
docker compose logs app

# Logs do nginx
docker compose logs nginx
```

Se o container `app` estiver parado ou reiniciando:
1. Confirme que `composer install` foi executado com sucesso (pasta `vendor` existe)
2. Reconstrua: `docker compose build --no-cache app && docker compose up -d`
