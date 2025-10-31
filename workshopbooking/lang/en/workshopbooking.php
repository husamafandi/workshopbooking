<?php



$string['modulename'] = 'Workshop booking';



$string['modulenameplural'] = 'Workshop sign‑ups';



$string['pluginname'] = 'Workshop booking';



$string['pluginadministration'] = 'Workshop sign‑up administration';



$string['name'] = 'Name';



$string['availability'] = 'Availability';



$string['signupstart'] = 'Signup opens';



$string['signupend'] = 'Signup closes';



$string['from'] = 'From';



$string['to'] = 'to';



$string['maxparticipants'] = 'Maximum participants';



$string['maxparticipants_help'] = '0 means no limit.';



$string['signup'] = 'Sign up';



$string['cancel'] = 'Cancel signup';



$string['signedup'] = 'You are signed up.';



$string['canceled'] = 'Your signup was canceled.';



$string['cannot_signup'] = 'Signup not possible.';



$string['cannot_cancel'] = 'Cancelation not possible.';



$string['full'] = 'Fully booked';



$string['notopen'] = 'Signup is not open.';



$string['participants'] = 'Participants';



$string['signedupon'] = 'Signed up on';



$string['noparticipants'] = 'No participants yet.';



$string['limit_reached'] = 'Bookings: {$a}';



$string['limit_unlimited'] = 'Bookings: {$a} (unlimited)';



$string['privacy:metadata'] = 'Workshop sign‑up stores minimal signup data.';



$string['none'] = 'No activities found.';



$string['exportcsv'] = 'Export participants (CSV)';



$string['actions'] = 'Actions';







// Capabilities



$string['workshopbooking:viewparticipants'] = 'View participants list';



$string['workshopbooking:export'] = 'Export participant list';







// Help (only HTML link)



$string['modulename_help'] = '<p><strong>Workshop booking</strong> streamlines session management. 



Participants see all available sessions with date and time (“14 October 2025 from 08:00 to 10:00”) and can sign up or cancel with one click.</p>



<p><strong>Participant view:</strong> Only open sessions are offered; full or closed slots show a notice. After signing up a status and a cancel button are shown. Group filters and seat counts are reflected per session.</p>



<p><strong>Trainer view:</strong> Management tools include group filter, CSV export, confirm/unconfirm and remove booking. The attendee list respects the selected group; the CSV export uses the same filter.</p>



<p><strong>Recurring sessions:</strong> Configure <em>series start</em> (recurstart), <em>number of sessions</em> (recurcount) and <em>interval in days</em> (recurintervaldays). A button generates/updates the sessions automatically.</p>



<p><strong>Times & duration:</strong> Use <em>AM start hour</em> (vmstarthour) and <em>PM start hour</em> (nmstarthour). <em>Duration per session (hours)</em> defines the end time and the display uses “from … to …”.</p>



<p><strong>Capacity:</strong> Set <em>min/max seats</em> (capacitymin/capacitymax). When max is reached sign‑ups are blocked. Confirmed seats are counted separately when confirmation is used.</p>



<p><strong>Signup window (optional):</strong> With <em>opens</em> and <em>closes</em> you limit when bookings are possible.</p>



<p><strong>Groups:</strong> Sessions can be linked to a course group. Trainers can filter by group; export respects the selection. “Repair groups” resets orphaned group IDs to “All groups”.</p>



<p><strong>Capabilities:</strong> Participants need <code>mod/workshopbooking:signup</code>; trainers additionally need <code>mod/workshopbooking:manage</code> for actions, export and series.</p>



<p><strong>Compatibility:</strong> Tested with Moodle 4.1.</p>



<p><span style="color:#6b7280"><strong>Developer:</strong> Husam Afandi</span></p>';



$string['pluginname_help'] = $string['modulename_help'];







// Manage signups



$string['gotoworkshop'] = 'Open workshop';



$string['removesignup'] = 'Remove signup';



$string['signupremoved'] = 'Signup removed.';



$string['cannot_remove'] = 'Could not remove signup.';







$string['allgroups_local'] = 'All groups';











$string['multiworkshops'] = 'Multiple workshops';



$string['multiworkshops_help'] = 'When enabled, sessions are generated for each workshop name entered below. All limits/time settings apply to all workshops.';



$string['workshopnames'] = 'Workshop names';



$string['workshopnames_help'] = 'Multiple workshops — one entry per line. Full format with all fields:
Name, DD.MM.YYYY HH:MM, URL, am|pm|both, Room, Description

