# 🚀 SETUP GUIDE - Call-Survey ULTRALIGHT

**Super simple setup instructions for beginners!**

---

## 📊 Visual Setup Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    SETUP PROCESS (5 STEPS)                      │
└─────────────────────────────────────────────────────────────────┘

Step 1: Get the Code          Step 2: Install           Step 3: Get Twilio
┌─────────────────┐           ┌─────────────────┐       ┌─────────────────┐
│ git clone       │    →      │ ./install.sh    │  →    │ twilio.com      │
│ Repository      │           │ (automatic!)    │       │ Free Trial      │
└─────────────────┘           └─────────────────┘       └─────────────────┘
                                                                ↓
Step 5: Open Browser    ←     Step 4: Configure         
┌─────────────────┐           ┌─────────────────┐       
│ localhost:8000  │    ←      │ Edit config.php │       
│ 🎉 Dashboard!   │           │ Add Credentials │       
└─────────────────┘           └─────────────────┘       

Total Time: ~5 minutes!
```

---

## ⚡ Fastest Way to Get Started (Recommended)

### Step 1: Download the Code
```bash
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
```

### Step 2: Run the Automatic Installer
```bash
chmod +x install.sh
./install.sh
```

The installer will guide you through everything! ✨

### Step 3: Get Your Twilio Account (Free Trial Available)

1. Go to https://www.twilio.com/try-twilio
2. Sign up for a free account
3. Get $15 free credit!
4. Note down these 3 things:
   - **Account SID** (looks like: ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)
   - **Auth Token** (looks like: your_auth_token_here)
   - **Phone Number** (buy one for free or use trial number)

### Step 4: Configure Your Twilio Credentials

Open the configuration file:
```bash
nano config.php
```

Find these lines and fill in your Twilio details:
```php
'account_sid' => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',  // ← Your Account SID
'auth_token' => 'your_auth_token_here',                 // ← Your Auth Token
'phone_number' => '+1234567890',                        // ← Your Twilio number
```

Save and exit (Ctrl+X, then Y, then Enter)

### Step 5: Start the Server
```bash
./start-dev.sh
```

### Step 6: Open Your Browser
Visit: **http://localhost:8000**

🎉 **You're done!** You should see the Call-Survey dashboard!

---

## 🐳 Even Easier: Use Docker (No PHP/MySQL Install Needed!)

If you have Docker installed:

```bash
# Step 1: Clone
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey

# Step 2: Start everything with one command!
docker-compose up -d

# Step 3: Configure Twilio (same as Step 4 above)
cp config.example.php config.php
nano config.php

# Step 4: Visit http://localhost:8000
```

That's it! Docker handles PHP, MySQL, and everything else! 🚀

---

## 📋 What You Need Before Starting

### Required Software

| Software | Version | How to Check | Installation |
|----------|---------|--------------|--------------|
| PHP | 7.4+ | `php -v` | [Install PHP](https://www.php.net/manual/en/install.php) |
| MySQL | 5.7+ | `mysql --version` | [Install MySQL](https://dev.mysql.com/downloads/mysql/) |
| Composer | Latest | `composer --version` | [Install Composer](https://getcomposer.com/download/) |
| Git | Any | `git --version` | [Install Git](https://git-scm.com/downloads) |

**OR just install Docker** and skip all of the above!

### Required Accounts

1. **Twilio Account** (Free trial available)
   - Sign up at: https://www.twilio.com/try-twilio
   - Get $15 free credit
   - No credit card required for trial

---

## 📖 Detailed Step-by-Step Instructions

### Method 1: Manual Installation (More Control)

#### 1️⃣ Install Dependencies
```bash
composer install
```

#### 2️⃣ Create Configuration File
```bash
cp config.example.php config.php
```

#### 3️⃣ Edit Configuration
```bash
nano config.php
# OR use your favorite text editor:
# code config.php (VS Code)
# vim config.php
# gedit config.php
```

Fill in:
- Twilio Account SID
- Twilio Auth Token
- Twilio Phone Number
- Database credentials (default: root / no password)

#### 4️⃣ Create Database
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE call_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schema
mysql -u root -p call_survey < database.sql
```

#### 5️⃣ Start Development Server
```bash
php -S localhost:8000 -t public
```

#### 6️⃣ Open Browser
Visit: http://localhost:8000

---

## ✅ Verify Installation

