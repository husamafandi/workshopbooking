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






namespace mod_workshopbooking\local;





defined('MOODLE_INTERNAL') || die();











use stdClass;





use moodle_url;





use context_module;





use core_date;











class bookingmanager {





    public static function user_confirmed_count_global(int $userid, int $cmid): int {





        global $DB;





        // Count confirmed bookings across all sessions for this activity only.





        $cm = get_coursemodule_from_id('workshopbooking', $cmid, 0, false, MUST_EXIST);





        $sessions = $DB->get_records('workshopbooking_session', ['workshopbookingid' => $cm->instance], '', 'id');





        if (empty($sessions)) { return 0; }





        list($insql, $params) = $DB->get_in_or_equal(array_keys($sessions), SQL_PARAMS_NAMED);





        $params['userid'] = $userid;





        $params['status'] = 1;





        return (int)$DB->count_records_select('workshopbooking_booking', "userid = :userid AND status = :status AND sessionid $insql", $params);





    }











    public static function session_counts(int $sessionid): array {





        global $DB;





        $confirmed = $DB->count_records('workshopbooking_booking', ['sessionid'=>$sessionid,'status'=>1]);





        $waitlist = $DB->count_records('workshopbooking_booking', ['sessionid'=>$sessionid,'status'=>2]);





        $pending  = $DB->count_records('workshopbooking_booking', ['sessionid'=>$sessionid,'status'=>0]);





        return compact('confirmed','waitlist','pending');





    }











    public static function create_series(stdClass $instance, int $cmid): int {





        global $DB;





        if (empty($instance->recurenabled) || empty($instance->recurstart) || empty($instance->recurcount)) {





            return 0;





        }





        $created = 0;





        $duration = max(1, (int)$instance->durationdays) * DAYSECS;





        $interval = max(1, (int)$instance->recurintervaldays) * DAYSECS;





        $openoff  = (int)$instance->bookopenoffsetdays * DAYSECS;





        $closeoff = (int)$instance->bookcloseoffsetdays * DAYSECS;











        // $t base moved into per-workshop loop





        





        // Determine workshop names list.





        











        // Build $workshops directly from $instance->workshopnames (if enabled).





        // Supported per-line formats:





        //   "Name"





        //   "Name, TT.MM.JJJJ"





        //   "Name, TT.MM.JJJJ HH:MM"    -> HH<12 overrides VM time; HH>=12 overrides NM time





        //   "Name, TT.MM.JJJJ von HH:MM"





        //   "Name, TT.MM.JJJJ, URL"     -> URL handled elsewhere for display; ignored here





        $workshops = [];





        if (!empty($instance->multiworkshops) && !empty($instance->workshopnames)) {





            $lines = preg_split('/\r\n|\r|\n/', trim($instance->workshopnames));





            foreach ($lines as $ln) {





                $ln = trim($ln);





                if ($ln === '') { continue; }





                $parts = array_map('trim', explode(',', $ln));





                $wname = $parts[0];





                $dayts = 0; $vmh = null; $vmm = 0; $nmh = null; $nmm = 0;











                if (count($parts) >= 2) {





                    // Try to find date and optional time in the second part





                    $p2 = $parts[1];





                    if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})(?:\s*(?:von\s*)?(\d{1,2}):(\d{2}))?$/u', $p2, $m)) {





                        $day = (int)$m[1]; $mon = (int)$m[2]; $yr = (int)$m[3];





                        $dayts = make_timestamp($yr, $mon, $day, 0, 0, 0);





                        if (!empty($m[4])) {





                            $hh = (int)$m[4]; $mm = (int)$m[5];





                            // <12 => morning override, >=12 => afternoon override





                            if ($hh < 12) { $vmh = $hh; $vmm = $mm; }





                            else { $nmh = $hh; $nmm = $mm; }





                        }





                    }





                }





                // Build entry; time overrides applied later when generating slots.





                $workshops[] = ['name'=>$wname, 'dayts'=>$dayts, 'vmh'=>$vmh, 'vmm'=>$vmm, 'nmh'=>$nmh, 'nmm'=>$nmm];





            }





        }





if (empty($workshops)) {





            // Fallback: use the activity name as single workshop.





            $workshops = [['name'=>$instance->name, 'customstart'=>0]];





        }





