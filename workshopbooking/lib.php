<?php
/** Map form fields to DB fields safely (supports wsb_* and legacy names). */
function wsb_apply_reminder_fields(&$rec, $data) {
    if (!is_object($rec)) { $rec = new stdClass(); }
    $rec->reminderenabled = isset($data->wsb_reminderenabled) ? (int)$data->wsb_reminderenabled : (int)($data->reminderenabled ?? 0);
    $rec->reminderamount  = isset($data->wsb_reminderamount)  ? (int)$data->wsb_reminderamount  : (int)($data->reminderamount ?? 1);
    $rec->reminderunit    = isset($data->wsb_reminderunit)    ? (string)$data->wsb_reminderunit : (string)($data->reminderunit ?? 'day');
    $rec->remindersubject = isset($data->wsb_remindersubject) ? (string)$data->wsb_remindersubject : (string)($data->remindersubject ?? '');
    if (isset($data->wsb_remindertemplate) && is_array($data->wsb_remindertemplate)) {
        $rec->remindertemplate = $data->wsb_remindertemplate['text'] ?? '';
        $rec->remindertemplateformat = $data->wsb_remindertemplate['format'] ?? 1;
    } else if (isset($data->remindertemplate) && is_array($data->remindertemplate)) {
        $rec->remindertemplate = $data->remindertemplate['text'] ?? '';
        $rec->remindertemplateformat = $data->remindertemplate['format'] ?? 1;
    } else {
        if (!isset($rec->remindertemplate)) $rec->remindertemplate = '';
        if (!isset($rec->remindertemplateformat)) $rec->remindertemplateformat = 1;
    }
}
























defined('MOODLE_INTERNAL') || die();























function workshopbooking_supports($feature) {











    switch ($feature) {











        case FEATURE_MOD_INTRO: return true;











        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;











        case FEATURE_GRADE_HAS_GRADE: return false;











        case FEATURE_BACKUP_MOODLE2: return false;











        default: return null;











    }











}























function workshopbooking_add_instance($data, $mform = null) {





    global $DB;











    $now = time();





    $data->timemodified = $now;





    if (!isset($data->timecreated)) { $data->timecreated = $now; }











    // Defaults for new fields.





    foreach (['recurenabled'=>0,'recurstart'=>0,'recurcount'=>0,'recurintervaldays'=>14,'durationdays'=>14,





              'vmstarthour'=>8,'nmstarthour'=>13,'capacitymin'=>10,'capacitymax'=>20,





              'maxbookingsperuser'=>5,'bookopenoffsetdays'=>7,'bookcloseoffsetdays'=>1,'multiworkshops'=>0] as $k=>$def) {





        if (!isset($data->$k)) { $data->$k = $def; }





    }





    if (!isset($data->maxparticipants)) { $data->maxparticipants = 0; }





    if (!isset($data->workshopnames)) { $data->workshopnames = ''; }











    // Filter unknown columns to avoid DB write errors if upgrade not yet applied.





    $rec = workshopbooking_filter_instance_fields($data);











    $rec->id = $DB->insert_record('workshopbooking', $rec);





    return $rec->id;











}























function workshopbooking_update_instance($data, $mform = null) {





    global $DB;











    $now = time();





    $data->timemodified = $now;





    if (!isset($data->timecreated)) { $data->timecreated = $now; }











    // Defaults for new fields.





    foreach (['recurenabled'=>0,'recurstart'=>0,'recurcount'=>0,'recurintervaldays'=>14,'durationdays'=>14,





              'vmstarthour'=>8,'nmstarthour'=>13,'capacitymin'=>10,'capacitymax'=>20,





              'maxbookingsperuser'=>5,'bookopenoffsetdays'=>7,'bookcloseoffsetdays'=>1,'multiworkshops'=>0] as $k=>$def) {





        if (!isset($data->$k)) { $data->$k = $def; }





    }





    if (!isset($data->maxparticipants)) { $data->maxparticipants = 0; }





    if (!isset($data->workshopnames)) { $data->workshopnames = ''; }











    // Filter unknown columns to avoid DB write errors if upgrade not yet applied.





    $rec = workshopbooking_filter_instance_fields($data);











    $rec->id = $data->instance;





    return $DB->update_record('workshopbooking', $rec);











}























