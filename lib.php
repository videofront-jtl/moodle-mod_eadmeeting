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
 * eadmeeting plugin main library file.
 *
 * @package    mod_eadmeeting
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function eadmeeting_supports($feature) {

    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the eadmeeting into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $eadmeeting Submitted data from the form in mod_form.php
 * @param mod_eadmeeting_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted eadmeeting record
 */
function eadmeeting_add_instance(stdClass $eadmeeting, mod_eadmeeting_mod_form $mform = null) {
    global $DB;

    $eadmeeting->timecreated = time();

    $eadmeeting->id = $DB->insert_record('eadmeeting', $eadmeeting);

    if (!defined('EADMEETING')) {
        require_once(__DIR__ . '/classes/eadmeeting.php');
    }
    eadmeeting::create ( $eadmeeting->name, $eadmeeting->coursemodule );

    return $eadmeeting->id;
}

/**
 * Updates an instance of the eadmeeting in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $eadmeeting An object from the form in mod_form.php
 * @param mod_eadmeeting_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function eadmeeting_update_instance(stdClass $eadmeeting, mod_eadmeeting_mod_form $mform = null) {
    global $DB;

    $eadmeeting->timemodified = time();
    $eadmeeting->id = $eadmeeting->instance;

    $result = $DB->update_record('eadmeeting', $eadmeeting);

    if (!defined('EADMEETING')) {
        require_once(__DIR__ . '/classes/eadmeeting.php');
    }
    eadmeeting::create ( $eadmeeting->name, $eadmeeting->coursemodule );

    return $result;
}

/**
 * Removes an instance of the eadmeeting from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function eadmeeting_delete_instance($id) {
    global $DB;

    if (!$eadmeeting = $DB->get_record('eadmeeting', array('id' => $id))) {
        return false;
    }
    $cm = get_coursemodule_from_instance('eadmeeting', $eadmeeting->id, $eadmeeting->course, false, MUST_EXIST);

    if (!defined('EADMEETING')) {
        require_once(__DIR__ . '/classes/eadmeeting.php');
    }
    eadmeeting::delete ( $eadmeeting->name, $cm->id );


    $DB->delete_records('eadmeeting', array('id' => $eadmeeting->id));

    return true;
}
