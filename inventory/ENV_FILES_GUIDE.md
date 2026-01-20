# Environment Files Guide

This document explains the environment file structure and usage for different deployment scenarios.

---

## File Structure

```
.env          → Local development with Docker (NOT committed to git)
.env.local    → Production deployment with Docker (NOT committed to git)
.env.example  → Template with placeholders (committed to git)
```

---

## Configuration Strategy

### Consistent Approach

Both local and production use the **same Docker setup**:
- App runs in Docker container
- MySQL and Redis run on host machine (outside container)
- Container connects via `host.docker.internal`

**The only difference**: Credentials and port numbers

---

## `.env` - Local Development

**Purpose**: Local development with Docker

**Key Settings**:
```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:4002

# Docker ports
DOCKER_APP_PORT_EXTERNAL=4002
DOCKER_APP_PORT_INTERNAL=4002
DOCKER_MYSQL_PORT_EXTERNAL=3306
DOCKER_REDIS_PORT_EXTERNAL=6379

# Database (connects to local MySQL via host.docker.internal)
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=inv_db
DB_USERNAME=root
DB_PASSWORD=your_local_password

# Redis (connects to local Redis via host.docker.internal)
REDIS_HOST=host.docker.internal
REDIS_PORT=6379
REDIS_PASSWORD=your_local_redis_password
```

**Usage**:
```bash
docker build -t inventory-management:latest .
docker compose --env-file .env up -d
```

---

## `.env.local` - Production

**Purpose**: Production deployment with Docker

**Key Settings**:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-production-url

# Docker ports (different from local to avoid conflicts)
DOCKER_APP_PORT_EXTERNAL=1236
DOCKER_APP_PORT_INTERNAL=4002
DOCKER_MYSQL_PORT_EXTERNAL=1234
DOCKER_REDIS_PORT_EXTERNAL=1235

# Database (connects to production MySQL via host.docker.internal)
DB_HOST=host.docker.internal
DB_PORT=1234
DB_DATABASE=inv_db
DB_USERNAME=inv_user
DB_PASSWORD=strong_production_password

# Redis (connects to production Redis via host.docker.internal)
REDIS_HOST=host.docker.internal
REDIS_PORT=1235
REDIS_PASSWORD=strong_production_redis_password
```

**Usage**:
```bash
docker build -t inventory-management:latest .
docker compose --env-file .env.local up -d
```

---

## `.env.example` - Template

**Purpose**: Template for creating new environment files

**Contains**:
- All required variables with placeholders
- Comprehensive comments explaining each setting
- Default values where appropriate
- Instructions for Docker vs non-Docker usage

**Usage**:
```bash
# For local development
cp .env.example .env
# Edit .env with your local credentials

# For production
cp .env.example .env.local
# Edit .env.local with production credentials
```

---

## Important Notes

### 1. `host.docker.internal` Explained

When the app runs in Docker, it can't use `127.0.0.1` to connect to services on the host machine. Instead, it uses `host.docker.internal`, which Docker automatically resolves to the host's IP address.

**In Docker**: Use `host.docker.internal`
**Without Docker**: Use `127.0.0.1`

### 2. Port Configuration

**Local Development**:
- App: 4002
- MySQL: 3306 (standard)
- Redis: 6379 (standard)

**Production**:
- App: 1236 (custom)
- MySQL: 1234 (custom, to avoid conflicts)
- Redis: 1235 (custom, to avoid conflicts)

### 3. Security

- Never commit `.env` or `.env.local` to git
- Use strong passwords in production
- Keep `APP_DEBUG=false` in production
- Use HTTPS in production (`APP_URL=https://...`)

---

## Quick Reference

| Scenario | File | Command |
|----------|------|---------|
| Local Dev (Docker) | `.env` | `docker compose --env-file .env up -d` |
| Production (Docker) | `.env.local` | `docker compose --env-file .env.local up -d` |
| Local Dev (No Docker) | `.env` | `php artisan serve` |
| New Setup | `.env.example` | `cp .env.example .env` |

---

## Troubleshooting

### Can't connect to MySQL/Redis

1. Verify services are running:
   ```bash
   sudo systemctl status mysql
   sudo systemctl status redis
   ```

2. Check they're listening on all interfaces:
   ```bash
   netstat -tlnp | grep 3306
   netstat -tlnp | grep 6379
   ```

3. Test from container:
   ```bash
   docker exec inventory_app nc -zv host.docker.internal 3306
   docker exec inventory_app nc -zv host.docker.internal 6379
   ```

### Wrong environment file loaded

Always specify the env file explicitly:
```bash
# Correct
docker compose --env-file .env up -d

# Wrong (uses .env by default)
docker compose up -d
```

---

## Migration Guide

### From Non-Docker to Docker

1. Update `.env`:
   ```bash
   # Change from:
   DB_HOST=127.0.0.1
   REDIS_HOST=127.0.0.1
   
   # To:
   DB_HOST=host.docker.internal
   REDIS_HOST=host.docker.internal
   ```

2. Add Docker variables:
   ```bash
   DOCKER_APP_PORT_EXTERNAL=4002
   DOCKER_APP_PORT_INTERNAL=4002
   DOCKER_MYSQL_PORT_EXTERNAL=3306
   DOCKER_REDIS_PORT_EXTERNAL=6379
   ```

3. Build and run:
   ```bash
   docker build -t inventory-management:latest .
   docker compose --env-file .env up -d
   ```

### From Docker back to Non-Docker

1. Update `.env`:
   ```bash
   # Change from:
   DB_HOST=host.docker.internal
   REDIS_HOST=host.docker.internal
   
   # To:
   DB_HOST=127.0.0.1
   REDIS_HOST=127.0.0.1
   ```

2. Run locally:
   ```bash
   php artisan serve
   ```
