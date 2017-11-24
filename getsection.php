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
 * @package    format_ned
 * @subpackage NED
 * @copyright  NED {@link http://ned.ca}
 * @author     NED {@link http://ned.ca}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @developer  G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 */

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../lib.php');

// Check access.
if (!confirm_sesskey()) {
    header('HTTP/1.1 403 Forbidden');
    echo get_string('invalidsesskey', 'error');
    die;
}

// Really they are required but want to validate and give own error message.
$courseid = optional_param('courseid', 0, PARAM_INT);
$sectionno = optional_param('sectionno', -1, PARAM_INT);
if (($courseid > 0) && ($sectionno > -1)) {
    header('HTTP/1.1 200 OK');

    global $PAGE;
    $PAGE->set_context(context_course::instance($courseid));
    $course = course_get_format($courseid)->get_course();
    $renderer = $PAGE->get_renderer('format_ned');
    echo $renderer->get_section($course, $sectionno);
} else {
    header('HTTP/1.1 400 Bad Request');
    echo 'Bad Request';
}
