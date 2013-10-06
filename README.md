dcrrspndnt
==========

De Correspondentbot, zoekt op twitter links naar artikelen op De Correspondent (http://decorrespondent.nl/), indien een nieuw, gedeeld artikel wordt gevonden stuurt de bot een request voor dat artikel op de correspondent. Vervolgens leest de bot de open-graph informatie en slaat die bij het gevonden artikel op.

Installatie:
checkout de repo
kopieer settings.local.php.sample naar settings.local.php
optioneel kopieer ga.inc.php.sample naar ga.inc.php

wijzig de waardes in beide bestanden

maak de mysql database aan

draai create-db.sql om de tabellen aan te maken

voeg bot.php toe aan je crontab (eens per 10 minuten is meer dan genoeg) bijv:

(crontab entry)

*/10 * * * * cd /var/www/[xxx]/[xxx]/decorrespondent; php bot.php 1>>/tmp/correspondent.out 2>&1

open index.php in de Browser


Dit project maakt gebruik van simple_html_dom.php en oAuth

============================
Dit projectje is een proeve van bekwaamheid, vind je de artikelen van de Correspondent leuk, overweeg dan een abonnement
http://decorrespondent.nl/

