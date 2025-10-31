<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/edit_form.php');

class block_workshopbooking_monthly_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement('text', 'config_cohortidnumbers',
            get_string('config_cohortidnumbers', 'block_workshopbooking_monthly'));
        $mform->setType('config_cohortidnumbers', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('config_cohortidnumbers', 'config_cohortidnumbers', 'block_workshopbooking_monthly');

        $mform->addElement('advcheckbox', 'config_hidefornonmembers',
            get_string('config_hidefornonmembers', 'block_workshopbooking_monthly'),
            get_string('config_hidefornonmembers_desc', 'block_workshopbooking_monthly'));
        $mform->setDefault('config_hidefornonmembers', 1);
    }
}
