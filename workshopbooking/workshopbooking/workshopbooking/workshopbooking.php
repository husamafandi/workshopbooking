<?php
/**
 * This file is part of the workshopbooking_unpacked plugin for Moodle.
 *
 * Copyright (C) 2025 Husam Afandi
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package   workshopbooking_unpacked
 * @author    Husam Afandi
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */


$string['modulename'] = 'Workshop‑Anmeldung';

$string['modulenameplural'] = 'Workshop‑Anmeldungen';

$string['pluginname'] = 'Workshop‑Anmeldung';

$string['pluginadministration'] = 'Verwaltung: Workshop‑Anmeldung';

$string['name'] = 'Name';

$string['availability'] = 'Verfügbarkeit';

$string['signupstart'] = 'Anmeldung öffnet';

$string['signupend'] = 'Anmeldung schließt';

$string['from'] = 'Von';

$string['to'] = 'bis';

$string['maxparticipants'] = 'Max. Anzahl der Anmeldungen';

$string['maxparticipants_help'] = '0 bedeutet unbegrenzt.';

$string['signup'] = 'Jetzt anmelden';

$string['cancel'] = 'Anmeldung stornieren';

$string['signedup'] = 'Sie sind angemeldet.';

$string['canceled'] = 'Ihre Anmeldung wurde storniert.';

$string['cannot_signup'] = 'Anmeldung nicht möglich.';

$string['cannot_cancel'] = 'Stornierung nicht möglich.';

$string['full'] = 'Ausgebucht';

$string['notopen'] = 'Anmeldung ist derzeit nicht geöffnet.';

$string['participants'] = 'Teilnehmer/innen';

$string['signedupon'] = 'Angemeldet am';

$string['noparticipants'] = 'Noch keine Anmeldungen.';

$string['limit_reached'] = 'Buchungen: {$a}';

$string['limit_unlimited'] = 'Buchungen: {$a} (unbegrenzt)';

$string['privacy:metadata'] = 'Das Plugin speichert minimale Anmeldedaten.';

$string['none'] = 'Keine Aktivitäten gefunden.';

$string['exportcsv'] = 'CSV Teilnehmer/innenliste exportieren';

$string['actions'] = 'Aktionen';



// Capabilities

$string['workshopbooking:viewparticipants'] = 'Teilnehmer/innenliste anzeigen';

$string['workshopbooking:export'] = 'Teilnehmer/innenliste exportieren';



// Help (only HTML link)

$string['modulename_help'] = '<p><strong>Workshop‑Anmeldung</strong> organisiert Termine übersichtlich. Teilnehmer_innen sehen verfügbare Sessions mit Datum und Uhrzeit und melden sich mit einem Klick an oder ab.</p><p><strong>Teilnehmer_innen‑Sicht:</strong> Offene Termine werden angeboten. Nach der Anmeldung erscheint der Status sowie eine Schaltfläche zum Abmelden. Die Liste kann nach Kursgruppen gefiltert werden; pro Session werden die belegten Plätze angezeigt.</p><p><strong>Trainer_innen‑Sicht:</strong> Verfügbare Funktionen oben: Gruppenfilter, CSV‑Export, Teilnehmendenliste, Bestätigen/Zurücknehmen einzelner Buchungen sowie Buchungen löschen. Der Export übernimmt den aktuell gewählten Filter.</p><p><strong>Serie erzeugen:</strong> Mit Startdatum, Anzahl der Vorkommnisse und Intervall werden Sessions automatisch erzeugt oder aktualisiert. Die Dauer pro Session wird in <em>Stunden</em> angegeben; aus Vormittags‑ und Nachmittags‑Starthour entstehen VM/NM‑Slots.</p><p><strong>Mehrere Workshops:</strong> Optional können mehrere Workshops in einem Vorgang erzeugt werden. Im Feld „Workshop‑Namen“ jeweils eine Zeile pro Workshop eintragen. Optional pro Zeile ein Startdatum angeben: „Name, TT.MM.JJJJ“ oder „Name, TT.MM.JJJJ HH:MM“. Alle Limits und Zeitfenster gelten für alle angelegten Workshops.</p><p><strong>Kapazitäten & Limits:</strong> Mindest‑/Maximalplätze je Session; „Max. bestätigte Workshops pro TN“ begrenzt die Anzahl gleichzeitig bestätigter Buchungen pro Person. 0 bedeutet unbegrenzt.</p><p><strong>Anmeldezeitraum (optional):</strong> Mit „Öffnet“ und „Schließt“ wird die Buchungsphase begrenzt; außerhalb des Fensters sind Anmeldungen nicht möglich.</p><p><strong>Gruppen:</strong> Jede Session erhält eine Kursgruppe im Format „Workshopname – Datum – VM/NM“. Die Auswahl „Kursgruppe“ filtert die Listenansicht und wird beim Export berücksichtigt.</p><p><strong>Datenschutz:</strong> Personenbezogene Daten bleiben im Kurs. Bei Bedarf können Datenschutzhinweise ergänzt werden.</p><p><span style="font-size:9pt;color:#6b7280"><strong>Entwickler:</strong> Husam Afandi</span></p>';

