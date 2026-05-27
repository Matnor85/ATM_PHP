--- Detta projekt är en webbaserad bankomatapplikation byggd i ren PHP (utan ramverk) med MVC-liknande struktur (Repositories & Services). ---
=== Bankomat & Admin-System ===

=== installationsguide ===
Krav: PHP 8.0+

Installation & Databas

Öppna en terminal i rotmappen.

Kör kommandot php seed.php

Klart! Databasen, tabellerna och testanvändarna är nu installerade.

Starta:

Kör php -S localhost:8000 -t public/ och öppna webbläsaren på http://localhost:8000.

Sedan:

1. Klicka dig till server rummer
2. Tryck på kortläsaren brvid dörren så kommer du in till serverrummer.
3. Första gången man är här så trycker på servern racket där finns ett script som lägger upp databas och en admin.
4. Sedan klicka på datorn så kommer du in till en login skärm
5. Admin-kort ID = 1231231231231231231231
   Autentiseringskod (PIN) = 1234
6. Gå till settings
7. Har trycker du på "Create Database" och sedan "Seed Data"

=== Admin Panelel ===
Kortnummer: 1234123412341234, pin: 1234 (Admin)
=== Bankomat Användare ===
Kortnummer: 1111111111111111, PIN: 1111 (Anna Andersson)

Kortnummer: 2222222222222222, PIN: 2222 (Björn Björkman)

Rollkontroll & Säkerhet

Systemet använder en role-kolumn i users-tabellen ('user' eller 'admin').

Server-side kontroll: Alla admin-sidor skyddas av funktionen require_role('admin') i index.php. Om en som inte är admin försöker nå en admin-URL omdirigeras de eller får ett 403-fel.

Sessionssäkerhet: Alla sessions-cookies har httponly och Strict samesite-policy för att förhindra XSS- och CSRF-attacker.

Hashning: Alla PIN-koder hashas med bcrypt (password_hash).

Admin-panelens funktionalitet

Admin-panelen ger full kontroll över systemet:

Användarhantering (CRUD): Skapa, redigera och radera användare (t.ex. namn, roll).

Kontolista: Översikt av alla användares konton och deras aktuella saldo.

Transaktionslogg: Sökbara listor med filtrering på typ (insättning/uttag) och datumintervall.

Audit Logg: Säkerhetslogg som spårar händelser med IP-adress och tidstämpel för full spårbarhet.

Paginering: Listor delas upp för att få en snygg ux.

Bankomatflöden (Användare)

Inloggning: Sker med kort(kortnummer) och PIN.

Saldo: Visar realtidssaldo per konto.

Uttag/Insättning: Utförs med server-side validering (saldokontroll).

Överföring: Möjlighet att flytta pengar mellan egna konton.

Transaktionshistorik: Visar användarens tidigare bankhändelser.

Och dokumentationen hjälper andra utvecklare att förstå flödena och säkerhetsmodellerna snabbt utan att behöva sitta i veckor för att förstå sig på systemet/koden beroende på storlek.
