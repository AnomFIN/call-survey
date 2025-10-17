# ✅ Getting Started Checklist

Use this checklist to track your setup progress!

---

## 📋 Pre-Installation Checklist

Before you begin, make sure you have:

- [ ] **Computer with internet connection**
- [ ] **Basic command line knowledge** (or willingness to learn!)
- [ ] **Text editor** (nano, vim, VS Code, or any text editor)

**Choose your installation method:**

- [ ] **Option A: Traditional Install** (requires PHP, MySQL, Composer)
- [ ] **Option B: Docker Install** (only requires Docker - easiest!)

---

## 🔧 Option A: Traditional Installation Checklist

### Step 1: Install Required Software

- [ ] **PHP 7.4 or higher installed**
  ```bash
  php -v  # Should show version 7.4+
  ```
  
- [ ] **MySQL 5.7 or higher installed**
  ```bash
  mysql --version  # Should show version 5.7+
  ```
  
- [ ] **Composer installed**
  ```bash
  composer --version  # Should show Composer version
  ```
  
- [ ] **Git installed**
  ```bash
  git --version  # Should show Git version
  ```

### Step 2: Get Twilio Account (FREE!)

- [ ] **Signed up at https://www.twilio.com/try-twilio**
- [ ] **Verified email address**
- [ ] **Got $15 free trial credit** (no credit card needed!)
- [ ] **Noted down Account SID** (starts with AC...)
- [ ] **Noted down Auth Token** (visible in console)
- [ ] **Got a phone number** (free trial number or purchased)

### Step 3: Download and Install Call-Survey

- [ ] **Cloned the repository**
  ```bash
  git clone https://github.com/AnomFIN/call-survey.git
  cd call-survey
  ```

- [ ] **Made install script executable**
  ```bash
  chmod +x install.sh
  chmod +x start-dev.sh
  ```

- [ ] **Ran the installer**
  ```bash
  ./install.sh
  ```

- [ ] **Installer completed successfully** (all green checkmarks ✅)

### Step 4: Configure Application

- [ ] **config.php created** (copied from config.example.php)

- [ ] **Twilio credentials added to config.php:**
  - [ ] Account SID filled in
  - [ ] Auth Token filled in
  - [ ] Phone Number filled in (format: +1234567890)

- [ ] **Database credentials configured:**
  - [ ] Database host (default: localhost)
  - [ ] Database name (default: call_survey)
  - [ ] Database username (default: root)
  - [ ] Database password (your MySQL password)

- [ ] **Base URL set in config.php**
  - For local: `http://localhost:8000`
  - For production: `https://yourdomain.com`

### Step 5: Setup Database

- [ ] **MySQL server is running**
  ```bash
  sudo systemctl status mysql  # or: brew services list
  ```

- [ ] **Database created**
  ```bash
  mysql -u root -p
  CREATE DATABASE call_survey CHARACTER SET utf8mb4;
  ```

- [ ] **Schema imported**
  ```bash
  mysql -u root -p call_survey < database.sql
  ```

- [ ] **Tables created successfully** (4 tables: surveys, questions, responses, answers)

### Step 6: Test Installation

- [ ] **Ran test script**
  ```bash
  php test.php
  ```

- [ ] **All tests passed** (all ✅ green checkmarks)
  - [ ] PHP version OK
  - [ ] Composer dependencies installed
  - [ ] Database connection successful
  - [ ] All tables exist

### Step 7: Start Server

- [ ] **Started development server**
  ```bash
  ./start-dev.sh
  # OR: php -S localhost:8000 -t public
  ```

- [ ] **Server started without errors**
- [ ] **Port 8000 available** (or chose different port)

### Step 8: Access Dashboard

- [ ] **Opened browser**
- [ ] **Visited http://localhost:8000**
- [ ] **Dashboard loaded successfully** (no errors)
- [ ] **Can see "Call-Survey ULTRALIGHT" heading**

### Step 9: Create First Survey

**Option A: Using Sample Script**
- [ ] **Ran sample script**
  ```bash
  php create-sample-survey.php
  ```
- [ ] **Sample survey created** (ID: 1)

**Option B: Using Web Interface**
- [ ] **Clicked "Luo uusi kysely" button**
- [ ] **Filled in survey title**
- [ ] **Filled in survey description**
- [ ] **Survey created successfully**
- [ ] **Added 2-3 questions**

### Step 10: Test Survey Sending (Optional)

