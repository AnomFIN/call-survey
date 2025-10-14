# Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Option 1: Automatic Installation (Recommended)

```bash
# Clone the repository
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey

# Run the installer
chmod +x install.sh
./install.sh

# Start the server
php -S localhost:8000 -t public
```

### Option 2: Manual Installation

```bash
# 1. Install dependencies
composer install

# 2. Configure
cp config.example.php config.php
nano config.php  # Add your Twilio credentials

# 3. Setup database
mysql -u root -p < database.sql

# 4. Start server
php -S localhost:8000 -t public
```

### Option 3: Docker

```bash
docker-compose up -d
```

## 📋 Configuration Checklist

- [ ] Copy `config.example.php` to `config.php`
- [ ] Add Twilio Account SID
- [ ] Add Twilio Auth Token
- [ ] Add Twilio Phone Number
- [ ] Configure database credentials
- [ ] Set base URL (for webhooks)
- [ ] Import database.sql
- [ ] Test with `php test.php`

## 🔧 Twilio Setup

1. Create account at [twilio.com](https://www.twilio.com)
2. Get your Account SID and Auth Token from the Console
3. Buy a phone number (supports Voice + SMS)
4. Configure webhook in Twilio Console:
   - Voice: `https://yourdomain.com/webhook/voice.php?survey_id=1`
   - SMS: `https://yourdomain.com/webhook/sms.php?survey_id=1`

## 📞 Create Your First Survey

```bash
# Create a sample survey
php create-sample-survey.php

# Or use the web interface
# Visit http://localhost:8000
# Click "Luo uusi kysely"
```

## 🧪 Testing

```bash
# Run validation tests
php test.php

# Test API
curl -X POST http://localhost:8000/api/send-survey \
  -H "Content-Type: application/json" \
  -d '{"survey_id": 1, "phone_number": "+358401234567", "channel": "call"}'
```

## 📚 Documentation

See [README.md](README.md) for full documentation.

## 🆘 Troubleshooting

**Database connection error?**
- Check credentials in `config.php`
- Verify MySQL is running
- Ensure database exists

**Twilio not working?**
- Verify credentials in `config.php`
- Check phone number format (E.164: +358401234567)
- Ensure webhook URL is publicly accessible (HTTPS required)

**Can't access dashboard?**
- Check PHP is running: `php -S localhost:8000 -t public`
- Verify port 8000 is not in use
- Check PHP error logs

## 💡 Tips

- Use ngrok for local webhook testing: `ngrok http 8000`
- Enable error reporting during development
- Check Twilio logs for webhook debugging
- Use the test script regularly: `php test.php`
