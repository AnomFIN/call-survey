# Copilot Runbook — AnomFIN Call-Survey ULTRALIGHT

- **Mikä tämä on:** ULTRALIGHT PHP (8.4) -sovellus, MySQL 8.0, Twilio Voice/SMS/WhatsApp; UI seuraa anomfin-website -designia.
- **Palvelinpolut:**
  - anomfin-website tuotanto → `/home/anomfinf/public_html/`
  - Call-Survey ULTRALIGHT → `/home/anomfinf/public_html/app/`
- **Ympäristö:** PHP 8.3–8.4, laajennukset `mysqli`, `curl`, `mbstring`; MySQL 8.0 utf8mb4; pakollinen SSL.
- **Asennus:** selain → `https://app.anomfin.fi/install/asennus.php`; täytä DB, Twilio, PIN, `.env` polku (suositus `/home/anomfinf/.envs/anomfin-ultralight`).
- **Secrets GitHubissa:** `CPANEL_HOST`, `CPANEL_USER`, ja `CPANEL_PASS` **tai** `CPANEL_SSH_KEY` + `CPANEL_SSH_PORT` (oletus 22).
- **Build & Deploy:** GitHub Actions → workflow `Deploy`; auto-run myös push `main`.
- **Design Sync:** `tools/sync-theme.sh` hakee `anomfin-website` public/assets → `public/css|js|fonts|img/brand`. Älä muokkaa `theme/*`; tee UI-muutokset `public/css/style.css` ja `public/js/app.js`.
- **Rollback:** Lataa aiempi `anomfin-ultralight.zip` GitHub Actions -artifacteista ja redeployaa workflowlla.
- **Turva:** `.env` ei julki; Twilio webhook allekirjoitustarkistus pakollinen; CSV-import ja PIN-suojatut API-kutsut testattava; Twilio Voice DTMF 1/2: reititys 2 → `AGENT_PSTN`.
- **Acceptanssi-checklist:**
  - `https://app.anomfin.fi/public/index.html` → HTTP 200.
  - UI vastaa anomfin-website -väri- ja fonttimaailmaa + logo näkyy.
  - CSV-import, PIN-suojatut API-kutsut toimivat.
  - Twilio Voice DTMF 1/2 ohjaa oikein (`2` → agentti).
  - SMS & WhatsApp lähetys + tilapäivitykset ok.
