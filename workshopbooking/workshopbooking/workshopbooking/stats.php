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


// Statistik: Workshop-Anmeldung (fertige Datei)

// Sichtbar nur für Site-Admins. Unterstützt systemweite Ansicht oder per ?id=cmid für eine Aktivität.



require(__DIR__.'/../../config.php');

require_once($CFG->libdir.'/weblib.php');

require_once($CFG->dirroot.'/course/lib.php');

require_once($CFG->dirroot.'/group/lib.php');

require_once($CFG->dirroot.'/cohort/lib.php');



// Fallback-String-Helfer (verhindert [[key]] in der UI).

function wsbx_str(string $key, string $fallback) {

    $sm = get_string_manager();

    return $sm->string_exists($key, 'mod_workshopbooking') ? get_string($key, 'mod_workshopbooking') : $fallback;

}



// Parameter

$id       = optional_param('id', 0, PARAM_INT);                // cmid optional

$range    = optional_param('range', '', PARAM_ALPHANUMEXT);    // ''|1m|3m|6m|12m

$datefrom = optional_param('datefrom', '', PARAM_RAW_TRIMMED); // YYYY-mm-dd

$dateto   = optional_param('dateto',   '', PARAM_RAW_TRIMMED);



$catid    = optional_param('catid', 0, PARAM_INT);

$cohortid = optional_param('cohortid', 0, PARAM_INT);

$courseid = optional_param('courseid', 0, PARAM_INT);

$groupid  = optional_param('groupid', 0, PARAM_INT);

$userid   = optional_param('userid', 0, PARAM_INT);



// Kontext & Seite

if ($id) {

    $cm = get_coursemodule_from_id('workshopbooking', $id, 0, false, MUST_EXIST);

    $course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);

    require_login($course, false, $cm);

    $PAGE->set_context(context_module::instance($cm->id));

    $PAGE->set_url(new moodle_url('/mod/workshopbooking/stats.php', ['id'=>$cm->id]));

    $PAGE->set_heading(format_string($course->fullname));

} else {

    require_login();

    $PAGE->set_context(context_system::instance());

    $PAGE->set_url(new moodle_url('/mod/workshopbooking/stats.php'));

    $PAGE->set_heading(format_string($SITE->fullname));

}

$PAGE->set_pagelayout('report');



// Zugriff: nur Site-Admin

if (!is_siteadmin()) {

    print_error('nopermissions', 'error', '', 'stats');

}



// Überschrift/Titel robust (unterstützt Strings mit {$a})

$pluginname = get_string('pluginname', 'mod_workshopbooking');

$headingraw = wsbx_str('stats_heading', 'Statistik: {$a}');

$heading = (strpos($headingraw, '{$a}') !== false) ? str_replace('{$a}', $pluginname, $headingraw) : $headingraw;

$PAGE->set_title($heading);



// === Optionsdaten für Filter ===



// Kursbereiche

$catopts = \core_course_category::make_categories_list();

if (!is_array($catopts)) { $catopts = []; }

$catopts = ['0'=>get_string('all')] + $catopts;



// Kurse unter gewähltem Kursbereich (inkl. Unterkategorien)

$courseopts = ['0'=>get_string('all')];

$courseids_in_cat = [];

if ($catid) {

    $cat = $DB->get_record('course_categories', ['id'=>$catid], 'id, path', IGNORE_MISSING);

    if ($cat && !empty($cat->path)) {

        $like = $cat->path.'/%';

        $catids = $DB->get_fieldset_sql("SELECT id FROM {course_categories} WHERE id = :id OR path LIKE :like",

            ['id'=>$catid, 'like'=>$like]);

        if ($catids) {

            list($insql, $inparams) = $DB->get_in_or_equal($catids, SQL_PARAMS_NAMED);

            $courses = $DB->get_records_sql("SELECT id, fullname FROM {course} WHERE category $insql ORDER BY fullname ASC", $inparams);

            foreach ($courses as $c) {

                $courseopts[$c->id] = format_string($c->fullname);

            }

            $courseids_in_cat = array_map('intval', array_keys($courses));

        }

    }

} else if (!empty($cm->course)) {

    // Falls Aufruf aus einer Aktivität, diesen Kurs zusätzlich anbieten.

    $c = $DB->get_record('course', ['id'=>$cm->course], 'id, fullname', IGNORE_MISSING);

    if ($c) { $courseopts[$c->id] = format_string($c->fullname); }

}



