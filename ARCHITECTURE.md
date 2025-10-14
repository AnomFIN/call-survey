# Call-Survey ULTRALIGHT - Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Client/User Layer                         │
├─────────────────────────────────────────────────────────────────┤
│  Web Browser         Phone (IVR)        SMS/WhatsApp            │
│  Dashboard UI        Voice Survey       Text Survey              │
└──────────┬─────────────────┬───────────────────┬────────────────┘
           │                 │                   │
           ▼                 ▼                   ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Application Layer (PHP)                       │
├─────────────────────────────────────────────────────────────────┤
│  ┌────────────┐  ┌──────────┐  ┌─────────┐  ┌──────────┐      │
│  │ Dashboard  │  │ Webhook  │  │   API   │  │  Survey  │      │
│  │    UI      │  │ Handler  │  │Endpoints│  │Management│      │
│  └─────┬──────┘  └────┬─────┘  └────┬────┘  └────┬─────┘      │
│        │              │              │            │             │
│        └──────────────┴──────────────┴────────────┘             │
│                          │                                      │
│                          ▼                                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Core Business Logic                         │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │  Database.php    │  Survey.php    │  TwilioService.php │   │
│  │  Connection      │  CRUD Ops      │  API Integration   │   │
│  └─────────────────────────────────────────────────────────┘   │
└────────────────────────┬───────────────────┬────────────────────┘
                         │                   │
                         ▼                   ▼
┌─────────────────────────────┐   ┌──────────────────────────┐
│     Data Layer (MySQL)       │   │   External Services      │
├─────────────────────────────┤   ├──────────────────────────┤
│  - surveys                   │   │  Twilio API              │
│  - questions                 │   │  - Voice Calls           │
│  - responses                 │   │  - SMS Messages          │
│  - answers                   │   │  - WhatsApp Messages     │
└─────────────────────────────┘   └──────────────────────────┘
```

## Data Flow

### 1. Voice Survey Flow

```
Customer Phone ──1. Inbound Call──> Twilio
                                      │
                 2. Webhook Request   │
                      (POST)          ▼
                         ┌────────────────────┐
                         │ webhook.php        │
                         │ (IVR Logic)        │
                         └────────┬───────────┘
                                  │
              3. Get Questions    │  4. TwiML Response
                from Database     │     (Ask Question)
                                  ▼
                         ┌────────────────────┐
                         │ Database           │
                         │ Save Answers       │
                         └────────────────────┘
                                  │
              5. Repeat until all questions answered
                                  │
              6. Thank You + Hangup
```

### 2. API Survey Flow

```
External App ──1. POST /api/send-survey──> API Handler
                                              │
              2. Validate Parameters          │
                 - survey_id                  ▼
                 - phone_number        ┌──────────────┐
                 - channel             │TwilioService │
                                       └──────┬───────┘
                                              │
              3. Initiate Communication       │
                 based on channel             │
                                              ▼
                    ┌────────────┬────────────┴────────────┐
                    ▼            ▼                         ▼
              Voice Call     SMS Message          WhatsApp Message
              (Webhook)      (Survey Link)        (Survey Link)
```

### 3. Dashboard Flow

```
Admin Browser ──1. Visit Dashboard──> index.php (Router)
                                          │
                                          ▼
                              ┌───────────────────────┐
                              │  dashboard.php        │
                              │  Load Survey List     │
                              └───────┬───────────────┘
                                      │
               2. Display All Active Surveys
                                      │
                                      ▼
               ┌──────────────────────────────────────┐
               │  User Actions:                       │
               │  - Create New Survey                 │
               │  - View/Edit Survey                  │
               │  - View Results                      │
               └──────┬───────────────────────────────┘
                      │
                      ├─> survey/create.php  (Create)
                      ├─> survey/view.php    (Edit)
                      └─> survey/results.php (Analyze)
```

## Component Responsibilities

### Frontend Components

#### Dashboard (dashboard.php)
- Display all active surveys
- Quick access to survey management
- Statistics overview

#### Survey Creator (survey/create.php)
- Form for new survey creation
- Title and description input
- Survey activation

#### Survey Editor (survey/view.php)
- Add/edit questions
- Configure question types
- Display webhook URLs
- Link to results

#### Results Viewer (survey/results.php)
- Response statistics
- Question-by-question analysis
- Visual charts and graphs
- Response list with details

### Backend Components

#### Database Class
```php
Responsibilities:
- Singleton database connection
- Query execution with prepared statements
- Result fetching helpers
- Transaction support
```

#### Survey Class
```php
Responsibilities:
- Survey CRUD operations
- Question management
- Response tracking
- Answer storage
- Results aggregation
```

#### TwilioService Class
```php
Responsibilities:
- Voice call initiation
- SMS message sending
- WhatsApp message sending
- Error handling for Twilio API
```

#### API Handler (api.php)
```php
Endpoints:
- POST /api/send-survey
- GET /api/survey-stats
```

#### Webhook Handler (webhook.php)
```php
Responsibilities:
- Parse Twilio requests
- Generate TwiML responses
- Manage call state via session
- Save survey responses
```

## Database Schema Relationships

```
┌─────────────┐
│   surveys   │
│ id          │◄───┐
│ title       │    │
│ description │    │
│ active      │    │
└─────────────┘    │
                   │
       ┌───────────┴───────────┐
       │                       │
       │                       │
┌──────▼──────┐         ┌──────▼──────┐
│ questions   │         │ responses   │
│ id          │         │ id          │
│ survey_id   │         │ survey_id   │
│ text        │◄────┐   │ phone       │
│ type        │     │   │ channel     │
│ order       │     │   │ status      │
└─────────────┘     │   └──────┬──────┘
                    │          │
                    │          │
                    │   ┌──────▼──────┐
                    │   │   answers   │
                    │   │ id          │
                    └───┤ question_id │
                        │ response_id │
                        │ value       │
                        └─────────────┘