$string['pluginname_help'] = $string['modulename_help'];



// Manage signups

$string['gotoworkshop'] = 'Zum Workshop';

$string['removesignup'] = 'Anmeldung löschen';

$string['signupremoved'] = 'Anmeldung wurde gelöscht.';

$string['cannot_remove'] = 'Anmeldung konnte nicht gelöscht werden.';



$string['allgroups_local'] = 'Alle Gruppen';





$string['multiworkshops'] = 'Mehrere Workshops';

$string['multiworkshops_help'] = 'Wenn aktiviert, werden Sessions für jeden eingegebenen Workshop‑Namen erzeugt. Alle Limits (min/max, Buchungsfenster, Dauer etc.) gelten für alle Workshops gleichermaßen.';

$string['workshopnames'] = 'Workshop‑Namen';

$string['workshopnames_help'] = 'Einen Namen pro Zeile. Optional: „Name, TT.MM.JJJJ“ (eigenes Startdatum). Optional Zeit: „Name, TT.MM.JJJJ HH:MM“ oder „Name, TT.MM.JJJJ von HH:MM“ – Zeiten < 12:00 überschreiben nur den VM‑Start, Zeiten ≥ 12:00 nur den NM‑Start. Optional 3. Wert: „URL“ – der Link „Zum Workshop“ wird angezeigt. Für alle anderen Parameter gelten die Formulareinstellungen.';

$string['recurhdr'] = 'Serientermine & Limits';

$string['recurenabled'] = 'Serientermine aktivieren (Sessions erzeugen)';

$string['recurstart'] = 'Startdatum der Serie';

$string['recurcount'] = 'Anzahl der Vorkommnisse';

$string['recurintervaldays'] = 'Intervall (Tage)';

$string['durationdays'] = 'Dauer je Termin (Stunden)';

$string['vmstarthour'] = 'Beginn Vormittag (Stunde)';

$string['nmstarthour'] = 'Beginn Nachmittag (Stunde)';

$string['capacitymin'] = 'Mindestteilnehmer/innen pro Session';

$string['capacitymax'] = 'Maximalteilnehmer/innen pro Session';

$string['maxbookingsperuser'] = 'Max. bestätigte Workshops pro TN';

$string['bookopenoffsetdays'] = 'Buchung öffnet (Tage vor Start)';

$string['bookcloseoffsetdays'] = 'Buchung schließt (Tage vor Start)';

$string['task_process_sessions'] = 'Sessions verarbeiten (Warteliste nachrücken)';

$string['btn_generate_series'] = 'Serie erzeugen/aktualisieren';

$string['nosessions'] = 'Noch keine Sessions definiert.';

$string['session'] = 'Session';

$string['dates'] = 'Zeitraum';

$string['slot'] = 'Slot';

$string['capacity'] = 'Kapazität';

$string['booking'] = 'Buchung';

$string['signup'] = 'Anmelden';

$string['signedup_confirmed'] = 'Du bist angemeldet (bestätigt).';

$string['signedup_waitlist'] = 'Du stehst auf der Warteliste.';

$string['status_booked'] = 'Angemeldet';

$string['seriesgenerated'] = 'Serie erzeugt/aktualisiert.';

$string['maxbookingsreached'] = 'Sie haben die maximale Anzahl an Workshops erreicht.';

$string['bookingnotopen'] = 'Die Buchung ist noch nicht geöffnet.';

$string['bookingclosed'] = 'Die Buchung ist geschlossen.';



$string['schemafixed'] = 'Datenbankschema geprüft/ergänzt.';



$string['btn_participants'] = 'Teilnehmer/innen anzeigen';

$string['btn_exportcsv'] = 'CSV exportieren';

$string['participants_heading'] = 'Angemeldete Teilnehmer/innen';

$string['col_session'] = 'Session';

$string['col_slot'] = 'Slot';

$string['col_date'] = 'Beginn';