Example:
Excel Basics, 15.01.2026 09:00, https://example.com/excel, am, R 1.01, Intro | Please bring a laptop
Python Advanced, 16.01.2026 13:00, https://example.com/python, pm, R 2.12, Advanced topics | Exercises with datasets
Presenting Compact, 20.01.2026 09:00, https://example.com/present, both, HS 3, Morning & afternoon as two slots | Certificate at the end
Privacy Update, 22.01.2026 09:00, https://example.com/gdpr, am, Online, Remote via Zoom | Link 24h in advance

Columns: 1) Name  2) Date/Time (optional)  3) URL (optional)  4) Mode am/pm/both (optional)  5) Room (optional)  6) Description (optional, line-break with \'|\' or \'\n\').';



$string['recurhdr'] = 'Recurrence & limits';



$string['recurenabled'] = 'Enable recurrence (generate sessions)';



$string['recurstart'] = 'Series start date';



$string['recurcount'] = 'Number of occurrences';



$string['recurintervaldays'] = 'Interval (days)';



$string['durationdays'] = 'Duration per session (hours)';



$string['vmstarthour'] = 'Morning start hour (VM)';



$string['nmstarthour'] = 'Afternoon start hour (NM)';



$string['capacitymin'] = 'Minimum participants per session';



$string['capacitymax'] = 'Maximum participants per session';



$string['maxbookingsperuser'] = 'Max confirmed workshops per user';



$string['bookopenoffsetdays'] = 'Booking opens (days before start)';



$string['bookcloseoffsetdays'] = 'Booking closes (days before start)';



$string['task_process_sessions'] = 'Process workshopbooking sessions (promote waitlist)';



$string['btn_generate_series'] = 'Generate/refresh 14-day series';



$string['nosessions'] = 'No sessions defined yet.';



$string['session'] = 'Session';



$string['dates'] = 'Dates';



$string['slot'] = 'Slot';



$string['capacity'] = 'Capacity';



$string['booking'] = 'Booking';



$string['signup'] = 'Sign up';



$string['signedup_confirmed'] = 'You are signed up (confirmed).';



$string['signedup_waitlist'] = 'You are on the waitlist.';



$string['status_booked'] = 'Booked';



$string['seriesgenerated'] = 'Series generated/refreshed.';



$string['maxbookingsreached'] = 'You have reached the maximum number of workshops.';



$string['bookingnotopen'] = 'Booking is not open yet.';



$string['bookingclosed'] = 'Booking is closed.';







$string['schemafixed'] = 'Database schema verified/repaired.';







$string['btn_participants'] = 'View participants';



$string['btn_exportcsv'] = 'Export CSV';



$string['participants_heading'] = 'Registered participants';



$string['col_session'] = 'Session';



$string['col_slot'] = 'Slot';



$string['col_date'] = 'Start';



$string['col_date_end'] = 'End';



$string['col_user'] = 'Participant';



$string['col_email'] = 'Email';



$string['col_userid'] = 'User ID';



$string['col_username'] = 'Username';



$string['col_created'] = 'Created';



$string['col_status'] = 'Status';



$string['status_confirmed'] = 'Confirmed';



$string['status_waitlist'] = 'Waitlist';











$string['filter_group'] = 'Course group';



$string['all_groups'] = 'All groups';



$string['apply_filter'] = 'Filter';



$string['csv_for_filter'] = 'Export CSV (this filter)';











$string['btn_groupfilter'] = 'Group filter';







$string['cfg_heading_defaults'] = 'Defaults';



$string['cfg_heading_defaults_desc'] = 'System-wide defaults used when creating new activities/sessions. Can be overridden per instance.';



$string['cfg_defaultbookopenoffsetdays'] = 'Default: Signup opens (days in advance)';



$string['cfg_defaultbookcloseoffsetdays'] = 'Default: Signup closes (days in advance)';



$string['cfg_defaultrecurenabled'] = 'Default: Enable recurrence';



$string['cfg_defaultrecurcount'] = 'Default: Number of occurrences';



$string['cfg_defaultrecurintervaldays'] = 'Default: Interval between occurrences (days)';



$string['cfg_defaultdurationdays'] = 'Default: Duration per session (days)';



$string['cfg_defaultvmstarthour'] = 'Default: Morning start hour';



$string['cfg_defaultnmstarthour'] = 'Default: Afternoon start hour';



$string['cfg_defaultcapacitymin'] = 'Default: Minimum participants';



$string['cfg_defaultcapacitymax'] = 'Default: Maximum participants';



$string['cfg_defaultmaxbookingsperuser'] = 'Default: Max bookings per user';











$string['cfg_heading_defaults'] = 'Settings – Workshop booking';



$string['cfg_heading_defaults_desc'] = 'System‑wide defaults for new activities and sessions (can be overridden per activity).';







$string['cfg_defaultbookopenoffsetdays_desc'] = 'How many days before the start the signup opens by default.';



$string['cfg_defaultbookcloseoffsetdays_desc'] = 'How many days before the start the signup closes by default.';







$string['cfg_defaultrecurenabled_desc'] = 'Enable recurrence by default for new activities.';



$string['cfg_defaultrecurcount_desc'] = 'Default number of occurrences in a series.';



$string['cfg_defaultrecurintervaldays_desc'] = 'Gap in days between two occurrences.';







$string['cfg_defaultdurationdays_desc'] = 'Duration in hours per session (default).';



$string['cfg_defaultvmstarthour_desc'] = 'Default morning start hour (24h).';



$string['cfg_defaultnmstarthour_desc'] = 'Default afternoon start hour (24h).';







$string['cfg_defaultcapacitymin_desc'] = 'Minimum participants per session (0 = no lower limit).';



$string['cfg_defaultcapacitymax_desc'] = 'Maximum participants per session.';







$string['cfg_defaultmaxbookingsperuser_desc'] = 'How many sessions a user may book in parallel (default).';











$string['workshopbooking:manage'] = 'Manage bookings';







$string['workshopbooking:signup'] = 'Sign up to workshops';







$string['workshopbooking:view'] = 'View workshop';







$string['stats'] = 'Statistics';

$string['stats_heading'] = 'Statistics: {$a}';

$string['stats_range'] = 'Date range';

$string['stats_bookings_trend'] = 'Bookings over time';

$string['stats_fillrate'] = 'Fill rate by session';

$string['stats_fillrate_series'] = 'Fill rate (%)';

$string['stats_leadtime'] = 'Lead time of signups (days)';

$string['stats_leadtime_series'] = 'Signups';

$string['stats_cancellation_rate'] = 'Cancellation rate';

$string['stats_fill'] = 'Fill';

$string['session'] = 'Session';

$string['cancelled'] = 'Cancelled';





$string['filter'] = 'Filter';

$string['filter_category'] = 'Course category';

$string['filter_cohort'] = 'Global cohort';

$string['filter_course'] = 'Course';

$string['filter_group'] = 'Course group';

$string['filter_user'] = 'User';

$string['note_maxreached'] = 'Maximum number of confirmed bookings reached – new signups will remain pending.';

$string['note_maxreached_admin'] = 'This user has already reached the maximum number of confirmed bookings.';



$string['note_maxreached_block'] = 'Maximum number of confirmed bookings reached – further signups are currently not allowed.';



$string['stats_adminmenu'] = 'Workshop statistics';

$string['btn_details'] = 'Details';

$string['details_about'] = 'Information';

$string['workshop_shortdesc'] = 'Short description';

$string['cta_goto_workshop'] = 'Go to workshop';

$string['reminders'] = 'Reminders';

$string['reminderenabled'] = 'Send reminder emails';

$string['reminderamount'] = 'Amount';

$string['reminderunit'] = 'Unit';

$string['remindersubject'] = 'Reminder subject';

$string['remindertemplate'] = 'Reminder message (placeholders allowed)';

$string['remindertemplate_help'] = 'Placeholders: [[fullname]], [[firstname]], [[workshopname]], [[sessiondate]], [[sessiontime]], [[coursefullname]], [[courseshortname]], [[courselink]].';

$string['mybookings'] = 'My bookings';

$string['downloadics'] = 'Download .ics';

$string['view_month'] = 'Month';

$string['view_week'] = 'Week';

$string['view_day'] = 'Day';

$string['jump_to'] = 'Jump to';

$string['slot_all'] = 'All slots';

$string['slot_vm'] = 'VM';

$string['slot_nm'] = 'NM';

$string['showmybookingsbutton'] = 'Show “My bookings” button';

$string['showmybookingsbutton_desc'] = 'Shows/hides the button on the main page. The page /mod/workshopbooking/mybookings.php remains directly accessible.';

$string['blockeddates'] = 'Booking blackout days';

$string['blockeddates_desc'] = "One date per line, format YYYY-MM-DD. Ranges with two dots: 2025-12-24..2026-01-06. Optional: add a label, e.g. 2025-12-25 | National Holiday. Lines starting with # are comments.";

$string['err_blockeddate'] = 'Bookings are blocked for this date (holiday/company closure).';

$string['blocked_short'] = 'Booking blocked';

$string['startlabel'] = 'Start:';

$string['durationlabel'] = 'Duration:';

$string['btn_lock'] = 'Block booking';

$string['btn_unlock'] = 'Allow booking';

$string['confirm_lock'] = 'Block booking for this session?';

$string['locked_badge'] = 'blocked';

$string['locked_text'] = 'Booking disabled.';

$string['lock_set'] = 'Session is now not bookable.';

$string['lock_unset'] = 'Session is bookable again.';
