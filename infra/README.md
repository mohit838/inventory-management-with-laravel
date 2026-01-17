# Infrastructure Configuration

This directory contains environment-specific Docker configurations for the Inventory Management application.

## Directory Structure

```
infra/
├── local/              # Local development environment
│   ├── .env.example    # Environment template for local dev
│   ├── .env.local      # Your local environment file (gitignored)
│   ├── .gitignore      # Protects sensitive local files
│   └── docker-compose.yml  # Local Docker Compose configuration
│
└── production/         # Production environment
    ├── .env.example    # Environment template for production
    ├── .env            # Production environment file (gitignored, managed by CI/CD)
    ├── .gitignore      # Protects sensitive production files
    └── docker-compose.yml  # Production Docker Compose configuration
```

---

## Local Development

### Setup

1. **Copy the environment template**:
   ```bash
   cp infra/local/.env.example infra/local/.env.local
   ```

2. **Edit `infra/local/.env.local`** with your local configuration:
   - Database credentials
   - Redis password
   - MinIO/S3 credentials
   - Port mappings

3. **Start the services**:
   ```bash
   docker compose -f infra/local/docker-compose.yml --env-file infra/local/.env.local up -d
   ```

4. **Run migrations**:
   ```bash
   docker compose -f infra/local/docker-compose.yml exec app php artisan migrate:fresh --seed
   ```

### Common Commands

- **View logs**:
  ```bash
  docker compose -f infra/local/docker-compose.yml logs -f app
  ```

- **Stop services**:
  ```bash
  docker compose -f infra/local/docker-compose.yml down
  ```

- **Execute artisan commands**:
  ```bash
  docker compose -f infra/local/docker-compose.yml exec app php artisan <command>
  ```

---

## Production Deployment

### Setup

1. **Create production environment file**:
   ```bash
   cp infra/production/.env.example infra/production/.env
   ```

2. **Edit `infra/production/.env`** with production credentials:
   - Strong database passwords
   - Production URLs
   - Production MinIO/S3 credentials
   - Set `APP_DEBUG=false`
   - Set `APP_ENV=production`

3. **Deploy via CI/CD**:
   The Jenkinsfile automatically uses `infra/production/docker-compose.yml` for deployment.

### Manual Deployment

If deploying manually:

```bash
docker compose -f infra/production/docker-compose.yml --env-file infra/production/.env up -d
```

---

## Environment Variables

### Required Variables

Both environments require these variables (see `.env.example` files for complete list):

- `APP_KEY` - Generate with `php artisan key:generate`
- `DB_*` - Database configuration
- `REDIS_*` - Redis configuration
- `MINIO_*` - Object storage configuration
- `DOCKER_*_PORT_EXTERNAL` - External port mappings

### Port Configuration

**Local Development** (default ports):
- MySQL: `1234`
- Redis: `1235`
- App: `1236`

**Production** (default ports):
- MySQL: `3306`
- Redis: `6379`
- App: `8000`

Customize these in your `.env` files using the `DOCKER_*_PORT_EXTERNAL` variables.

---

## Security Notes

- **Never commit `.env` or `.env.local` files** - They contain sensitive credentials
- **Use strong passwords** in production
- **Review `.gitignore` files** to ensure sensitive files are protected
- **Use HTTPS** in production (`APP_URL` should use `https://`)

---

## Troubleshooting

### Services won't start

1. Check if ports are already in use:
   ```bash
   sudo netstat -tulpn | grep <port>
   ```

2. View service logs:
   ```bash
   docker compose -f infra/local/docker-compose.yml logs <service-name>
   ```

### Database connection errors

1. Ensure MySQL is healthy:
   ```bash
   docker compose -f infra/local/docker-compose.yml ps
   ```

2. Verify `DB_HOST` matches the service name in docker-compose.yml (should be `mysql`)

### Permission errors

If you encounter permission errors with storage or cache:

```bash
docker compose -f infra/local/docker-compose.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

---

For more detailed information, see:
- [README.md](../README.md) - Project overview
- [DEV_GUIDE.md](../DEV_GUIDE.md) - Developer guide
