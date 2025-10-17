# call-survey

AnomFIN Call-Survey ULTRALIGHT — äärimmäisen kevyt ja helppokäyttöinen PHP-pohjainen asiakastyytyväisyyskyselyjärjestelmä, jossa on suora Twilio-integraatio (puhelut, SMS, WhatsApp).

## 🚀 Super Easy Setup

**👉 BRAND NEW? Start here:** [**GETTING-STARTED.md**](GETTING-STARTED.md) - **Complete checklist to track your progress!**

**👉 WANT STEP-BY-STEP GUIDE?** [**SETUP.md**](SETUP.md) - **Detailed setup guide with troubleshooting!**

**Quick reference?** See [QUICKSTART.md](QUICKSTART.md) for a 5-minute setup summary.

**Ready for production?** See [DEPLOYMENT.md](DEPLOYMENT.md) for complete deployment instructions.

### Three Ways to Get Started

#### 1️⃣ Automatic Install (Easiest - Recommended!)

```bash
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
./install.sh  # Interactive installer - just follow the prompts!
```

Then edit `config.php` with your Twilio credentials and visit http://localhost:8000

#### 2️⃣ Docker (No PHP/MySQL installation needed!)

```bash
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
cp config.example.php config.php
# Edit config.php with your Twilio credentials
docker-compose up -d
# Visit http://localhost:8000
```

#### 3️⃣ Manual Install (More control)

```bash
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
composer install
cp config.example.php config.php
# Edit config.php
mysql -u root -p < database.sql
php -S localhost:8000 -t public
```

### 📖 Setup Documentation

- **[GETTING-STARTED.md](GETTING-STARTED.md)** - Complete checklist for tracking your setup
- **[SETUP.md](SETUP.md)** - Step-by-step guide for beginners with troubleshooting
- **[QUICKSTART.md](QUICKSTART.md)** - Quick reference (5 minutes)
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Production deployment guide

## Ominaisuudet

- 📞 **Puhelukyselyt** - Automaattiset kyselypuhelut Twilion kautta
- 💬 **SMS-kyselyt** - Lähetä kyselyt tekstiviestillä
- 📱 **WhatsApp-kyselyt** - Tavoita asiakkaat WhatsAppissa
- 📊 **Yksinkertainen hallintapaneeli** - Luo ja hallitse kyselyitä
- 💾 **MySQL-tietokanta** - Tallenna vastaukset turvallisesti
- ⚡ **ULTRALIGHT** - Kevyt ja nopea, vain PHP ja MySQL

## Asennus

### Vaatimukset

- PHP 7.4 tai uudempi
- MySQL 5.7 tai uudempi
- Composer
- Twilio-tili

### Asennusohjeet

1. **Kloonaa repositorio**
```bash
git clone https://github.com/AnomFIN/call-survey.git
cd call-survey
```

2. **Asenna riippuvuudet**
```bash
composer install
```

3. **Luo tietokanta**
```bash
mysql -u root -p < database.sql
```

4. **Konfiguroi sovellus**
```bash
cp config.example.php config.php
nano config.php
```

Täytä Twilio-tunnuksesi ja tietokannan tiedot.

5. **Käynnistä kehityspalvelin**
```bash
php -S localhost:8000 -t public
```

Avaa selaimessa: http://localhost:8000

## Käyttö

### Kyselyn luominen

1. Avaa hallintapaneeli
2. Klikkaa "Luo uusi kysely"
3. Lisää kysymykset
4. Aktivoi kysely

### Kyselyn lähettäminen

#### API:n kautta

```bash
curl -X POST http://localhost:8000/api/send-survey \
  -H "Content-Type: application/json" \
  -d '{
    "survey_id": 1,
    "phone_number": "+358401234567",
    "channel": "call"
  }'
```

#### PHP:n kautta

```php
$twilioService = new \CallSurvey\TwilioService($config['twilio']);
$result = $twilioService->makeCall(
    '+358401234567',
    'http://yourdomain.com/webhook/voice.php?survey_id=1'
);
```

### Kyselyn tulokset

Näytä tulokset hallintapaneelissa tai hae API:sta:

```bash
curl http://localhost:8000/api/survey-stats?id=1
```

## Tietorakenne

### Surveys (Kyselyt)
- `id` - Kyselyn tunniste
- `title` - Kyselyn otsikko
- `description` - Kuvaus
- `active` - Onko kysely aktiivinen
- `created_at` - Luontiaika

### Questions (Kysymykset)
- `id` - Kysymyksen tunniste
- `survey_id` - Mihin kyselyyn kuuluu
- `question_text` - Kysymysteksti
- `question_type` - Tyyppi (rating, yesno, text)
- `question_order` - Järjestys

### Responses (Vastaukset)
- `id` - Vastauksen tunniste
- `survey_id` - Mihin kyselyyn
- `phone_number` - Vastaajan numero
- `channel` - Kanava (call, sms, whatsapp)
- `status` - Tila (started, completed, abandoned)

### Answers (Yksittäiset vastaukset)
- `id` - Vastauksen tunniste
- `response_id` - Mihin vastaukseen kuuluu
- `question_id` - Mihin kysymykseen
- `answer_value` - Vastauksen arvo

## API Endpointit

### POST /api/send-survey
Lähetä kysely asiakkaalle

**Parametrit:**
- `survey_id` - Kyselyn ID
- `phone_number` - Puhelinnumero (E.164-muoto)
- `channel` - Kanava (call, sms, whatsapp)

### GET /api/survey-stats?id={survey_id}
Hae kyselyn tilastot

**Vastaus:**
```json
{
  "survey_id": 1,
  "total_responses": 10,
  "completed_responses": 8,
  "questions": 3
}
```

## Twilio Webhookit

### /webhook/voice.php
Käsittelee puhelukyselyt. Twilio kutsuu tätä endpointia jokaisella kysymyksellä.

## Tuotantoon vienti

1. **Aseta tuotantoympäristö**
   - Käytä Apache/Nginx-palvelinta
   - Aseta document root -> `public/` -kansioon
   - Ota SSL käyttöön (Twilio vaatii HTTPS:ää)

2. **Konfiguroi Twilio**
   - Aseta webhook URL: `https://yourdomain.com/webhook/voice.php`
   - Varmista että palomuurit sallivat Twilion IP-osoitteet

3. **Tietoturva**
   - Älä commitoi `config.php` -tiedostoa
   - Käytä vahvoja salasanoja
   - Rajoita tietokannan käyttöoikeudet

## Kehitys

Projekti on suunniteltu äärimmäisen kevyeksi ja yksinkertaiseksi. 

**Arkkitehtuuri:**
- `src/` - PHP-luokat
- `public/` - Julkiset tiedostot
- `database.sql` - Tietokantarakenne

## Lisenssi

MIT License

## Tuki

Jos tarvitset apua:
1. Luo issue GitHubissa
2. Tarkista Twilion dokumentaatio
3. Tarkista PHP-virhelokit

## Tekijät

AnomFIN - https://github.com/AnomFIN
