# Developer Guide - Inventory Management API

This guide provides essential commands and information for developing, testing, and managing the Inventory Management API.

---

## 🐳 Docker Setup (Recommended)

### Quick Start

```bash
# 1. Ensure MySQL and Redis are running on your local machine
sudo systemctl status mysql
sudo systemctl status redis

# 2. Build Docker image
docker build -t inventory-management:latest .

# 3. Start container
docker compose --env-file .env up -d

# 4. Check logs
docker logs inventory_app -f

# 5. Verify health
docker ps
curl http://localhost:4002/api/health
```

### Environment Configuration

Your `.env` file should have these Docker-specific settings:

```bash
# Docker Configuration
DOCKER_APP_PORT_EXTERNAL=4002
DOCKER_APP_PORT_INTERNAL=4002
DOCKER_MYSQL_PORT_EXTERNAL=3306
DOCKER_REDIS_PORT_EXTERNAL=6379

# Database (use host.docker.internal for Docker)
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=inv_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis (use host.docker.internal for Docker)
REDIS_HOST=host.docker.internal
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password
```

> **Note**: Use `host.docker.internal` when running in Docker, or `127.0.0.1` for local non-Docker development.

---

## 🔧 Troubleshooting Docker Issues

### Issue: Container Can't Connect to MySQL

**Symptoms**:

```
MySQL at host.docker.internal:3306 not ready... waiting 2s
```

**Debug Steps**:

```bash
# 1. Check if MySQL is running
sudo systemctl status mysql
netstat -tlnp | grep 3306

# 2. Verify MySQL is listening on all interfaces (not just localhost)
sudo grep bind-address /etc/mysql/mysql.conf.d/mysqld.cnf
# Should show: bind-address = 0.0.0.0

# 3. Test connection from container
docker exec inventory_app ping -c 3 host.docker.internal
docker exec inventory_app nc -zv host.docker.internal 3306

# 4. Check container logs
docker logs inventory_app --tail 100

# 5. Verify environment variables
docker exec inventory_app env | grep DB_
```

**Solutions**:

1. **MySQL not listening on 0.0.0.0**:

    ```bash
    # Edit MySQL config
    sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
    # Change: bind-address = 0.0.0.0
    sudo systemctl restart mysql
    ```

2. **Firewall blocking connection**:

    ```bash
    sudo ufw allow 3306
    ```

3. **Wrong credentials**:
    ```bash
    # Test credentials
    mysql -h 127.0.0.1 -P 3306 -u root -p
    ```

---

### Issue: Container Can't Connect to Redis

**Symptoms**:

```
Redis at host.docker.internal:6379 not ready... waiting 2s
```

**Debug Steps**:

```bash
# 1. Check if Redis is running
sudo systemctl status redis
netstat -tlnp | grep 6379

# 2. Test Redis connection
redis-cli -h 127.0.0.1 -p 6379 ping

# 3. Test from container
docker exec inventory_app redis-cli -h host.docker.internal -p 6379 ping

# 4. Check Redis configuration
sudo grep bind /etc/redis/redis.conf
# Should include: bind 0.0.0.0 ::1

# 5. Check if password is required
redis-cli -h 127.0.0.1 -p 6379 INFO | grep requirepass
```

**Solutions**:

1. **Redis not listening on all interfaces**:

    ```bash
    sudo nano /etc/redis/redis.conf
    # Change: bind 0.0.0.0 ::1
    sudo systemctl restart redis
    ```

2. **Password mismatch**:
    ```bash
    # Check Redis password in config
    sudo grep requirepass /etc/redis/redis.conf
    # Update .env REDIS_PASSWORD to match
    ```

---

### Issue: Storage Directory Errors

**Symptoms**:

```
file_put_contents(/var/www/html/storage/...): Failed to open stream: No such file or directory
```

**Solution**:
This is automatically fixed by the `docker-entrypoint.sh` script. If you still see this error:

```bash
# Rebuild the image to get the latest entrypoint
docker compose down
docker build -t inventory-management:latest .
docker compose --env-file .env up -d
```

---

### Issue: Container Exits Immediately

**Debug Steps**:

```bash
# 1. View full logs
docker logs inventory_app

# 2. Check container status
docker ps -a --filter "name=inventory_app"

# 3. Run container interactively for debugging
docker run -it --rm \
  --env-file .env \
  --add-host=host.docker.internal:host-gateway \
  inventory-management:latest \
  /bin/bash

# Inside container, test manually:
nc -zv host.docker.internal 3306
nc -zv host.docker.internal 6379
```

