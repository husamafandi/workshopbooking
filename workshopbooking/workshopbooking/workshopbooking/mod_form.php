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
































require_once($CFG->dirroot.'/course/moodleform_mod.php');































class mod_workshopbooking_mod_form extends moodleform_mod {















    public function definition() {















        $mform = $this->_form;































        // Standard activity name.















        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);















        $mform->setType('name', PARAM_TEXT);















        $mform->addRule('name', null, 'required', null, 'client');































        // Description/editor.















        $this->standard_intro_elements();































        $mform->addElement('header', 'availabilityhdr', get_string('availability', 'mod_workshopbooking'));















        $mform->addElement('date_time_selector', 'signupstart', get_string('signupstart', 'mod_workshopbooking'), ['optional' => true]);















        $mform->addElement('date_time_selector', 'signupend', get_string('signupend', 'mod_workshopbooking'), ['optional' => true]);















// --- SimpleBooking: recurrence & limits ---







        $mform->addElement('header', 'recurhdr', get_string('recurhdr', 'mod_workshopbooking'));















        $mform->addElement('advcheckbox', 'recurenabled', get_string('recurenabled', 'mod_workshopbooking'));







        $mform->setDefault('recurenabled', 1);















        $mform->addElement('date_time_selector', 'recurstart', get_string('recurstart', 'mod_workshopbooking'));







        $mform->setDefault('recurstart', time() + 7*24*3600);















        $mform->addElement('text', 'recurcount', get_string('recurcount', 'mod_workshopbooking'));







        $mform->setType('recurcount', PARAM_INT);







        $mform->setDefault('recurcount', 12);















        $mform->addElement('text', 'recurintervaldays', get_string('recurintervaldays', 'mod_workshopbooking'));







        $mform->setType('recurintervaldays', PARAM_INT);







        $mform->setDefault('recurintervaldays', 14);















        $mform->addElement('text', 'durationdays', get_string('durationdays', 'mod_workshopbooking'));







        $mform->setType('durationdays', PARAM_INT);







        $mform->setDefault('durationdays', 14);















        $mform->addElement('text', 'vmstarthour', get_string('vmstarthour', 'mod_workshopbooking'));







        $mform->setType('vmstarthour', PARAM_INT);







        $mform->setDefault('vmstarthour', 8);















        $mform->addElement('text', 'nmstarthour', get_string('nmstarthour', 'mod_workshopbooking'));















        // Multiple workshops







        $mform->addElement('advcheckbox', 'multiworkshops', get_string('multiworkshops', 'mod_workshopbooking'));







        $mform->setDefault('multiworkshops', 0);







        $mform->addHelpButton('multiworkshops', 'multiworkshops', 'mod_workshopbooking');















        $mform->addElement('textarea', 'workshopnames', get_string('workshopnames', 'mod_workshopbooking'), 'rows="6" cols="60"');







        $mform->setType('workshopnames', PARAM_RAW);







        $mform->addHelpButton('workshopnames', 'workshopnames', 'mod_workshopbooking');







        $mform->hideIf('workshopnames', 'multiworkshops', 'notchecked');























        $mform->setType('nmstarthour', PARAM_INT);







        $mform->setDefault('nmstarthour', 13);















        $mform->addElement('text', 'capacitymin', get_string('capacitymin', 'mod_workshopbooking'));







        $mform->setType('capacitymin', PARAM_INT);







        $mform->setDefault('capacitymin', 10);















        $mform->addElement('text', 'capacitymax', get_string('capacitymax', 'mod_workshopbooking'));







        $mform->setType('capacitymax', PARAM_INT);







        $mform->setDefault('capacitymax', 20);















        $mform->addElement('text', 'maxbookingsperuser', get_string('maxbookingsperuser', 'mod_workshopbooking'));







        $mform->setType('maxbookingsperuser', PARAM_INT);







        $mform->setDefault('maxbookingsperuser', 5);















        $mform->addElement('text', 'bookopenoffsetdays', get_string('bookopenoffsetdays', 'mod_workshopbooking'));







        $mform->setType('bookopenoffsetdays', PARAM_INT);







        $mform->setDefault('bookopenoffsetdays', 7);















        $mform->addElement('text', 'bookcloseoffsetdays', get_string('bookcloseoffsetdays', 'mod_workshopbooking'));







        $mform->setType('bookcloseoffsetdays', PARAM_INT);







        $mform->setDefault('bookcloseoffsetdays', 1);















        $this->standard_coursemodule_elements();















        



        // Apply global defaults from admin settings, if present.



        $config = get_config('mod_workshopbooking');



        if ($config) {



            if (isset($config->defaultbookopenoffsetdays)) { $mform->setDefault('bookopenoffsetdays', (int)$config->defaultbookopenoffsetdays); }



            if (isset($config->defaultbookcloseoffsetdays)) { $mform->setDefault('bookcloseoffsetdays', (int)$config->defaultbookcloseoffsetdays); }



            if (isset($config->defaultrecurenabled)) { $mform->setDefault('recurenabled', (int)$config->defaultrecurenabled); }



            if (isset($config->defaultrecurstart)) { $mform->setDefault('recurstart', (int)$config->defaultrecurstart); } // optional



            if (isset($config->defaultrecurcount)) { $mform->setDefault('recurcount', (int)$config->defaultrecurcount); }



            if (isset($config->defaultrecurintervaldays)) { $mform->setDefault('recurintervaldays', (int)$config->defaultrecurintervaldays); }



            if (isset($config->defaultdurationdays)) { $mform->setDefault('durationdays', (int)$config->defaultdurationdays); }



            if (isset($config->defaultvmstarthour)) { $mform->setDefault('vmstarthour', (int)$config->defaultvmstarthour); }



            if (isset($config->defaultnmstarthour)) { $mform->setDefault('nmstarthour', (int)$config->defaultnmstarthour); }



            if (isset($config->defaultcapacitymin)) { $mform->setDefault('capacitymin', (int)$config->defaultcapacitymin); }



            if (isset($config->defaultcapacitymax)) { $mform->setDefault('capacitymax', (int)$config->defaultcapacitymax); }



            if (isset($config->defaultmaxbookingsperuser)) { $mform->setDefault('maxbookingsperuser', (int)$config->defaultmaxbookingsperuser); }



        }











        $this->add_action_buttons();















    }















}















