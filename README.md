dcrrspndnt
==========

De Correspondentbot, zoekt op twitter links naar artikelen op De Correspondent (http://decorrespondent.nl/), indien een nieuw, gedeeld artikel wordt gevonden stuurt de bot een request voor dat artikel op de correspondent. Vervolgens leest de bot de open-graph informatie en slaat die bij het gevonden artikel op.

preview: http://molecule.nl/decorrespondent/

Installatie:
-----------
- checkout de repo
- kopieer settings.local.php.sample naar settings.local.php
- optioneel kopieer ga.inc.php.sample naar ga.inc.php
- wijzig de waardes in beide bestanden
- maak de mysql database aan
- draai create-db.sql om de tabellen aan te maken
- voeg bot.php toe aan je crontab (eens per 10 minuten is meer dan genoeg) bijv:

(crontab entry)

*/10 * * * * cd /var/www/[xxx]/[xxx]/decorrespondent; php bot.php 1>>/tmp/correspondent.out 2>&1

In de data directorie staat een bestand some-data.sql, je kunt deze importeren in je database zodat je alvast een aantal artikel links en meta data hebt verzameld. Hiermee kan je je website bekijken zonder dat je de bot voor de eerste keer hebt opgestart. De data gaat over de artikelen die voor 9 oktober zijn verschenen. Als ik een verse datadump wegschrijf zal de datum in deze README dat aangeven.

open index.php in de Browser en begin te lezen.

Dit project maakt gebruik van simple_html_dom.php en oAuth

============================
Dit projectje is een proeve van bekwaamheid, vind je de artikelen van de Correspondent leuk, overweeg dan een abonnement
http://decorrespondent.nl/

![dcrrspndnt logo](img/dcrrspndnt.png)