// Gruppen (Kursgruppen)

$groupopts = ['0'=>get_string('all')];

if ($courseid) {

    $groups = $DB->get_records('groups', ['courseid'=>$courseid], 'name ASC', 'id, name');

    foreach ($groups as $g) { $groupopts[$g->id] = format_string($g->name); }

}



// Nutzer/innen in Gruppe

$useropts = ['0'=>get_string('all')];

if ($groupid) {

    $members = $DB->get_records_sql("SELECT u.id, u.firstname, u.lastname

                                       FROM {groups_members} gm

                                       JOIN {user} u ON u.id = gm.userid

                                      WHERE gm.groupid = :gid

                                   ORDER BY u.lastname, u.firstname", ['gid'=>$groupid]);

    foreach ($members as $u) { $useropts[$u->id] = fullname($u); }

}



// Globale Gruppen (Cohorts)

$cohortopts = ['0'=>get_string('all')];

$cohorts = $DB->get_records('cohort', null, 'name ASC', 'id, name');

foreach ($cohorts as $ch) { $cohortopts[$ch->id] = format_string($ch->name); }



// === Zeitraum bestimmen ===

$to = time();

if (!empty($datefrom) && !empty($dateto)) {

    $from = strtotime($datefrom.' 00:00:00');

    $to   = strtotime($dateto.' 23:59:59');

    if (!$from || !$to || $from > $to) { $from = strtotime('-3 months', time()); $to = time(); }

} else {

    switch ($range) {

        case '1m':  $from = strtotime('-1 month',  $to); break;

        case '3m':  $from = strtotime('-3 months', $to); break;

        case '6m':  $from = strtotime('-6 months', $to); break;

        case '12m': $from = strtotime('-12 months',$to); break;

        default:    $from = strtotime('-3 months', $to); // Standardfenster

    }

}



// Monatslabels (inkl. von/bis)

$labels = [];

$labelkeys = [];

$cur = usergetdate($from);

$curts = make_timestamp($cur['year'], $cur['mon'], 1);

while ($curts <= $to) {

    $labelkeys[] = userdate($curts, '%Y-%m');

    $labels[]    = userdate($curts, get_string('strftimemonthyear', 'core_langconfig'));

    $y = (int)date('Y', $curts);

    $m = (int)date('m', $curts) + 1;

    if ($m > 12) { $m = 1; $y++; }

    $curts = make_timestamp($y, $m, 1);

}



// === Datenabfrage ===

$where = ["b.timecreated BETWEEN :from AND :to"];

$params = ['from'=>$from, 'to'=>$to];

$joins  = [

    "JOIN {workshopbooking_session} s ON s.id = b.sessionid",

    "JOIN {workshopbooking} w ON w.id = s.workshopbookingid",

    "JOIN {course} c ON c.id = w.course"

];



if ($id && !$catid && !$courseid) { // Wenn von einer Instanz aufgerufen und kein Bereich/Kurs gewählt

    $where[] = "w.id = :instanceid"; $params['instanceid'] = $cm->instance;

}

if ($catid) {

    if ($courseids_in_cat) {

        list($insql, $inparams) = $DB->get_in_or_equal($courseids_in_cat, SQL_PARAMS_NAMED);

        $where[] = "w.course $insql"; $params += $inparams;

    } else {

        $where[] = "1=0";

    }

}

if ($courseid) { $where[] = "w.course = :courseid"; $params['courseid'] = $courseid; }

if ($cohortid) { $joins[] = "JOIN {cohort_members} chm ON chm.userid = b.userid AND chm.cohortid = :cohortid"; $params['cohortid'] = $cohortid; }

if ($groupid && $courseid) { $joins[] = "JOIN {groups_members} gm ON gm.userid = b.userid AND gm.groupid = :groupid"; $params['groupid'] = $groupid; }

if ($userid) { $where[] = "b.userid = :userid"; $params['userid'] = $userid; }



$sql = "

    SELECT b.id, b.sessionid, b.userid, b.status, b.timecreated,

           s.timestart, s.capacitymax, s.slot, w.id AS instanceid, w.course

      FROM {workshopbooking_booking} b

      ".implode("\n      ", $joins)."

     WHERE ".implode(" AND ", $where);



$bookings = $DB->get_records_sql($sql, $params);



// Zeitreihe

$series_confirmed = array_fill(0, count($labels), 0);

$series_pending   = array_fill(0, count($labels), 0);

$series_cancelled = array_fill(0, count($labels), 0);

$index_by_key = array_flip($labelkeys);



foreach ($bookings as $b) {

    $key = userdate($b->timecreated, '%Y-%m');

    if (!isset($index_by_key[$key])) { continue; }

    $i = $index_by_key[$key];

    if ((int)$b->status === 1)      { $series_confirmed[$i]++; }

    else if ((int)$b->status === 0) { $series_pending[$i]++; }

    else if ((int)$b->status === 3 || (int)$b->status === 4) { $series_cancelled[$i]++; }

}



// Auslastung pro Session (für Sessions, die in die Filter fallen)

$sessionids = array_unique(array_map(function($b){ return (int)$b->sessionid; }, $bookings));

$filllabels = []; $fillvalues = [];

if ($sessionids) {

    list($insql, $inparams) = $DB->get_in_or_equal($sessionids, SQL_PARAMS_NAMED);

    $sql2 = "SELECT s.id, s.name, s.capacitymax, s.timestart,

                    SUM(CASE WHEN b.status=1 THEN 1 ELSE 0 END) AS confirmed

               FROM {workshopbooking_session} s

          LEFT JOIN {workshopbooking_booking} b ON b.sessionid = s.id

              WHERE s.id $insql

           GROUP BY s.id, s.name, s.capacitymax, s.timestart

           ORDER BY s.timestart ASC";

    $rows = $DB->get_records_sql($sql2, $inparams);

    foreach ($rows as $r) {

        $cap = (int)($r->capacitymax ?? 0);

        $conf = (int)($r->confirmed ?? 0);

        $pct = ($cap > 0) ? round(($conf / $cap) * 100) : 0;

        $title = format_string($r->name ?? wsbx_str('pluginname','Workshop'));

        $title = preg_replace('/\s*-\s*(VM|NM)$/u', '', (string)$title);

        $filllabels[] = $title.' ('.userdate($r->timestart, get_string('strftimedate','core_langconfig')).')';

        $fillvalues[] = $pct;

    }

}



// Vorlaufzeit (nur bestätigte)

$leadlabels = ['0-1','2-7','8-30','31-90','90+'];

$leadvalues = array_fill(0, count($leadlabels), 0);

foreach ($bookings as $b) {

    if ((int)$b->status !== 1 || empty($b->timestart)) { continue; }

    $days = floor(($b->timestart - $b->timecreated) / DAYSECS);

    if ($days <= 1)      { $leadvalues[0]++; }

    else if ($days <= 7) { $leadvalues[1]++; }

    else if ($days <= 30){ $leadvalues[2]++; }

    else if ($days <= 90){ $leadvalues[3]++; }

    else                 { $leadvalues[4]++; }

}



// Stornoquote

$totconfirmed = array_sum($series_confirmed);

$totcancelled = array_sum($series_cancelled);



// Ausgabe

echo $OUTPUT->header();

echo $OUTPUT->heading($heading);



// Filter unter Überschrift (kompakt)

echo html_writer::start_div('mb-3');

echo html_writer::start_tag('form', ['method'=>'get', 'class'=>'form-inline flex-wrap']);

if ($id) { echo html_writer::empty_tag('input', ['type'=>'hidden','name'=>'id','value'=>$id]); }



$ctl = [];

$ctl[] = html_writer::span(

    html_writer::label(get_string('from'), 'datefrom', false, ['class'=>'mr-1']).

    html_writer::empty_tag('input', ['type'=>'date','name'=>'datefrom','value'=>($datefrom?:userdate($from, '%Y-%m-%d')),'class'=>'form-control form-control-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(get_string('to'), 'dateto', false, ['class'=>'mr-1']).

    html_writer::empty_tag('input', ['type'=>'date','name'=>'dateto','value'=>($dateto?:userdate($to, '%Y-%m-%d')),'class'=>'form-control form-control-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('stats_range','Zeitraum'), 'range', false, ['class'=>'mr-1']).

    html_writer::select(['' => 'Bitte auswählen','1m'=>'1 Monat','3m'=>'3 Monate','6m'=>'6 Monate','12m'=>'1 Jahr'], 'range', (!empty($datefrom)&&!empty($dateto)) ? '' : $range, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('filter_category','Kursbereich'), 'catid', false, ['class'=>'mr-1']).

    html_writer::select($catopts, 'catid', $catid, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('filter_cohort','Globale Gruppe'), 'cohortid', false, ['class'=>'mr-1']).

    html_writer::select($cohortopts, 'cohortid', $cohortid, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('filter_course','Kurs'), 'courseid', false, ['class'=>'mr-1']).

    html_writer::select($courseopts, 'courseid', $courseid, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('filter_group','Kursgruppe'), 'groupid', false, ['class'=>'mr-1']).

    html_writer::select($groupopts, 'groupid', $groupid, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);

$ctl[] = html_writer::span(

    html_writer::label(wsbx_str('filter_user','Nutzer/in'), 'userid', false, ['class'=>'mr-1']).

    html_writer::select($useropts, 'userid', $userid, false, ['class'=>'custom-select custom-select-sm']),

    'mr-3 mb-2'

);



$btns = html_writer::tag('button', wsbx_str('filter','Filtern'), ['class'=>'btn btn-primary btn-sm mr-2', 'type'=>'submit']);

$reseturl = new moodle_url('/mod/workshopbooking/stats.php', $id?['id'=>$id]:[]);

$btns .= html_writer::link($reseturl, get_string('reset'), ['class'=>'btn btn-secondary btn-sm']);

$ctl[] = html_writer::span($btns, 'mb-2');



echo implode('', $ctl);

echo html_writer::end_tag('form');

echo html_writer::end_div();



// Diagramm 1: Zeitverlauf

$chart1 = new core\chart_line();

$chart1->set_title(wsbx_str('stats_bookings_trend','Buchungen im Zeitverlauf'));

$chart1->set_labels($labels);

$chart1->add_series(new core\chart_series(wsbx_str('status_confirmed','Bestätigt'), array_values($series_confirmed)));

$chart1->add_series(new core\chart_series(wsbx_str('pending','Ausstehend'), array_values($series_pending)));

$chart1->add_series(new core\chart_series(wsbx_str('cancelled','Storniert'), array_values($series_cancelled)));

echo $OUTPUT->render($chart1);



// Diagramm 2: Auslastung je Termin

if (!empty($filllabels)) {

    $chart2 = new core\chart_bar();

    $chart2->set_title(wsbx_str('stats_fillrate','Auslastung pro Termin'));

    $chart2->set_labels($filllabels);

    $chart2->add_series(new core\chart_series(wsbx_str('stats_fillrate_series','Auslastung (%)'), $fillvalues));

    echo $OUTPUT->render($chart2);

}



// Diagramm 3: Vorlaufzeit-Buckets

$chart3 = new core\chart_bar();

$chart3->set_title(wsbx_str('stats_leadtime','Vorlaufzeit der Anmeldungen'));

$chart3->set_labels($leadlabels);

$chart3->add_series(new core\chart_series(wsbx_str('stats_leadtime_series','Anzahl'), $leadvalues));

echo $OUTPUT->render($chart3);



// Diagramm 4: Stornoquote

$chart4 = new core\chart_pie();

$chart4->set_title(wsbx_str('stats_cancellation_rate','Stornoquote'));

$chart4->add_series(new core\chart_series(wsbx_str('stats_cancellation_rate','Stornoquote'), [$totconfirmed, $totcancelled]));

$chart4->set_labels([wsbx_str('status_confirmed','Bestätigt'), wsbx_str('cancelled','Storniert')]);

echo $OUTPUT->render($chart4);



echo $OUTPUT->footer();

