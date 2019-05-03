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
 * EAD Meeting view.
 *
 * @package    mod_eadmeeting
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

ob_start ();

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$id = optional_param('id', 0, PARAM_INT);
$n  = optional_param('n', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('eadmeeting', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $eadmeeting = $DB->get_record('eadmeeting', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $eadmeeting = $DB->get_record('eadmeeting', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $eadmeeting->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('eadmeeting', $eadmeeting->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_eadmeeting\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $eadmeeting);
$event->trigger();

if (!defined('EADMEETING')) {
    require_once(__DIR__ . '/classes/eadmeeting.php');
}

$context     = context_module::instance ( $cm->id );
$isProfessor = has_capability ( 'moodle/course:update', $context );

$url = eadmeeting::getplayerurl ( $eadmeeting->name, $cm->id, $isProfessor );

header ( 'Location: ' . $url );
die();