Run the test script:
```bash
php test.php
```

You should see:
```
✅ PHP version 8.x.x
✅ Composer dependencies installed
✅ Database connection successful
✅ All tables exist
```

---

## 🎯 Create Your First Survey

### Option A: Using the Sample Script
```bash
php create-sample-survey.php
```

This creates a demo survey with 3 questions!

### Option B: Using the Web Interface

1. Open http://localhost:8000
2. Click **"Luo uusi kysely"** (Create new survey)
3. Fill in:
   - Title: "Customer Satisfaction Survey"
   - Description: "Tell us about your experience"
4. Click **"Luo kysely"** (Create survey)
5. Add questions:
   - "How satisfied were you? (1-5)"
   - "Would you recommend us? (yes/no)"
   - "Any feedback? (text)"

---

## 📞 Send Your First Survey

### Via API (Terminal)
```bash
curl -X POST http://localhost:8000/api/send-survey \
  -H "Content-Type: application/json" \
  -d '{
    "survey_id": 1,
    "phone_number": "+1234567890",
    "channel": "call"
  }'
```

### What Happens:
1. Twilio makes an automated call to the phone number
2. The recipient hears questions in voice (Text-to-Speech)
3. They answer by pressing keys (1-5 for ratings, 1/2 for yes/no)
4. Answers are saved to your database
5. You can view results in the dashboard!

---

## 🆘 Troubleshooting

### Problem: "PHP not found"
**Solution:** Install PHP
```bash
# Ubuntu/Debian
sudo apt-get install php php-mysql php-mbstring

# macOS
brew install php

# Windows
# Download from https://windows.php.net/download/
```

### Problem: "MySQL connection failed"
**Solution:** 
1. Check MySQL is running: `sudo systemctl status mysql`
2. Start MySQL: `sudo systemctl start mysql`
3. Verify credentials in `config.php`

### Problem: "Composer not found"
**Solution:** Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Problem: "Port 8000 already in use"
**Solution:** Use a different port
```bash
php -S localhost:8080 -t public
# Then visit http://localhost:8080
```

### Problem: "Twilio API error"
**Solution:**
1. Double-check Account SID in config.php
2. Double-check Auth Token in config.php
3. Verify phone number is in E.164 format: `+1234567890`
4. Check Twilio console for error logs

---

## 🎓 Next Steps

After setup, check out:

1. **[QUICKSTART.md](QUICKSTART.md)** - Quick reference guide
2. **[README.md](README.md)** - Full documentation (Finnish)
3. **[API.md](API.md)** - API reference for developers
4. **[DEPLOYMENT.md](DEPLOYMENT.md)** - Deploy to production

---

## 💡 Pro Tips

### For Development
```bash
# Use ngrok to test webhooks locally
ngrok http 8000
# Copy the HTTPS URL (e.g., https://abc123.ngrok.io)
# Use it in config.php as base_url
```

### For Production
- Use Apache or Nginx (not PHP built-in server)
- Enable HTTPS/SSL (required by Twilio)
- Follow [DEPLOYMENT.md](DEPLOYMENT.md) guide

### For Testing
```bash
# Run validation regularly
php test.php

# Create sample data
php create-sample-survey.php

# Check PHP errors
tail -f /var/log/php/error.log
```

---

## 📞 Need Help?

1. **Check documentation:**
   - [QUICKSTART.md](QUICKSTART.md)
   - [README.md](README.md)
   - [API.md](API.md)

2. **Common issues:**
   - See "Troubleshooting" section above
   - Check `php test.php` output

3. **Still stuck?**
   - Create an issue on GitHub
   - Check Twilio documentation
   - Review PHP error logs

---

## 🎉 Success Checklist

- [ ] PHP, MySQL, Composer installed (or Docker running)
- [ ] Twilio account created and credentials obtained
- [ ] Code cloned from GitHub
- [ ] `config.php` created and filled with credentials
- [ ] Database created and schema imported
- [ ] `php test.php` shows all green checkmarks ✅
- [ ] Server running on http://localhost:8000
- [ ] Dashboard loads in browser
- [ ] First survey created
- [ ] Test survey sent successfully

**All checked?** Congratulations! 🎊 You're ready to start surveying!

---

**Questions? Problems? Found this helpful?**
⭐ Star the repo on GitHub: https://github.com/AnomFIN/call-survey
