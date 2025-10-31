<?php
require(__DIR__ . '/../../config.php');
require_login();
require_once($CFG->libdir . '/pdflib.php');

$month = optional_param('month', '', PARAM_RAW_TRIMMED);
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = userdate(time(), '%Y-%m');
}
list($y, $m) = array_map('intval', explode('-', $month));

$monthstart = make_timestamp($y, $m, 1, 0, 0, 0);
$monthend   = strtotime('+1 month', $monthstart);

$context = context_system::instance();
$PAGE->set_context($context);

global $DB, $USER;

$sql = "SELECT s.timestart, s.timeend, s.slot,
               w.name AS workshopname, c.fullname AS coursename,
               c.id AS courseid, cm.id AS cmid
          FROM {workshopbooking_booking} b
          JOIN {workshopbooking_session} s ON s.id = b.sessionid
          JOIN {workshopbooking} w ON w.id = s.workshopbookingid
          JOIN {course} c ON c.id = w.course
          JOIN {modules} m ON m.name = 'workshopbooking'
          JOIN {course_modules} cm ON cm.instance = w.id AND cm.course = c.id AND cm.module = m.id
         WHERE b.userid = :userid
           AND b.status = 1
           AND s.status <> 3
           AND s.timestart >= :start AND s.timestart < :end
           AND cm.deletioninprogress = 0
           AND cm.visible = 1
           AND c.visible = 1
      ORDER BY s.timestart ASC";
$params = ['userid' => $USER->id, 'start' => $monthstart, 'end' => $monthend];
$rows = $DB->get_records_sql($sql, $params);

// Runtime visibility filter.
$filtered = [];
foreach ($rows as $r) {
    $modinfo = get_fast_modinfo($r->courseid);
    if (empty($modinfo->cms[$r->cmid])) { continue; }
    $cm = $modinfo->cms[$r->cmid];
    if (!$cm->uservisible) { continue; }
    $filtered[] = $r;
}

// Build HTML
$monthlabel = userdate($monthstart, '%B %Y');
$username = fullname($USER);
$asof = userdate(time(), '%d.%m.%Y');
$table = '<h2 style="font-family: sans-serif;">' . get_string('pluginname', 'block_workshopbooking_monthly') . ' — ' . s($monthlabel) . '</h2>' .
         '<div style="font-family: sans-serif; font-size: 11px; color: #555; margin-bottom: 8px;">' .
         'Benutzer: ' . s($username) . ' &nbsp; | &nbsp; Stand: ' . s($asof) . '</div>';
$table .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="font-size:11px;">';
$table .= '<thead><tr style=\"background:#f0f0f0;\"><th align=\"left\">'. get_string('label_date', 'block_workshopbooking_monthly') . '</th><th align=\"left\">'. get_string('label_name', 'block_workshopbooking_monthly') . '</th><th align=\"left\">VM/NM</th></tr></thead><tbody>';

if (empty($filtered)) {
    $table .= '<tr><td colspan="2">' . get_string('nobookingsmonth', 'block_workshopbooking_monthly') . '</td></tr>';
} else {
    foreach ($filtered as $r) {
        $date = userdate($r->timestart, get_string('strftimedate', 'langconfig'));
        $name = format_string($r->coursename) . ' › ' . format_string($r->workshopname);
        $vmnm = !empty($r->slot) ? s(strtoupper(trim($r->slot))) : '';
$table .= '<tr><td>' . s($date) . '</td><td>' . s($name) . '</td><td>' . $vmnm . '</td></tr>';}
}
$table .= '</tbody></table>';

$pdf = new pdf();
$pdf->SetTitle(get_string('pluginname', 'block_workshopbooking_monthly') . ' - ' . $monthlabel);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->writeHTML($table, true, false, true, false, '');
$filename = 'meine-buchungen-' . $month . '.pdf';
$pdf->Output($filename, 'D'); // force download