---

### Issue: Health Check Failing

**Debug Steps**:

```bash
# 1. Check health status
docker inspect inventory_app --format='{{.State.Health.Status}}'

# 2. View health check logs
docker inspect inventory_app --format='{{range .State.Health.Log}}{{.Output}}{{end}}'

# 3. Test health endpoint manually
curl -v http://localhost:4002/api/health

# 4. Check if app is running inside container
docker exec inventory_app ps aux | grep php
```

---

## 📋 Useful Docker Commands

```bash
# View logs (follow mode)
docker logs inventory_app -f

# View last 100 lines
docker logs inventory_app --tail 100

# Restart container
docker compose --env-file .env restart

# Stop and remove container
docker compose --env-file .env down

# Rebuild image and restart
docker build -t inventory-management:latest . && \
docker compose --env-file .env up -d --force-recreate

# Execute command inside container
docker exec inventory_app php artisan migrate
docker exec inventory_app php artisan cache:clear

# Access container shell
docker exec -it inventory_app /bin/bash

# Check container resource usage
docker stats inventory_app

# Inspect container configuration
docker inspect inventory_app

# View container networks
docker network ls
docker network inspect inventory_network
```

---

## 🔐 API Documentation (Swagger)

The API is fully documented using Swagger (OpenAPI 3.0).

- **Documentation URL**: `http://localhost:4002/api/documentation` (Docker) or `http://localhost:8000/api/documentation` (local)
- **Regenerate Documentation**: Run this whenever you change controller attributes.
    ```bash
    php artisan l5-swagger:generate
    ```

### How to Authenticate in Swagger:

1.  **Login**: Use the `POST /api/v1/login` endpoint to get an `access_token`.
2.  **Authorize**: Click the **"Authorize"** button at the top of the Swagger UI.
3.  **Token**: Paste the token string (e.g., `eyJhbGci...`) into the value field and click **Authorize**.
4.  **Lock Icons**: Protected routes will show a closed lock icon, indicating your token is active.

---

## 🧪 Test User Credentials

The database seeder creates default users for testing different RBAC permissions.

| Role           | Email                 | Password   | Permissions                                                 |
| :------------- | :-------------------- | :--------- | :---------------------------------------------------------- |
| **Superadmin** | `superadmin@test.com` | `password` | Total Control (All permissions + Users)                     |
| **Admin**      | `admin@test.com`      | `password` | High Level Access (All except Users Management)             |
| **User**       | `user@test.com`       | `password` | Restricted Access (View Categories/Products, Create Orders) |

---

## 🛠️ Essential Commands

### Database Management

- **Fresh Install (Migrations + Seeds)**:
    ```bash
    php artisan migrate:fresh --seed
    ```
- **Reset Permissions only**:
    ```bash
    php artisan db:seed --class=PermissionSeeder
    ```

### Cache & Optimization

- **Clear All Caches (Recommended after changes)**:
    ```bash
    php artisan optimize:clear
    ```
- **Rebuild Caches**:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    ```

---

## 🔍 Debugging with Tinker

Tinker is essential for testing services and repository logic directly.

- **Check Service Status (e.g., MinIO)**:

    ```php
    // Verify MinioService instantiation and public link generation
    $minio = app(App\Services\MinioService::class);
    echo Storage::disk('minio')->url('test.png');
    ```

- **Query Database Models**:

    ```php
    // Check superadmin user
    App\Models\User::role('superadmin')->first();
    ```

- **Clear Cache Manually**:

    ```php
    Cache::flush();
    ```

- **Run Commands via Tinker**:
    ```bash
    php artisan tinker --execute="echo App\Models\Product::count();"
    ```

---

## 📂 Project Architecture Notes

- **JWT Authentication**: Managed by `JwtMiddleware`. Tokens are valid for 15 minutes.
- **RBAC**: Implemented using `spatie/laravel-permission`. Permissions are checked via middleware in `routes/api.php`.
- **Media**: Image uploads are handled by `MinioService` (S3 compatible).

---

## Project Tree

- `Linux / macOS`

```bash
tree -I "node_modules|dist|.git|.vscode|logs|tmp|coverage|vendor" > project-tree.txt
```

## Create a shared network and connect existing containers

```bash
docker network create inventory-net || true
docker network connect inventory-net inventory_mysql || true
docker network connect inventory-net inventory_redis || true
```

## Build Img Only

```bash
docker build -t mohit838/img:latest .
docker push mohit838/img:latest
```
