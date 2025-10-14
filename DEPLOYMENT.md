# Production Deployment Guide

## Prerequisites

- Apache or Nginx web server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- SSL certificate (required for Twilio webhooks)
- Domain name

## Apache Setup

### 1. Install Required Packages

```bash
sudo apt update
sudo apt install apache2 php php-mysql php-mbstring php-xml composer git
```

### 2. Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
```

### 3. Install Dependencies

```bash
sudo composer install --no-dev --optimize-autoloader
```

### 4. Configure Application

```bash
sudo cp config.example.php config.php
sudo nano config.php
```

Update these values:
- Twilio credentials
- Database credentials
- Base URL (with HTTPS)

### 5. Setup Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE call_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'callsurvey'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON call_survey.* TO 'callsurvey'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
mysql -u callsurvey -p call_survey < database.sql
```

### 6. Configure Apache Virtual Host

Create `/etc/apache2/sites-available/call-survey.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAdmin admin@yourdomain.com
    DocumentRoot /var/www/call-survey/public

    <Directory /var/www/call-survey/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/call-survey-error.log
    CustomLog ${APACHE_LOG_DIR}/call-survey-access.log combined

    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAdmin admin@yourdomain.com
    DocumentRoot /var/www/call-survey/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem

    <Directory /var/www/call-survey/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/call-survey-error.log
    CustomLog ${APACHE_LOG_DIR}/call-survey-access.log combined
</VirtualHost>
```

### 7. Setup SSL with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

### 8. Enable Site

```bash
sudo a2enmod rewrite ssl
sudo a2ensite call-survey
sudo systemctl restart apache2
```

### 9. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/call-survey
sudo chmod -R 755 /var/www/call-survey
sudo chmod 600 /var/www/call-survey/config.php
```

### 10. Configure Twilio Webhooks

In Twilio Console, set webhook URLs:
- Voice: `https://yourdomain.com/webhook/voice.php?survey_id=1`
- SMS: `https://yourdomain.com/webhook/sms.php?survey_id=1`

## Nginx Setup (Alternative)

Create `/etc/nginx/sites-available/call-survey`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com;

    root /var/www/call-survey/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable and restart:

```bash
sudo ln -s /etc/nginx/sites-available/call-survey /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Security Checklist

- [ ] SSL/HTTPS enabled
- [ ] `config.php` has restricted permissions (600)
- [ ] Database user has limited privileges
- [ ] PHP `display_errors` is off in production
- [ ] Strong database passwords
- [ ] Firewall configured (UFW or iptables)
- [ ] Regular backups scheduled
- [ ] Keep dependencies updated

## Monitoring

### Setup Log Rotation

Create `/etc/logrotate.d/call-survey`:

```
/var/www/call-survey/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### Monitor Application

```bash
# Check Apache logs
sudo tail -f /var/log/apache2/call-survey-error.log

# Check PHP errors
sudo tail -f /var/log/php8.1-fpm.log

# Monitor database
sudo mysql -u root -p -e "SHOW PROCESSLIST;"
```

## Backup

```bash
# Backup database
mysqldump -u callsurvey -p call_survey > backup-$(date +%Y%m%d).sql

# Backup files
tar -czf call-survey-backup-$(date +%Y%m%d).tar.gz /var/www/call-survey
```

## Update Application

```bash
cd /var/www/call-survey
sudo git pull
sudo composer install --no-dev --optimize-autoloader
sudo systemctl reload apache2
```

## Troubleshooting

### Check PHP Version
```bash
php -v
```

### Test Database Connection
```bash
cd /var/www/call-survey
php test.php
```

### Check Apache Status
```bash
sudo systemctl status apache2
sudo apache2ctl configtest
```

### View Logs
```bash
sudo tail -f /var/log/apache2/call-survey-error.log
```
