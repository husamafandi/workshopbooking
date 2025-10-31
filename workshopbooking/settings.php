<?php



defined('MOODLE_INTERNAL') || die();







if ($ADMIN->fulltree) {



    $settings->add(new admin_setting_heading(



        'mod_workshopbooking/defaults',



        get_string('cfg_heading_defaults', 'mod_workshopbooking'),



        '' // description intentionally empty



    ));







    // Signup window.



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultbookopenoffsetdays',



        get_string('bookopenoffsetdays', 'mod_workshopbooking'),



        '',



        7, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultbookcloseoffsetdays',



        get_string('bookcloseoffsetdays', 'mod_workshopbooking'),



        '',



        2, PARAM_INT



    ));







    // Recurrence.



    $settings->add(new admin_setting_configcheckbox(



        'mod_workshopbooking/defaultrecurenabled',



        get_string('recurenabled', 'mod_workshopbooking'),



        '',



        0



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultrecurcount',



        get_string('recurcount', 'mod_workshopbooking'),



        '',



        6, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultrecurintervaldays',



        get_string('recurintervaldays', 'mod_workshopbooking'),



        '',



        14, PARAM_INT



    ));







    // Session defaults.



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultdurationdays',



        get_string('durationdays', 'mod_workshopbooking'),



        '',



        5, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultvmstarthour',



        get_string('vmstarthour', 'mod_workshopbooking'),



        '',



        8, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultnmstarthour',



        get_string('nmstarthour', 'mod_workshopbooking'),



        '',



        13, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultcapacitymin',



        get_string('capacitymin', 'mod_workshopbooking'),



        '',



        10, PARAM_INT



    ));



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultcapacitymax',



        get_string('capacitymax', 'mod_workshopbooking'),



        '',



        15, PARAM_INT



    ));







    // Per-user limit.



    $settings->add(new admin_setting_configtext(



        'mod_workshopbooking/defaultmaxbookingsperuser',



        get_string('maxbookingsperuser', 'mod_workshopbooking'),



        '',



        5, PARAM_INT



    ));



}



  // Footer-Hinweis (Link + Abstand).



$settings->add(new admin_setting_heading(



    'mod_workshopbooking/devcontactfooter',



    '',



    '<div style="font-size:9pt; color:#6c757d; margin-top:14px; margin-bottom:14px; line-height:1.6;">



        <strong style="color:#6c757d;">Entwickler</strong>: 



        <span style="color:#6c757d;">Husam Afandi</span>



        <span style="margin:0 8px; color:#6c757d;">|</span>



        <strong style="color:#6c757d;">Kontakt</strong>:



        <a style="color:#6c757d; text-decoration: underline;" 



           href="mailto:husam.afandi@icloud.com?subject=Ticket%20an%20den%20Support">



           Ticket an den Support schicken



        </a>



           </div>'



));







// Extra admin entry: system-wide Workshop statistics.



if ($hassiteconfig) {



    // Under Reports.



    $ADMIN->add('reports', new admin_externalpage('workshopbookingstats_admin',



        get_string('stats_adminmenu','mod_workshopbooking'),



        new moodle_url('/mod/workshopbooking/stats.php'),



        'moodle/site:config'));



    // Also add under Plugins → Activities for quick access.



    $ADMIN->add('modsettings', new admin_externalpage('workshopbookingstats_admin_mod',



        get_string('stats_adminmenu','mod_workshopbooking'),



        new moodle_url('/mod/workshopbooking/stats.php'),



        'moodle/site:config'));



}






if ($ADMIN->fulltree) {

    // Show/Hide "My bookings" button (default hidden).
    $settings->add(new admin_setting_configcheckbox(
        'mod_workshopbooking/showmybookingsbutton',
        get_string('showmybookingsbutton', 'mod_workshopbooking'),
        get_string('showmybookingsbutton_desc', 'mod_workshopbooking'),
        0
    ));

    // Blackout dates (holidays / company closure). One date per line, or ranges with two dots.
    $settings->add(new admin_setting_configtextarea(
        'mod_workshopbooking/blockeddates',
        get_string('blockeddates', 'mod_workshopbooking'),
        get_string('blockeddates_desc', 'mod_workshopbooking'),
        ''
    ));
}

