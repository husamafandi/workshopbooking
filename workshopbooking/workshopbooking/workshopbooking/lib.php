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











