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
 * Embed eadmeeting Content
 *
 * @package    mod_eadmeeting
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

ob_start ();
header ( 'Access-Control-Allow-Origin: *' );

require_once ( "../../config.php" );
require_once ( "locallib.php" );

global $PAGE, $DB, $CFG, $OUTPUT;

$token = optional_param ('user_status', false, PARAM_TEXT);
if ( $token && !isloggedin() ) {
    $external_tokens = $DB->get_record ( 'external_tokens', array( 'token' => $token ), '*', IGNORE_MISSING );

    if ( $external_tokens ) {
        $user = $DB->get_record ( 'user', array( 'id' => $external_tokens->userid ), '*', IGNORE_MISSING );
        complete_user_login ( $user );
    }
}

$id = required_param('id', PARAM_INT);

// Verify course context.
$cm = get_coursemodule_from_id('eadmeeting', $id);
if (!$cm) {
    print_error('invalidcoursemodule');
}
$course = $DB->get_record('course', array('id' => $cm->course));
if (!$course) {
    print_error('coursemisconf');
}

try {
    require_course_login($course, true, $cm, true, true);
} catch (Exception $e) {
    echo '<body style="margin:0">Erro de Sessão!</body>';
    return;
}
$context = context_module::instance($cm->id);
require_capability('mod/eadmeeting:view', $context);

echo $OUTPUT->header();

if ($eadmeeting->intro) {
    echo $OUTPUT->box(format_module_intro('eadmeeting', $eadmeeting, $cm->id), 'generalbox mod_introbox', 'eadmeetingintro');
}

if (!defined('EADMEETING')) {
    require_once(__DIR__ . '/classes/eadmeeting.php');
}
$url = eadmeeting::getplayerurl($eadmeeting->name, $cm->id,false);

header ( 'Location: ' . $url );
die();