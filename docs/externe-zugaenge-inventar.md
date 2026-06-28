# Inventar: Externe Konten & Zugänge

**Stand:** 2026-06-28 · Reine Leseaufgabe — **keine Secret-Werte** in dieser Liste, nur Dienst, Ort im Code und der `.env`-Variablenname. Zweck: klären, **auf wessen Namen** die Konten laufen und ob du selbst Zugang hast.

## 1. API-/Datendienste mit Schlüssel/Login (Zugang zu klären)
| Dienst | Zweck | Host | Ort im Code | Schlüssel (.env) | Zugang? |
|---|---|---|---|---|---|
| **Tomorrow.io** | Wetter (Dashboard) | api.tomorrow.io | `EmployeeDashboardController::fetchWeatherData` | `DASHBOARD_KEY` | ⚠️ **Ex-Programmierer**, kostenlos → später tauschen |
| **OpenWeatherMap** | Wetter (Admin) | api.openweathermap.org | `AdminController::getWeatherData` | `WEATHER_API_KEY` | ❓ wessen Konto? |
| **RapidAPI** (Weatherbit) | Wetter | weatherbit-v1-mashape.p.rapidapi.com | `ToolsController` | `RAPIDAPI_KEY` | ❓ |
| **myUplink / NIBE** | Wärmepumpen-Daten (OAuth) | api.myuplink.com, login.myuplink.com | `Api/ApiLinkController` | `MYUPLINK_CLIENT_ID`, `MYUPLINK_CLIENT_SECRET` | ❓ NIBE-Entwicklerkonto |
| **Google Maps** | Karten | maps.googleapis.com | viele Blade-Views | `GOOGLE_MAPS_KEY` | ❓ Google-Cloud-Konto |
| **Mapbox** | Karten/Adresssuche | api.mapbox.com | `customer/customer_page/prof.blade.php` | `MAPBOX_TOKEN` | ❓ Mapbox-Konto |
| **Bitrix24** | Chat-Integration | solaraspekt.bitrix24.de | `Bitrix-/MessageController` | (Webhook/Token in `.env`) | Firmen-Konto „solaraspekt" |
| **IDS / GC Online** | Artikel-Shop-Schnittstelle | gconlineplus.de | `config/services.php → ids` | `IDS_USERNAME`, `IDS_PASSWORD`, `IDS_KNDNR` | ❓ Händler-/Kundenkonto |
| **NewsAPI** | News-Widget | newsapi.org | `config/services.php → newsapi` | `NEWSAPI_KEY` | ❓ |
| **AWS** (S3 + SES) | Dateispeicher / Mailversand | s3.eu-central-1.amazonaws.com | `config/services.php → ses`, filesystems | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` | ❓ AWS-Konto |
| **Mailgun / Postmark** | Mailversand (Alternativen) | api.mailgun.net | `config/services.php` | `MAILGUN_*`, `POSTMARK_TOKEN` | ❓ evtl. ungenutzt |

## 2. Freie Dienste (kein Konto/Schlüssel nötig)
Open-Meteo (`api.open-meteo.com`), DWD (`www.dwd.de`), JRC PVGIS (`re.jrc.ec.europa.eu`, EU-Solardaten), Overpass/OpenStreetMap (`overpass-api.de`), Geo-IP (`ipapi.co`, `ipinfo.io`, `api.iplocation.net`), Avatare (`ui-avatars.com`). → kein Handlungsbedarf.

## 3. Mail / IMAP
- **Ausgehende Mail (SMTP):** `config/mail.php`, `.env MAIL_*`.
- **Mail-Hosting:** Standard-IMAP-Host im Code = `w00dfa8e.kasserver.com` → Hoster **ALL-INKL (KAS)**. Zugang zum Mail-/Webhosting? ❓
- **IMAP-Abruf der Lead-Mails:** pro Konto in der **Datenbank** gespeichert (`LeadEmailAccounts`), Host/Benutzer/Passwort vom Nutzer eingegeben. ⚠️ Passwörter liegen aktuell **im Klartext** in der DB (Audit #16).

## 4. Domain / Hosting
- **Produktiv-Domain:** `nuri-head.de` (in `ApiLinkController` als OAuth-Redirect). DNS-/Hosting-Zugang? ❓
- Firmen-Sites: `solar-aspekt.de`, `solaraspekt.bitrix24.de`.
- **Produktionsserver** (wo `nuri-head.de` läuft): aus dem Code **nicht** ermittelbar → ❓ klären (Hoster, evtl. Ex-Programmierer).
- Lokal: `ticket.test` (Herd, dein Rechner).

## 5. Git / Code-Hosting
- **origin** = `github.com/raminsadid2021/nuri-head` (öffentlich) — vermutlich **Ex-Programmierer/Owner**, du hast **keinen Schreibzugang** (403 bestätigt).
- **fork** = `github.com/yamasolaraspekt-max/nuri-head` (öffentlich) — **dein** Konto.
- **Privates Repo** für den App-Code: **noch einzurichten** (für das Backup von `app/`+`routes/`).

## Kurz: was du klären solltest (Priorität)
1. **GitHub origin** (raminsadid2021) + **Produktions-Hosting/Server** für nuri-head.de — wer hat Zugang?
2. **myUplink/NIBE**, **Google Cloud (Maps)**, **Mapbox**, **AWS**, **IDS-Händlerkonto** — auf wessen Namen?
3. **Mail-Hosting (ALL-INKL/kasserver)** — Zugangsdaten?
4. **Tomorrow.io** — unkritisch (kostenlos), später eigenes Konto.
