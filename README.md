# AnomFIN Call-Survey ULTRALIGHT

AnomFIN Call-Survey ULTRALIGHT on hyperkevyt mutta yritystason soitto- ja viestipalautteen työkalu. Se yhdistää Twilio Voice IVR:n, SMS- ja WhatsApp-viestit, CSV-importin sekä PIN-suojatut API-rajapinnat yhdeksi saumattomaksi kokonaisuudeksi.

## Design System Sync

- UI on 1:1 linjassa [`anomfin-website`](https://github.com/AnomFIN/anomfin-website) -kokemuksen kanssa.
- `tools/sync-theme.sh` hakee sparse checkoutilla seuraavat polut `anomfin-website` -reposta ja kopioi ne applikaatioon:
  - `public/assets/css/*` → `public/css/theme/`
  - `public/assets/js/*` → `public/js/theme/`
  - `public/assets/fonts/*` → `public/fonts/`
  - `public/assets/img/brand/*` → `public/img/brand/`
- Synkronoinnin jälkeen `public/css/theme/theme-index.css` generoidaan automaattisesti ja `public/css/style.css` importtaa sen. Kaikki app-kohtaiset luokat prefiksataan `app-`-etuliitteellä törmäysten välttämiseksi.
- PNG-ikonit korvataan kevyillä SVG-versioilla (`logo-mark.svg`, `apple-touch-icon.svg`), jotta repo pysyy binäärivapaana. CI/CD voi generoida tarvittavat rasteriversiot buildissa tarpeen mukaan, mutta niitä ei säilytetä versionhallinnassa.

## Palvelininfra

- anomfin-website (tuotanto): `/home/anomfinf/public_html/`
- Call-Survey ULTRALIGHT: `/home/anomfinf/public_html/app/`
- Suositeltu `.env`-polku: `/home/anomfinf/.envs/anomfin-ultralight` (Apache estää suoran pääsyn repojuuresta).
- PHP 8.3–8.4 + laajennukset `mysqli`, `curl`, `mbstring`; MySQL 8.0 (utf8mb4 oletuksena).

## Turvallisuus ja laatustandardit

- `.htaccess` blokkaa `install/`-hakemiston ja `.env*`-tiedostot ellei `ANOMFIN_INSTALL_ACTIVE=1` ole asetettu hetkellisessä asennuksessa.
- `api/twilio-webhook.php` validoi kaikki Twilio-kutsut `X-Twilio-Signature` -otsakkeen avulla (`hash_hmac` + base64-vertailu).
- CSV-import, PIN-suojatut API-kutsut ja Twilio Voice DTMF (1/2 → `AGENT_PSTN`) kuuluvat regressioputkeen.
- Tietokantataulut luodaan `utf8mb4`-merkistöllä (`sql/schema.sql`).
- Ei Composer-riippuvuuksia; käytetään ainoastaan vakiophp:tä (`curl`, `PDO`, `mysqli`).

## Asennus ja käyttöönotto

1. Deployaa sovellus GitHub Action -workflowlla (ks. alla). Ensijulkaisu luo polun `/home/anomfinf/public_html/app/` cPanelissa.
2. Avaa `https://app.anomfin.fi/install/asennus.php` ja suorita wizard (DB, Twilio, PIN, `.env`-polku). Asennuksen jälkeen poista tai disabloi `install/`.
3. Twilio-integraatio: lisää Voice/SMS/WhatsApp-webhookit osoittamaan `https://app.anomfin.fi/api/twilio-webhook.php`.
4. Varmista, että `.env`-tiedosto ei ole web-palvelimen juuren alla.

## GitHub Actions — Deploy

Workflow `.github/workflows/deploy.yml` tekee seuraavaa:

1. Checkouttaa repot (mukaan lukien sparse checkout `anomfin-website` → assets) ja cachettaa theme-revision.
2. Ajaa PHP 8.4 -ympäristössä linttauksen (`php -l`) ja paikallisen health checkin (`php -S` + `curl`).
3. Synkkaa design-tokenit `tools/sync-theme.sh` -skriptillä.
4. Pakkaa `anomfin-ultralight.zip` (sis. `public`, `api`, `install`, `sql`, `worker`, `.env.example`, `README.md`, `.htaccess`).
5. Siirtää ZIP:n cPanel-palvelimelle SFTP/rsyncillä ja purkaa sen hakemistoon `/home/anomfinf/public_html/app/`.
6. Suorittaa `curl -sI https://app.anomfin.fi/public/index.html | grep 200` varmistaakseen onnistuneen julkaisun.
7. Lataa ZIP:n myös workflow-artifactiksi rollbackia varten.

Triggerit: `push` → `main` sekä manuaalinen `workflow_dispatch`.

### Secrets

- `CPANEL_HOST`, `CPANEL_USER` ja `CPANEL_PASS` **tai** `CPANEL_SSH_KEY` + `CPANEL_SSH_PORT` (oletus `22`).

## Rollback

- Avaa viimeisin onnistunut workflow-runi, lataa `anomfin-ultralight.zip` artifact.
- Deployaa sama paketti uudelleen ajamalla `Deploy`-workflow manuaalisesti ja syöttämällä artifact tiedostosyötteeksi.

## Kehitysehdotuksia / Roadmap

- Live Dashboard (WebSocket) Twilio-eventeille.
- Agenttien reaaliaikainen kalenterivarauksien synkronointi (Google/Outlook).
- AI-pohjainen sentimenttianalyysi IVR-tallenteista ja automaattinen palauteraportointi.
