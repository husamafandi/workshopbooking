<?php
require(__DIR__ . '/../../config.php');
require_login();

$month = optional_param('month', '', PARAM_RAW_TRIMMED); // 'YYYY-MM'
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = userdate(time(), '%Y-%m');
}
list($y, $m) = array_map('intval', explode('-', $month));

$monthstart = make_timestamp($y, $m, 1, 0, 0, 0);
$monthend   = strtotime('+1 month', $monthstart);
$prevmonth  = userdate(strtotime('-1 month', $monthstart), '%Y-%m');
$nextmonth  = userdate($monthend, '%Y-%m');

$PAGE->set_url(new moodle_url('/blocks/workshopbooking_monthly/index.php', ['month' => $month]));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'block_workshopbooking_monthly'));
$PAGE->set_heading(get_string('pluginname', 'block_workshopbooking_monthly'));
$PAGE->requires->css(new moodle_url('/blocks/workshopbooking_monthly/styles.css'));

global $DB, $USER, $OUTPUT;

$sql = "SELECT s.id AS sessionid, s.name AS sessionname, s.timestart, s.timeend, s.slot, s.workshopnames AS sws,
               w.id AS instanceid, w.name AS workshopname, w.workshopnames AS iws,
               c.id AS courseid, c.fullname AS coursename,
               cm.id AS cmid
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

function wsb_display_name_local(string $raw): string {
    return trim(preg_replace('/\s*(?:-|—|–)?\s*(?:\(|\[)?\s*(?:VM|NM)\s*(?:\)|\])?\s*$/iu', '', $raw));
}
function parse_workshopnames_map_local(?string $txt): array {
    $map = [];
    if (empty($txt)) { return $map; }
    $lines = preg_split('/\r?\n/', (string)$txt);
    foreach ($lines as $line) {
        if (trim($line) === '') { continue; }
        $parts = array_map('trim', explode(',', $line));
        $key = wsb_display_name_local((string)($parts[0] ?? ''));
        if ($key === '') { continue; }
        $room = ''; $desc = '';
        $p3 = isset($parts[3]) ? core_text::strtolower(trim($parts[3])) : '';
        if (in_array($p3, ['vm','nm','beides'])) {
            $desc = (string)($parts[4] ?? '');
            if (isset($parts[5])) { $room = (string)($parts[4] ?? ''); $desc = (string)($parts[5] ?? ''); }
        } else {
            $desc = (string)($parts[3] ?? '');
            if (isset($parts[4])) { $room = (string)($parts[3] ?? ''); $desc = (string)($parts[4] ?? ''); }
        }
        $kup = core_text::strtoupper($key);
        $map[$key] = ['room'=>$room, 'desc'=>$desc];
        if (!isset($map[$kup])) { $map[$kup] = $map[$key]; }
    }
    return $map;
}

$days = [];
$instancemap = [];
foreach ($rows as $r) {
    // runtime visibility check
    $modinfo = get_fast_modinfo($r->courseid);
    if (empty($modinfo->cms[$r->cmid])) { continue; }
    $cm = $modinfo->cms[$r->cmid];
    if (!$cm->uservisible) { continue; }

    if (!isset($instancemap[$r->instanceid])) {
        $instancemap[$r->instanceid] = parse_workshopnames_map_local((string)($r->iws ?? ''));
    }
    $sessionmap = parse_workshopnames_map_local((string)($r->sws ?? ''));
    $map = $sessionmap + $instancemap[$r->instanceid];

    $displayname = wsb_display_name_local((string)($r->sessionname ?? $r->workshopname));
    $room = '';
    if (isset($map[$displayname])) { $room = (string)($map[$displayname]['room'] ?? ''); }
    else if (isset($map[core_text::strtoupper($displayname)])) { $room = (string)($map[core_text::strtoupper($displayname)]['room'] ?? ''); }

    $daykey = userdate($r->timestart, '%Y-%m-%d');
    if (!isset($days[$daykey])) {
        $days[$daykey] = [
            'daylabel' => userdate($r->timestart, get_string('strftimedateshort', 'langconfig')),
            'entries' => []
        ];
    }
    $date = userdate($r->timestart, get_string('strftimedate', 'langconfig'));
    $formattime = get_string('strftimetime', 'langconfig');
    $time = userdate($r->timestart, $formattime);

    $days[$daykey]['entries'][] = [
        'name' => format_string($displayname),
        'date' => s($date),
        'time' => s($time),
        'room' => s($room !== '' ? $room : '-'),
        'url' => (new moodle_url('/mod/workshopbooking/view.php', ['id' => $r->cmid]))->out(false),
        'slot' => !empty($r->slot) ? s(strtoupper(trim($r->slot))) : ''
    ];
}

$template = [
    'monthlabel' => userdate($monthstart, '%B %Y'),
    'hasitems' => !empty($days),
    'days' => array_values($days),
    'showfooter' => false,
    'emptymessage' => get_string('nobookingsmonth', 'block_workshopbooking_monthly'),
    'prevurl' => (new moodle_url('/blocks/workshopbooking_monthly/index.php', ['month' => $prevmonth]))->out(false),
    'nexturl' => (new moodle_url('/blocks/workshopbooking_monthly/index.php', ['month' => $nextmonth]))->out(false),
    'compact' => false,
    'labels' => [
        'name' => get_string('label_name', 'block_workshopbooking_monthly'),
        'date' => 'Startdatum',
        'room' => get_string('label_room', 'block_workshopbooking_monthly'),
        'time' => get_string('time'),
    ],
    'pdfurl' => (new moodle_url('/blocks/workshopbooking_monthly/export.php', ['month' => $month]))->out(false),
    'downloadpdf' => get_string('downloadpdf', 'block_workshopbooking_monthly'),
    'emptyicon' => $OUTPUT->image_url('empty', 'block_workshopbooking_monthly')->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_workshopbooking_monthly/monthly', $template);
echo $OUTPUT->footer();