- [ ] **Configured webhook URL in Twilio console**
  - Voice: `https://yourdomain.com/webhook/voice.php?survey_id=1`
  - (For local testing, use ngrok)

- [ ] **Sent test survey via API**
  ```bash
  curl -X POST http://localhost:8000/api/send-survey \
    -H "Content-Type: application/json" \
    -d '{"survey_id": 1, "phone_number": "+1234567890", "channel": "call"}'
  ```

- [ ] **Received successful response** (success: true)

---

## 🐳 Option B: Docker Installation Checklist

Much simpler! Just need Docker.

### Step 1: Install Docker

- [ ] **Docker installed**
  ```bash
  docker --version
  docker-compose --version
  ```

### Step 2: Get Twilio Account

- [ ] **Signed up at https://www.twilio.com/try-twilio**
- [ ] **Got Account SID**
- [ ] **Got Auth Token**
- [ ] **Got Phone Number**

### Step 3: Setup Call-Survey

- [ ] **Cloned repository**
  ```bash
  git clone https://github.com/AnomFIN/call-survey.git
  cd call-survey
  ```

- [ ] **Created config file**
  ```bash
  cp config.example.php config.php
  ```

- [ ] **Edited config.php with Twilio credentials**

### Step 4: Start with Docker

- [ ] **Started Docker containers**
  ```bash
  docker-compose up -d
  ```

- [ ] **Containers running** (check with: `docker ps`)

### Step 5: Access Application

- [ ] **Visited http://localhost:8000**
- [ ] **Dashboard loaded**
- [ ] **Created first survey**

---

## 🎉 Success Checklist

You're ready to go when ALL of these are checked:

- [ ] ✅ Server running without errors
- [ ] ✅ Dashboard accessible in browser
- [ ] ✅ Database connected
- [ ] ✅ Twilio credentials configured
- [ ] ✅ At least one survey created
- [ ] ✅ Test script shows all green checks

---

## 🆘 Troubleshooting Checklist

If something isn't working, check these:

### General Issues

- [ ] **Checked that all required software is installed**
- [ ] **Verified PHP version is 7.4 or higher**
- [ ] **Confirmed MySQL is running**
- [ ] **Made sure port 8000 is not already in use**

### Configuration Issues

- [ ] **config.php exists** (not config.example.php)
- [ ] **Twilio credentials are correct** (no typos)
- [ ] **Phone number is in E.164 format** (+1234567890)
- [ ] **Database credentials match your MySQL setup**

### Database Issues

- [ ] **MySQL server is running**
- [ ] **Database 'call_survey' exists**
- [ ] **User has permissions on database**
- [ ] **All 4 tables imported successfully**

### Server Issues

- [ ] **PHP built-in server started without errors**
- [ ] **No other service using port 8000**
- [ ] **Tried accessing from http://localhost:8000 (not https)**

### Twilio Issues

- [ ] **Account is active** (not suspended)
- [ ] **Phone number is verified** (for trial accounts)
- [ ] **Webhook URL is accessible** (use ngrok for local testing)
- [ ] **Checked Twilio error logs** in console

---

## 📞 Need More Help?

If you're stuck after going through this checklist:

1. **Run the test script** and share output:
   ```bash
   php test.php
   ```

2. **Check the documentation:**
   - [SETUP.md](SETUP.md) - Detailed setup guide
   - [QUICKSTART.md](QUICKSTART.md) - Quick reference
   - [README.md](README.md) - Full documentation

3. **Common fixes:**
   - Restart MySQL: `sudo systemctl restart mysql`
   - Reinstall dependencies: `rm -rf vendor && composer install`
   - Check PHP errors: Enable error reporting in config.php
   - Verify Twilio: Check console.twilio.com for errors

4. **Create a GitHub issue** with:
   - Output of `php test.php`
   - Output of `php -v`
   - Output of `mysql --version`
   - Any error messages you see

---

## 🎓 Next Steps After Setup

Once everything is checked off above:

- [ ] **Read [QUICKSTART.md](QUICKSTART.md)** for quick tips
- [ ] **Explore [API.md](API.md)** to learn the API
- [ ] **Check [DEPLOYMENT.md](DEPLOYMENT.md)** for production deployment
- [ ] **Review [ARCHITECTURE.md](ARCHITECTURE.md)** to understand the system

---

**Print this checklist and check items off as you go!**

Good luck! 🚀
