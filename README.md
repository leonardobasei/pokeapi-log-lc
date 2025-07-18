
# Logcomex

Aplicação Laravel 11 para listagem e detalhe de Pokémons, integrando com a PokeAPI e persistindo em MySQL via Docker.

## 💾 Variáveis de Ambiente

Renomeie `.env.example` para `.env` e preencha com:

```dotenv
APP_NAME=Logcomex
APP_ENV=local
APP_KEY=base64:FLMmcbCbcl6m/jgT2BdtkdzVldFxtC3PSPzLWP7g0WM=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=UTC

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=logcomex
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

POKEAPI_BASE_URL=https://pokeapi.co/api/v2/pokemon/
````

> **Outras variáveis** (`MAIL_*`, `REDIS_*`, `AWS_*`, etc.) podem permanecer com os valores padrão ou conforme necessidade de seu ambiente.


## 🐳 Docker

1. **Buildar e subir containers**
   No diretório do projeto, rode:

   ```bash
   docker-compose up --build -d
   ```

   Isso criará os serviços:

   * **`app`**: Laravel + PHP 8.3
   * **`mysql`**: MySQL 8.0


## 🛠️ Instalação e Migrations

Dentro do container `app`, execute:

```bash
# Acessar o container
docker exec -it laravel-app bash

# Instalar dependências PHP
composer install

# Gerar chave de aplicação
php artisan key:generate

# Criar tabelas no banco
php artisan migrate
```

> Se usar sessão e cache em database, não esqueça de criar as tabelas:
>
> ```bash
> php artisan session:table
> php artisan cache:table
> php artisan migrate
> ```


## 🚀 Acessando a Aplicação

Após subir o Docker e rodar as migrations, abra no navegador:

```
http://localhost:8000
```

O frontend (via Vite) estará disponível em:

```
http://localhost:5173
```

