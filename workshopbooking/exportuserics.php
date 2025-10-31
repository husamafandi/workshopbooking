<?php
// SPDX-License-Identifier: GPL-3.0
require(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'workshopbooking');
$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/workshopbooking:view', $context);

global $DB, $USER, $CFG, $SITE;

// Instanz laden
$instance = $DB->get_record('workshopbooking', ['id' => $cm->instance], '*', MUST_EXIST);

// Bestätigte Buchungen des aktuellen Nutzers
$sql = "SELECT s.*, b.id AS bookingid
          FROM {workshopbooking_booking} b
          JOIN {workshopbooking_session} s ON s.id = b.sessionid
         WHERE s.workshopbookingid = :iid AND b.userid = :uid AND b.status = 1
      ORDER BY s.timestart ASC";
$rows = $DB->get_records_sql($sql, ['iid' => $instance->id, 'uid' => $USER->id]);

// Output: ICS
$fn = clean_filename(format_string($course->shortname . '-' . $instance->name . '-meine-termine.ics', true));
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$fn.'"');

$prodid = '-//Workshopbooking//Moodle//DE';
$now = gmdate('Ymd\THis\Z');

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:$prodid\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "METHOD:PUBLISH\r\n";

$baseurl = (new moodle_url('/mod/workshopbooking/view.php', ['id'=>$cm->id]))->out(false);

foreach ($rows as $s) {
    $name = preg_replace('/\s*-\s*(VM|NM)$/', '', (string)($s->name ?? 'Workshop'));
    $title = $name;
    if (!empty($s->timestart)) {
        $title = $name . ' • ' . userdate($s->timestart, '%d.%m.%Y %H:%M');
        if (!empty($s->slot)) { $title .= ' ('.$s->slot.')'; }
    }
    $uid = "wb".$instance->id."-b".$s->id."-u".$USER->id."@".$CFG->wwwroot;
    $dtstart = gmdate('Ymd\THis\Z', (int)$s->timestart);
    $dur = (int)($s->timeduration ?? 0);
    $duration = $dur > 0 ? "PT{$dur}S" : "PT3600S";
    $desc = "Kurs: ".format_string($course->fullname, true)."\\n".$baseurl;

    echo "BEGIN:VEVENT\r\n";
    echo "UID:$uid\r\n";
    echo "DTSTAMP:$now\r\n";
    echo "DTSTART:$dtstart\r\n";
    echo "DURATION:$duration\r\n";
    echo "SUMMARY:".preg_replace('/[\r\n]+/',' ', $title)."\r\n";
    echo "DESCRIPTION:".preg_replace('/[\r\n]+/',' ', $desc)."\r\n";
    echo "END:VEVENT\r\n";
}

echo "END:VCALENDAR\r\n";