$string['col_date_end'] = 'Ende';

$string['col_user'] = 'Teilnehmer/in';

$string['col_email'] = 'E-Mail';

$string['col_userid'] = 'Nutzer-ID';

$string['col_username'] = 'Nutzername';

$string['col_created'] = 'Erstellt am';

$string['col_status'] = 'Status';

$string['status_confirmed'] = 'Bestätigt';

$string['status_waitlist'] = 'Warteliste';





$string['filter_group'] = 'Kursgruppe';

$string['all_groups'] = 'Alle Gruppen';

$string['apply_filter'] = 'Filtern';

$string['csv_for_filter'] = 'CSV exportieren (mit Filter)';





$string['btn_groupfilter'] = 'Gruppenfilter';



$string['pluginname_help'] = '<p><strong>Workshop-Anmeldung</strong> ermöglicht eine schnelle und klare Organisation von Terminen. Teilnehmer_innen sehen eine Liste aller verfügbaren Sessions mit Datum und Uhrzeit (Format: „14. Oktober 2025 von 08:00 bis 10:00“) und melden sich mit einem Klick an oder ab.</p>



<p><strong>Teilnehmer_innen-Sicht:</strong> Nur offene Termine werden angeboten; volle oder geschlossene Slots zeigen einen Hinweis. Nach der Anmeldung erscheint der Status und eine Schaltfläche zum Abmelden. Die Darstellung berücksichtigt Gruppenfilter und zeigt pro Session die belegten Plätze.</p>



<p><strong>Trainer_innen-Sicht:</strong> Oben stehen Verwaltungsfunktionen: Gruppenfilter, CSV-Export, Bestätigen/Zurücknehmen einzelner Buchungen sowie Buchung löschen. Die Liste der Anmeldungen kann nach Gruppe gefiltert werden; der CSV-Export übernimmt den aktuell gewählten Filter.</p>



<p><strong>Serientermine erzeugen:</strong> Beim Anlegen der Aktivität definiert ihr <em>Startdatum der Serie</em> (recurstart), <em>Anzahl der Termine</em> (recurcount) und das <em>Intervall in Tagen</em> (recurintervaldays). Mit einem Button können die Sessions automatisch erzeugt oder aktualisiert werden.</p>



<p><strong>Uhrzeiten und Dauer:</strong> Verwendet <em>Vormittags-Starthour</em> (vmstarthour) und <em>Nachmittags-Starthour</em> (nmstarthour). Die <em>Dauer pro Session</em> (Stunden) bestimmt die Endzeit; die Ausgabe erfolgt als „von … bis …“.</p>



<p><strong>Kapazitäten:</strong> Tragt min/max Plätze (capacitymin/capacitymax) ein. Ist das Maximum erreicht, wird die Anmeldung automatisch gesperrt. Bestätigte Plätze werden separat gezählt, wenn eine Bestätigung erfolgt.</p>



<p><strong>Anmeldezeitraum (optional):</strong> Mit <em>Öffnet</em> und <em>Schließt</em> begrenzt ihr die Buchungsphase; außerhalb dieses Fensters können Teilnehmer_innen nicht buchen.</p>



<p><strong>Gruppen:</strong> Sessions können einer Kursgruppe zugeordnet werden. Im Trainerbereich filtert ihr nach Gruppe; der Export berücksichtigt die Auswahl. Der Button „Gruppen erzeugen“ setzt verwaiste Gruppen-IDs auf „Alle Gruppen“.</p>



<p><strong>Rechte:</strong> Teilnehmer_innen benötigen <code>mod/workshopbooking:signup</code>; Trainer_innen erhalten zusätzlich <code>mod/workshopbooking:manage</code>, um Aktionen, Export und Serientermine zu nutzen.</p>



<p><strong>Kompatibilität:</strong> Getestet mit Moodle 4.1. Für ältere Versionen können Abweichungen bestehen.</p>



<p><strong>Entwickler:</strong> Husam Afandi</p>';



$string['cfg_heading_defaults'] = 'Voreinstellungen';

$string['cfg_heading_defaults_desc'] = 'Diese Werte sind systemweite Standards für neue Workshop‑Anmeldungen und Sessions. Sie können in der Aktivität überschrieben werden.';

$string['cfg_defaultbookopenoffsetdays'] = 'Standard: Anmeldung öffnet (Tage vorher)';

$string['cfg_defaultbookcloseoffsetdays'] = 'Standard: Anmeldung schließt (Tage vorher)';

