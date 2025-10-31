# Workshop‑Anmeldung (mod_workshopbooking)

Ein schnelles Moodle‑Aktivitäts‑Plugin für Workshop‑Anmeldungen und Veranstaltungen – mit Ein‑Klick‑An/Abmeldung, optionalem Anmeldezeitraum, Platzlimit und Serienterminen.

## Installation
1. Diese ZIP-Datei **ohne** entpacken in Moodle als Admin hochladen: _Website-Administration → Plugins → Plugins installieren_.
   Alternativ per FTP/SCP nach `mod/` hochladen und entpacken, sodass der Ordner `mod/workshopbooking` entsteht.
2. Nach dem Upload die Datenbankänderungen bestätigen.

## Nutzung
- In einem Kurs: *Aktivität oder Material anlegen* → **Workshop‑Anmeldung**.
- Felder setzen: Name, Beschreibung, Anmeldezeitraum, Max. Plätze.
- Teilnehmende melden sich mit einem Klick an/ab, Trainer/innen sehen die Liste.

Kompatibel ab Moodle 4.1.

## Neu in 1.1.0
- CSV-Export der Teilnehmer/innenliste (Button in der Aktivitätsansicht für Lehrende).
- Eigenes Aktivitäts-Icon (`pix/icon.svg`).

## Neu in 1.1.1
- Export-Button zusätzlich im Hauptbereich der Aktivitätsansicht.
- Export-Link im Einstellungsmenü der Aktivität.

## Neu in 1.2.0
- Neue Rechte: `mod/workshopbooking:viewparticipants` (nur Lehr-/Managerrollen) und `mod/workshopbooking:export`.
- Teilnehmer/innen sehen keine Namensliste und haben keinen CSV-Export.

## Neu in 1.2.1
- Neues Aktivitäts-Icon (event.png) eingebunden als pix/icon.png.

## Neu in 1.3.0
- Überarbeitete Ansicht (Card-Layout, Badges, konsistente Meldungen).
- Doppelter Titel entfernt (kein zweites großes "Name").
- Leichte Styles in `styles.css`.
