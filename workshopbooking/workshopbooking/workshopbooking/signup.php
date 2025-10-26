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







require(__DIR__.'/../../config.php');













$id = required_param('id', PARAM_INT);













// If sesskey missing, don't error; redirect to view with sesskey appended.






$cm = get_coursemodule_from_id('workshopbooking', $id, 0, false, MUST_EXIST);






require_login(null, false, $cm);













$url = new moodle_url('/mod/workshopbooking/view.php', ['id' => $id, 'action' => 'signup', 'sesskey' => sesskey()]);






redirect($url);






