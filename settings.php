<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * EAD Meeting configuration settings.
 *
 * @package    mod_eadmeeting
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->libdir . "/resourcelib.php");

    $settings->add(new admin_setting_configtext('eadmeeting/url',
        get_string('url_title', 'eadmeeting'),
        get_string('url_desc', 'eadmeeting'), ''));

    $settings->add(new admin_setting_configtext('eadmeeting/token',
        get_string('token_title', 'eadmeeting'),
        get_string('token_desc', 'eadmeeting'), ''));

    $infofields = $DB->get_records('user_info_field');
    foreach ($infofields as $infofield) {
        $itensseguranca["profile_{$infofield->id}"] = $infofield->name;
    }
}
