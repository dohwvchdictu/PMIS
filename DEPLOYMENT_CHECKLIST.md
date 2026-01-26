# Security & Deployment Checklist

## Pre-Deployment Security Checklist

### ✅ Environment Configuration

- [ ] Copy `.env.production.example` to `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`: `php artisan key:generate`
- [ ] Generate secure `JWT_SECRET` (64 characters): `openssl rand -base64 64`
- [ ] Update all `<CHANGE_ME>` values in `.env`
- [ ] Remove `.env.example` from production server
- [ ] Ensure `.env` is in `.gitignore`

### ✅ Database Security

- [ ] Use strong database credentials
- [ ] Create dedicated database user with minimal privileges
- [ ] Enable SSL for database connections
- [ ] Configure database backups
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed necessary data: `php artisan db:seed --force`

### ✅ Application Security

- [ ] Remove all debug code (`dd()`, `dump()`, `var_dump()`)
- [ ] Verify JWT middleware is properly configured
- [ ] Test authentication and authorization
- [ ] Enable CSRF protection (already enabled)
- [ ] Set `SESSION_SECURE_COOKIE=true`
- [ ] Set `SESSION_SAME_SITE=strict`
- [ ] Configure rate limiting
- [ ] Review all API endpoints for proper authentication

### ✅ File Permissions

```bash
# Set proper ownership
chown -R www-data:www-data /path/to/bacpmis

# Set directory permissions
find /path/to/bacpmis -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/bacpmis -type f -exec chmod 644 {} \;

# Storage and cache need write permissions
chmod -R 775 /path/to/bacpmis/storage
chmod -R 775 /path/to/bacpmis/bootstrap/cache
```

### ✅ Caching & Optimization

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Build frontend assets
npm run build
```

### ✅ Web Server Configuration

#### Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name bacpmis.doh.gov.ph;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name bacpmis.doh.gov.ph;
    root /var/www/bacpmis/public;

    index index.php;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Hide server version
    server_tokens off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### ✅ SSL/TLS Configuration

- [ ] Install SSL certificate
- [ ] Configure HTTPS redirect
- [ ] Test SSL configuration: https://www.ssllabs.com/ssltest/
- [ ] Enable HTTP/2
- [ ] Configure HSTS header

### ✅ Monitoring & Logging

- [ ] Configure Laravel log rotation
- [ ] Set up error tracking (Sentry, Bugsnag, etc.)
- [ ] Configure server monitoring
- [ ] Set up uptime monitoring
- [ ] Configure database query logging (development only)
- [ ] Review and configure audit logs

### ✅ Backup Strategy

- [ ] Configure automated database backups
- [ ] Configure file storage backups
- [ ] Test backup restoration process
- [ ] Document backup retention policy
- [ ] Store backups in secure location

### ✅ Testing Before Go-Live

- [ ] Test login/logout functionality
- [ ] Test user permissions
- [ ] Test procurement creation workflow
- [ ] Test mode of procurement updates
- [ ] Test schedule management
- [ ] Test audit trail functionality
- [ ] Test file uploads
- [ ] Verify email notifications
- [ ] Test under load
- [ ] Perform security scan

### ✅ Post-Deployment

- [ ] Monitor error logs for 24 hours
- [ ] Verify all scheduled tasks are running
- [ ] Test backup process
- [ ] Update documentation
- [ ] Train users on new features
- [ ] Set up on-call rotation

## Quick Commands Reference

### Deployment Commands

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear and optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Rollback Commands

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restore from backup
# (specific commands depend on your backup solution)
```

### Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --secret="your-secret-token"
# Access via: https://bacpmis.doh.gov.ph/your-secret-token

# Disable maintenance mode
php artisan up
```

## Environment-Specific Notes

### Development

- `APP_DEBUG=true`
- `LOG_LEVEL=debug`
- Use SQLite for local development (optional)
- Enable Laravel Telescope

### Staging

- `APP_DEBUG=false`
- `LOG_LEVEL=info`
- Use production-like database
- Test deployment scripts

### Production

- `APP_DEBUG=false`
- `LOG_LEVEL=error`
- All security measures enabled
- Monitoring and backups configured

## Security Contacts

- **Security Issues**: Report to security@doh.gov.ph
- **Emergency Contact**: [Add emergency contact]
- **On-Call Rotation**: [Add rotation schedule]

## Additional Resources

- Laravel Security Best Practices: https://laravel.com/docs/10.x/deployment
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP Security Checklist: https://www.php.net/manual/en/security.php
