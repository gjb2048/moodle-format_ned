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

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/format/ned/nedsettings_form.php');
require_once($CFG->dirroot.'/course/format/ned/lib.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/course/format/ned/nedsettings.php', array('id' => $id));
// Will be needed when colour added in: $PAGE->requires->js_call_amd('format_ned/tabsettings', 'init', array());.

$PAGE->requires->js_call_amd('format_ned/nedsettingsform', 'init', array());

if ($id) {
    if ($id == SITEID) {
        print_error('You cannot edit the site course using this form');
    }
} else {
    print_error('Course id must be specified');
}

$courseformat = course_get_format($id);
$course = $courseformat->get_course();
require_login($course);
$coursecontext = context_course::instance($course->id);
require_capability('moodle/course:update', $coursecontext);

// First create the form.
$data = $courseformat->get_settings();
$editform = new course_ned_edit_form(null,
    array('courseid' => $course->id), 'post', '', array('class' => 'ned_settings')
);

// Don't parse non-form course data.
unset($data->hiddensections);
unset($data->coursedisplay);
$editform->set_data($data);

if ($editform->is_cancelled()) {
    if (empty($course)) {
        redirect($CFG->wwwroot);
    } else {
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
} else if ($data = $editform->get_data()) {
    // Remove form data that is not course setting data.
    unset($data->id);
    unset($data->submitbutton);
    $courseformat->update_course_format_options($data);
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}

// Print the form.
$streditcoursesettings = get_string('editcoursesettings');

// Breadcrumb.
$PAGE->navbar->add(get_string('pluginname', 'format_ned'));
$PAGE->navbar->add(get_string('settings'));

$PAGE->set_title($streditcoursesettings);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditcoursesettings);

$editform->display();
echo $OUTPUT->footer();
