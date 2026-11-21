# Run HopeHaven locally with Docker

This Compose setup runs PHP 8.4 and a local reverse proxy. It connects to the existing Windows PostgreSQL server, using database name, port, username and password from the existing .env. It does not create a database, run migrations or seed records. It is for development, not public hosting.

## Start

1. Install and start Docker Desktop with Linux containers enabled.
2. Keep the existing project .env with its APP_KEY and PostgreSQL credentials. Do not replace it with .env.example, which defaults to SQLite.
3. In PowerShell:

~~~powershell
cd C:\Users\User\Herd\orphanage-management
docker compose up --build
~~~

Open http://localhost:8000. If port 8000 is already in use by Herd or another process, change the left-hand port in compose.yaml and APP_URL together (for example, 8080).

The compiled Blade-page CSS and JavaScript already in public are used directly. Continue editing files in VS Code. After editing Tailwind source, use the existing host Node environment to run npm run build:design. This setup does not start a Vite dev server. Inertia pages require their usual frontend build separately.

## Existing PostgreSQL connection

Compose overrides DB_HOST with host.docker.internal, which refers to the Windows host from Docker Desktop. Other DB settings remain in .env. PostgreSQL must accept connections from Docker's network. If a connection fails, check the PostgreSQL listener and pg_hba.conf and allow only the required database/user/client network. Do not enable trust authentication or broad public access.

Test the connection without changing records:

~~~powershell
docker compose exec app php artisan tinker --execute="dump(DB::selectOne('select current_database() as name'));"
~~~

The portal currently has simulated roles. To keep its local-only care API restriction intact, the container runs an internal loopback proxy and publishes only to 127.0.0.1. Do not change this to 0.0.0.0 or expose it on ZeroTier without implementing authentication and authorization first.

## Useful commands

~~~powershell
docker compose config --quiet
docker compose logs --tail=80 app
docker compose stop
docker compose down
~~~

Source is mounted read-only in the container. Storage is mounted from the existing storage folder so uploaded child photos stay available. PHP dependencies and bootstrap cache use Docker volumes, separate from Herd. Shared file sessions/cache use the existing storage folder. No .env credentials or private uploads are copied into the image.

After changing Composer dependencies, rebuild and refresh the dependency volume without deleting other volumes:

~~~powershell
docker compose build app
docker compose run --rm --no-deps --entrypoint composer app install --no-interaction --prefer-dist --no-scripts
~~~