```

## Security Architecture

### Input Validation
```
User Input ──> Validation ──> Sanitization ──> Processing
                   │              │
                   ▼              ▼
            Type Checking    htmlspecialchars()
            Range Checking   PDO Prepared Stmt
            Format Checking
```

### Authentication Flow (Future)
```
Request ──> API Key Check ──> Rate Limit ──> Authorization ──> Process
              │                   │              │
              ▼                   ▼              ▼
          Valid?              Within Limit?   Has Permission?
          Yes/No              Yes/No          Yes/No
```

## Deployment Architecture

### Development Environment
```
Developer Machine
├── PHP Built-in Server (localhost:8000)
├── Local MySQL Database
├── Twilio Test Credentials
└── ngrok for Webhook Testing
```

### Production Environment (Single Server)
```
Production Server
├── Apache/Nginx + PHP-FPM
├── MySQL Database
├── SSL Certificate (Let's Encrypt)
├── Twilio Production Credentials
└── Domain with DNS
```

### Production Environment (Docker)
```
Docker Host
├── Web Container (PHP + Apache)
│   └── Application Files
├── Database Container (MySQL)
│   └── Persistent Volume
└── Docker Network (Internal)
```

## Performance Considerations

### Caching Strategy
- Database connection pooling (singleton)
- Session management for webhook state
- Static asset caching (Apache/Nginx)

### Scaling Strategy
```
Load Balancer
    │
    ├─> Web Server 1 (Stateless)
    ├─> Web Server 2 (Stateless)
    └─> Web Server 3 (Stateless)
             │
             ▼
    Shared Database Server
    (MySQL with Replication)
```

## Monitoring Points

### Application Monitoring
- PHP error logs
- Apache/Nginx access logs
- Database slow query log
- Twilio webhook logs

### Business Metrics
- Survey send success rate
- Survey completion rate
- Average response time
- Questions per survey
- Responses per day

## Technology Stack

```
┌─────────────────────────────────────────────┐
│              Presentation Layer             │
│  HTML5 │ CSS3 │ Vanilla JavaScript          │
└─────────────────────────────────────────────┘
┌─────────────────────────────────────────────┐
│              Application Layer              │
│  PHP 7.4+ │ Composer │ PSR-4 Autoloading   │
└─────────────────────────────────────────────┘
┌─────────────────────────────────────────────┐
│              Integration Layer              │
│  Twilio SDK 6.44+ │ REST API                │
└─────────────────────────────────────────────┘
┌─────────────────────────────────────────────┐
│              Data Layer                     │
│  MySQL 5.7+ │ PDO │ utf8mb4                 │
└─────────────────────────────────────────────┘
┌─────────────────────────────────────────────┐
│              Infrastructure Layer           │
│  Apache/Nginx │ Docker │ Linux               │
└─────────────────────────────────────────────┘
```

## File Structure Explained

```
call-survey/
│
├── src/                      # Core business logic (PSR-4)
│   ├── Database.php         # Singleton DB connection
│   ├── Survey.php           # Survey operations
│   └── TwilioService.php    # External API integration
│
├── public/                   # Web accessible (document root)
│   ├── index.php            # Front controller (router)
│   ├── dashboard.php        # Main admin interface
│   ├── api.php              # REST API endpoints
│   ├── webhook.php          # Twilio callback handler
│   ├── .htaccess            # Apache mod_rewrite rules
│   └── survey/              # Survey management pages
│       ├── create.php       # New survey form
│       ├── view.php         # Edit survey & questions
│       └── results.php      # Analytics & visualization
│
├── config.example.php       # Configuration template
├── database.sql            # Schema & indexes
├── composer.json           # PHP dependencies
│
├── README.md               # Main documentation
├── QUICKSTART.md          # Getting started guide
├── DEPLOYMENT.md          # Production setup
├── API.md                 # API reference
├── CONTRIBUTING.md        # Development guide
├── CHANGELOG.md           # Version history
├── LICENSE                # MIT license
│
├── install.sh             # Automated installer
├── start-dev.sh           # Dev server launcher
├── test.php               # Validation script
├── create-sample-survey.php  # Demo data
│
├── Dockerfile             # Container definition
├── docker-compose.yml     # Multi-container setup
└── .gitignore            # Git exclusions
```

## Key Design Decisions

### 1. ULTRALIGHT Philosophy
- Minimal dependencies (only Twilio SDK)
- No framework overhead
- Direct PHP, no ORM
- Simple routing

### 2. Database Design
- Normalized structure (4NF)
- Proper foreign keys
- Indexed for performance
- UTF-8 full support

### 3. Security First
- Prepared statements only
- Output escaping everywhere
- Config file protection
- HTTPS enforcement

### 4. Developer Experience
- Clear separation of concerns
- PSR-4 autoloading
- Extensive documentation
- Easy local development

### 5. Production Ready
- Docker support
- Apache/Nginx configs
- SSL/HTTPS ready
- Monitoring friendly

## Future Enhancements

### Phase 2
- Email survey delivery
- Advanced analytics dashboard
- CSV/Excel export
- Survey templates

### Phase 3
- Multi-language surveys
- Question branching logic
- Custom branding
- Scheduled surveys

### Phase 4
- API authentication
- Webhook logging
- Rate limiting
- Admin user system

## Conclusion

This architecture provides:
✅ Simplicity (ULTRALIGHT)
✅ Scalability (stateless design)
✅ Security (best practices)
✅ Maintainability (clean code)
✅ Extensibility (modular structure)