$string['cfg_defaultrecurenabled'] = 'Standard: Serientermine aktivieren';

$string['cfg_defaultrecurcount'] = 'Standard: Anzahl Serientermine';

$string['cfg_defaultrecurintervaldays'] = 'Standard: Intervall (Tage) zwischen Terminen';

$string['cfg_defaultdurationdays'] = 'Standard: Dauer je Termin (Tage)';

$string['cfg_defaultvmstarthour'] = 'Standard: Startzeit Vormittag (Stunde)';

$string['cfg_defaultnmstarthour'] = 'Standard: Startzeit Nachmittag (Stunde)';

$string['cfg_defaultcapacitymin'] = 'Standard: Mindestteilnehmerzahl';

$string['cfg_defaultcapacitymax'] = 'Standard: Maximale Teilnehmerzahl';

$string['cfg_defaultmaxbookingsperuser'] = 'Standard: Max. Buchungen pro Nutzer/in';





$string['cfg_heading_defaults'] = 'Einstellungen – Workshop‑Anmeldung';

$string['cfg_heading_defaults_desc'] = 'Systemweite Voreinstellungen für neue Aktivitäten und Sessions (können je Aktivität überschrieben werden).';



$string['cfg_defaultbookopenoffsetdays_desc'] = 'Wieviele Tage vor Beginn öffnet die Anmeldung standardmäßig?';

$string['cfg_defaultbookcloseoffsetdays_desc'] = 'Wieviele Tage vor Beginn schließt die Anmeldung standardmäßig?';



$string['cfg_defaultrecurenabled_desc'] = 'Bei neuen Aktivitäten Serientermine standardmäßig aktivieren.';

$string['cfg_defaultrecurcount_desc'] = 'Wie viele Termine soll eine Serie standardmäßig enthalten?';

$string['cfg_defaultrecurintervaldays_desc'] = 'Abstand in Tagen zwischen zwei Terminen der Serie.';



$string['cfg_defaultdurationdays_desc'] = 'Dauer in Stunden pro Termin (Standardvorgabe).';

$string['cfg_defaultvmstarthour_desc'] = 'Voreinstellung für Startzeit am Vormittag (Stunde, 24h).';

$string['cfg_defaultnmstarthour_desc'] = 'Voreinstellung für Startzeit am Nachmittag (Stunde, 24h).';



$string['cfg_defaultcapacitymin_desc'] = 'Mindestanzahl Teilnehmende pro Termin (0 = keine Untergrenze).';

$string['cfg_defaultcapacitymax_desc'] = 'Maximale Anzahl Teilnehmende pro Termin.';



$string['cfg_defaultmaxbookingsperuser_desc'] = 'Wie viele Termine darf eine Person parallel buchen (Standardwert)?';





$string['workshopbooking:manage'] = 'Buchungen verwalten';



$string['workshopbooking:signup'] = 'An Workshops anmelden';



$string['workshopbooking:view'] = 'Workshop-Ansicht aufrufen';



$string['stats'] = 'Statistik';
$string['stats_heading'] = 'Statistik: {$a}';
$string['stats_range'] = 'Zeitraum';
$string['stats_bookings_trend'] = 'Buchungen im Zeitverlauf';
$string['stats_fillrate'] = 'Auslastung pro Termin';
$string['stats_fillrate_series'] = 'Auslastung (%)';
$string['stats_leadtime'] = 'Vorlaufzeit der Anmeldungen (Tage)';
$string['stats_leadtime_series'] = 'Anmeldungen';
$string['stats_cancellation_rate'] = 'Stornoquote';
$string['stats_fill'] = 'Auslastung';
$string['session'] = 'Termin';
$string['cancelled'] = 'Storniert';


$string['filter'] = 'Filtern';
$string['filter_category'] = 'Kursbereich';
$string['filter_cohort'] = 'Globale Gruppe';
$string['filter_course'] = 'Kurs';
$string['filter_group'] = 'Kursgruppe';
$string['filter_user'] = 'Nutzer/in';
$string['note_maxreached'] = 'Max. Anzahl bestätigter Anmeldungen erreicht – neue Anmeldungen bleiben vorerst ausstehend.';
$string['note_maxreached_admin'] = 'Diese/r Teilnehmer/in hat bereits die maximale Anzahl bestätigter Anmeldungen erreicht.';

$string['note_maxreached_block'] = 'Max. Anzahl bestätigter Anmeldungen erreicht – weitere Anmeldungen sind derzeit nicht möglich.';

$string['stats_adminmenu'] = 'Workshop‑Statistik';
