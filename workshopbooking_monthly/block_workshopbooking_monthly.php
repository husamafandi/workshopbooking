<?php
defined('MOODLE_INTERNAL') || die();

class block_workshopbooking_monthly extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_workshopbooking_monthly');
    }

    // Allow on site, dashboard and course pages.
    public function applicable_formats() {
        return ['site' => true, 'my' => true, 'course-view' => true];
    }

    // Instance config: cohort restriction helpers.
    private function user_in_allowed_cohorts(): bool {
        global $DB, $USER, $CFG;
        if (empty($this->config) || empty($this->config->cohortidnumbers)) {
            return true; // No restriction set.
        }
        require_once($CFG->dirroot . '/cohort/lib.php');
        $raw = core_text::strtolower(trim((string)$this->config->cohortidnumbers));
        $ids = preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($ids)) { return true; }
        foreach ($ids as $idnumber) {
            $cohortid = $DB->get_field('cohort', 'id', ['idnumber' => $idnumber]);
            if ($cohortid && cohort_is_member($cohortid, $USER->id)) {
                return true;
            }
        }
        return false;
    }

    private static function wsb_display_name(string $raw): string {
        $name = preg_replace('/\s*(?:-|—|–)?\s*(?:\(|\[)?\s*(?:VM|NM)\s*(?:\)|\])?\s*$/iu', '', $raw);
        return trim((string)$name);
    }

    private static function parse_workshopnames_map(?string $txt): array {
        $map = [];
        if (empty($txt)) { return $map; }
        $lines = preg_split('/\r?\n/', (string)$txt);
        foreach ($lines as $line) {
            if (trim($line) === '') { continue; }
            $parts = array_map('trim', explode(',', $line));
            $key = self::wsb_display_name((string)($parts[0] ?? ''));
            if ($key === '') { continue; }
            $room = ''; $desc='';
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

    private static function pretty_date(int $ts): string {
        $y1 = (int)userdate($ts, '%Y');
        $yc = (int)userdate(time(), '%Y');
        $fmt = ($y1 === $yc) ? '%A, %d. %B' : '%A, %d. %B, %Y';
        return userdate($ts, $fmt);
    }

    public function get_content() {
        global $DB, $USER, $OUTPUT, $PAGE;

        if (!$this->user_in_allowed_cohorts()) {
            $this->content = (object)['text' => '', 'footer' => ''];
            if (!empty($this->config->hidefornonmembers)) { return $this->content; }
        }

        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = (object)['text' => '', 'footer' => ''];

        $PAGE->requires->css(new moodle_url('/blocks/workshopbooking_monthly/styles.css'));

        // Month param for dashboard navigation.
        $month = optional_param('wbm_month', '', PARAM_RAW_TRIMMED); // 'YYYY-MM'
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = userdate(time(), '%Y-%m');
        }
        list($y, $m) = array_map('intval', explode('-', $month));

        $monthstart = make_timestamp($y, $m, 1, 0, 0, 0);
        $monthend   = strtotime('+1 month', $monthstart);
        $prevmonth  = userdate(strtotime('-1 month', $monthstart), '%Y-%m');
        $nextmonth  = userdate($monthend, '%Y-%m');

        $sql = "SELECT s.id AS sessionid, s.name AS sessionname, s.timestart, s.timeend, s.slot, s.workshopnames AS sws,
                       w.id AS instanceid, w.name AS workshopname, w.workshopnames AS iws,
                       cm.id AS cmid, c.id AS courseid, c.fullname AS coursename
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
        $records = $DB->get_records_sql($sql, $params);

        $instancemap = [];
        $flat = [];
        foreach ($records as $r) {
            // runtime visibility check
            $modinfo = get_fast_modinfo($r->courseid);
            if (empty($modinfo->cms[$r->cmid])) { continue; }
            $cm = $modinfo->cms[$r->cmid];
            if (!$cm->uservisible) { continue; }

            if (!isset($instancemap[$r->instanceid])) {
                $instancemap[$r->instanceid] = self::parse_workshopnames_map((string)($r->iws ?? ''));
            }
            $sessionmap = self::parse_workshopnames_map((string)($r->sws ?? ''));
            $map = $sessionmap + $instancemap[$r->instanceid];

            $displayname = self::wsb_display_name((string)($r->sessionname ?? $r->workshopname));
            $room = '';
            if (isset($map[$displayname])) { $room = (string)($map[$displayname]['room'] ?? ''); }
            else if (isset($map[core_text::strtoupper($displayname)])) { $room = (string)($map[core_text::strtoupper($displayname)]['room'] ?? ''); }

            $flat[] = [
                'name' => format_string($displayname),
                'date' => s(self::pretty_date((int)$r->timestart)),
                'room' => s($room !== '' ? $room : '-'),
                'url' => (new moodle_url('/mod/workshopbooking/view.php', ['id' => $r->cmid]))->out(false),
                'slot' => !empty($r->slot) ? s(strtoupper(trim($r->slot))) : ''
            ];
        }

        $prevurl = new moodle_url($PAGE->url, ['wbm_month' => $prevmonth]);
        $nexturl = new moodle_url($PAGE->url, ['wbm_month' => $nextmonth]);
        $pdfurl  = new moodle_url('/blocks/workshopbooking_monthly/export.php', ['month' => $month]);

        $data = [
            'monthlabel' => userdate($monthstart, '%B %Y'),
            'hasitems' => !empty($flat),
            'flat' => $flat,
            'showfooter' => true,
            'fullurl' => (new moodle_url('/blocks/workshopbooking_monthly/index.php', ['month' => $month]))->out(false),
            'viewfull' => get_string('viewfull', 'block_workshopbooking_monthly'),
            'emptymessage' => get_string('nobookingsmonth', 'block_workshopbooking_monthly'),
            'compact' => true,
            'prevurl' => $prevurl->out(false),
            'nexturl' => $nexturl->out(false),
            'pdfurl'  => $pdfurl->out(false),
            'emptyicon' => $OUTPUT->image_url('empty', 'block_workshopbooking_monthly')->out(false),
            'downloadpdf' => get_string('downloadpdf', 'block_workshopbooking_monthly'),
            'iconprev' => $OUTPUT->image_url('nav-prev', 'block_workshopbooking_monthly')->out(false),
            'iconnext' => $OUTPUT->image_url('nav-next', 'block_workshopbooking_monthly')->out(false),
        ];

        $this->content->text = $OUTPUT->render_from_template('block_workshopbooking_monthly/monthly', $data);
        return $this->content;
    }
}