function workshopbooking_delete_instance($id) {











    global $DB;











    if (!$simple = $DB->get_record('workshopbooking', ['id' => $id])) {











        return false;











    }











    $DB->delete_records('workshopbooking_signups', ['workshopbookingid' => $simple->id]);











    $DB->delete_records('workshopbooking', ['id' => $simple->id]);











    return true;











}























function workshopbooking_cm_info_view(cm_info $cm) {











    // Custom cm info if needed.











}



































/**











 * Add an 'Export CSV' link to the activity settings navigation (visible to managers/teachers).











 *











 * @param settings_navigation $settingsnav











 * @param navigation_node $workshopbookingnode











 */











function workshopbooking_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $workshopbookingnode = null) {











    global $PAGE;











    if (empty($PAGE->cm)) { return; }











    $context = context_module::instance($PAGE->cm->id);











    if (has_capability('mod/workshopbooking:export', $context)) {











        $url = new moodle_url('/mod/workshopbooking/view.php', ['id' => $PAGE->cm->id, 'action' => 'exportcsv', 'sesskey' => sesskey()]);











        $workshopbookingnode->add(











            get_string('exportcsv', 'mod_workshopbooking'),











            $url,











            navigation_node::TYPE_SETTING,











            null,











            'mod_workshopbooking_exportcsv',











            new pix_icon('i/export', 'export')











        );











    }











}

















function workshopbooking_filter_instance_fields($data) {





    global $DB;





    $cols = $DB->get_columns('workshopbooking');





    $allowed = array_keys($cols);





    $out = new stdClass();





    foreach ($data as $k=>$v) {





        if (in_array($k, $allowed, true)) { $out->$k = $v; }





    }





    return $out;





}



















/**
 * Return label for a blackout day matching $timestart in user timezone.
 * Supports lines like:
 *   2025-12-25
 *   2025-12-24..2026-01-06 | Betriebsurlaub
 *   2025-12-26 | Nationalfeiertag
 * @param int $timestart
 * @return string Non-empty label if blocked, '' otherwise.
 */
function wsb_blocked_label(int $timestart): string {
    $list = (string) get_config('mod_workshopbooking', 'blockeddates');
    if (trim($list) === '') { return ''; }
    $d = usergetdate($timestart);
    $day = sprintf('%04d-%02d-%02d', (int)$d['year'], (int)$d['mon'], (int)$d['mday']);
    foreach (preg_split("/\r\n|\n|\r/", $list) as $line) {
        $line = trim($line);
        if ($line === '' || (isset($line[0]) && $line[0] === '#')) { continue; }
        $parts = explode('|', $line, 2);
        $datepart = trim($parts[0]);
        $label = isset($parts[1]) ? trim($parts[1]) : '';
        if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $datepart)) {
            if ($datepart === $day) { return $label !== '' ? $label : get_string('blocked_short','mod_workshopbooking'); }
        } elseif (preg_match('~^(\d{4}-\d{2}-\d{2})\.\.(\d{4}-\d{2}-\d{2})$~', $datepart, $m)) {
            $start = strtotime($m[1] . ' 00:00:00 ' . date_default_timezone_get());
            $end   = strtotime($m[2] . ' 23:59:59 ' . date_default_timezone_get());
            $ts    = make_timestamp((int)$d['year'], (int)$d['mon'], (int)$d['mday'], 0, 0, 0);
            if ($ts >= $start && $ts <= $end) { return $label !== '' ? $label : get_string('blocked_short','mod_workshopbooking'); }
        }
    }
    return '';
}