for ($i = 0; $i < (int)$instance->recurcount; $i++) {





            foreach ($workshops as $w) {





                $wname = $w['name'];





                $tbase = (!empty($w['dayts']) ? (int)$w['dayts'] : (int)$instance->recurstart);





                if (!empty($w['customstart'])) { $tbase = (int)$w['customstart']; }





                $t = $tbase + ($i * $interval);











                // Compute VM/NM start times for this workshop occurrence.





                $d = usergetdate($t);





$vmhour = (isset($w['vmh']) && $w['vmh'] !== null) ? (int)$w['vmh'] : (int)$instance->vmstarthour;





$vmmin = (isset($w['vmm']) ? (int)$w['vmm'] : 0);





$vmstart = make_timestamp($d['year'], $d['mon'], $d['mday'], $vmhour, $vmmin, 0);





                $nmhour = (isset($w['nmh']) && $w['nmh'] !== null) ? (int)$w['nmh'] : (int)$instance->nmstarthour;





$nmmin = (isset($w['nmm']) ? (int)$w['nmm'] : 0);





$nmstart = make_timestamp($d['year'], $d['mon'], $d['mday'], $nmhour, $nmmin, 0);











                foreach ([['VM', $vmstart], ['NM', $nmstart]] as $slotdef) {





                    [$slotlabel, $startts] = $slotdef;





                    $sess = (object)[





                        'workshopbookingid' => $instance->id,





                        'name' => $wname . " - $slotlabel",





                        'slot' => $slotlabel,





                        'timestart' => $startts,





                        'timeend' => $startts + $duration,





                        'bookingopen' => $startts - $openoff,





                        'bookingclose' => $startts - $closeoff,





                        'capacitymin' => (int)$instance->capacitymin ?: 10,





                        'capacitymax' => (int)$instance->capacitymax ?: 20,





                        'groupid' => 0,





                        'status' => 0,





                        'timecreated' => time()





                    ];





                    // Avoid duplicates for same timestart, slot AND name.





                    $exists = $DB->record_exists('workshopbooking_session', [





                        'workshopbookingid' => $sess->workshopbookingid,





                        'slot' => $sess->slot,





                        'timestart' => $sess->timestart,





                        'name' => $sess->name





                    ]);





                    if (!$exists) {





                        $DB->insert_record('workshopbooking_session', $sess);





                        $created++;





                    }





                }





            }





        }





return $created;





    }











    private static function combine_day_hour(int $dayts, int $hour): int {





        $d = usergetdate($dayts);





        // Compose timestamp at given hour in server timezone.





        return make_timestamp($d['year'], $d['mon'], $d['mday'], $hour, 0, 0);





    }











    public static function book(int $sessionid, int $userid, int $cmid, int $maxperuser): int {





        // Returns status set: 1 confirmed, 2 waitlist, 0 pending.





        $forcepending = false;

global $DB;





        $session = $DB->get_record('workshopbooking_session', ['id'=>$sessionid], '*', MUST_EXIST);





        $counts = self::session_counts($sessionid);











        // Enforce max per user (confirmed count only).





        if ($maxperuser > 0) {





            $current = self::user_confirmed_count_global($userid, $cmid);





            if ($current >= $maxperuser) {





                $forcepending = true;





            }





        }











        // Within booking window?





        $now = time();





        if ($session->bookingopen && $now < $session->bookingopen) {





            throw new \moodle_exception('bookingnotopen', 'mod_workshopbooking');





        }





        if ($session->bookingclose && $now > $session->bookingclose) {





            throw new \moodle_exception('bookingclosed', 'mod_workshopbooking');





        }











        // Determine status (confirm or waitlist).





        

        // Per-user confirmed cap check: if reached, force pending instead of throwing.

        if ($maxperuser > 0) {

            $cm = get_coursemodule_from_id('workshopbooking', $cmid, 0, false, MUST_EXIST);

            $uconf = (int)$DB->count_records_sql(

                "SELECT COUNT(1)

                   FROM {workshopbooking_booking} b

                   JOIN {workshopbooking_session} s ON s.id = b.sessionid

                  WHERE b.userid = :uid AND b.status = 1 AND s.workshopbookingid = :iid",

                ['uid'=>$userid, 'iid'=>$cm->instance]

            );

            if ($uconf >= $maxperuser) {

                $forcepending = true;

            }

        }

$status = ($counts['confirmed'] < $session->capacitymax) ? 1 : 2;

        if (!empty($forcepending)) { $status = 0; }













        // Upsert booking.





        $rec = $DB->get_record('workshopbooking_booking', ['sessionid'=>$sessionid,'userid'=>$userid]);





        if ($rec) {





            if ((int)$rec->status === 3) {





                $rec->status = $status;





                $DB->update_record('workshopbooking_booking', $rec);





            } else {





                // Already booked.





                return (int)$rec->status;





            }





        } else {





            $DB->insert_record('workshopbooking_booking', (object)[





                'sessionid'=>$sessionid,





                'userid'=>$userid,





                'status'=>$status,





                'timecreated'=>time()





            ]);





        }





        return $status;





    }











    public static function cancel(int $sessionid, int $userid): void {





        global $DB;





        if ($rec = $DB->get_record('workshopbooking_booking', ['sessionid'=>$sessionid,'userid'=>$userid])) {





            $rec->status = 3;





            $DB->update_record('workshopbooking_booking', $rec);





        }





    }











    public static function promote_waitlist(int $sessionid): int {





        global $DB;





        $session = $DB->get_record('workshopbooking_session', ['id'=>$sessionid], '*', MUST_EXIST);





        $counts = self::session_counts($sessionid);





        $slotsfree = max(0, $session->capacitymax - $counts['confirmed']);





        if ($slotsfree <= 0) { return 0; }





        $waiters = $DB->get_records('workshopbooking_booking', ['sessionid'=>$sessionid,'status'=>2], 'timecreated ASC', '*', 0, $slotsfree);





        $promoted = 0;





        foreach ($waiters as $w) {





            $w->status = 1;





            $DB->update_record('workshopbooking_booking', $w);





            $promoted++;





        }





        return $promoted;





    }





}





