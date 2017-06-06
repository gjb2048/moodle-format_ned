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
 * @copyright  Michael Gardener <mgardener@cissq.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/format/ned/tabsettings_form.php');
require_once($CFG->dirroot.'/course/format/ned/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$categoryid = optional_param('category', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/course/format/ned/tabsettings.php', array('id' => $id));
$PAGE->requires->js_call_amd('format_ned/tabsettings', 'init', array());

require_login();
if ($id) {
    if ($id == SITEID) {
        print_error('You cannot edit the site course using this form');
    }

    if (!$course = $DB->get_record('course', array('id' => $id))) {
        print_error('Course ID was incorrect');
    }
    require_login($course);
    $category = $DB->get_record('course_categories', array('id' => $course->category), '*', MUST_EXIST);
    $coursecontext = context_course::instance($course->id);
    require_capability('moodle/course:update', $coursecontext);
} else {
    require_login();
    print_error('Course id must be specified');
}

if ($delete && $DB->record_exists('format_ned_colour', array('id' => $delete, 'predefined' => 0))) {
    $DB->delete_records('format_ned_colour', array('id' => $delete, 'predefined' => 0));
    format_ned_update_course_setting('colourschema', 0);
}

$course = course_get_format($course)->get_course();


$data = new stdClass();
$data->courseid = $course->id;

$data->showtabs = format_ned_get_setting($data->courseid, 'showtabs');
$data->mainheading = format_ned_get_setting($data->courseid, 'mainheading');
$data->tabcontent = format_ned_get_setting($data->courseid, 'tabcontent');
$data->tabwidth = format_ned_get_setting($data->courseid, 'tabwidth');
$data->completiontracking = format_ned_get_setting($data->courseid, 'completiontracking');
$data->activitytrackingbackground = format_ned_get_setting($data->courseid, 'activitytrackingbackground');
$data->locationoftrackingicons = format_ned_get_setting($data->courseid, 'locationoftrackingicons');
$data->showorphaned = format_ned_get_setting($data->courseid, 'showorphaned');
$data->topicheading = format_ned_get_setting($data->courseid, 'topicheading');
$data->maxtabs = format_ned_get_setting($data->courseid, 'maxtabs');

$defaulttab = $DB->get_field('format_ned_config', 'value',
    array('courseid' => $course->id, 'variable' => 'defaulttab')
);

$completion = new completion_info($course);
if ((!$completion->is_enabled()) && $defaulttab == 'option2') {
    $data->defaulttab = 'option1';
} else {
    $data->defaulttab = ($defaulttab) ? $defaulttab : 'option1';
}

$data->colourschema = format_ned_get_setting($data->courseid, 'colourschema');
$data->topictoshow = format_ned_get_setting($data->courseid, 'topictoshow');
$data->showsection0 = format_ned_get_setting($data->courseid, 'showsection0');
$data->showonlysection0 = format_ned_get_setting($data->courseid, 'showonlysection0');
$data->defaulttabwhenset = time();

// First create the form.
$editform = new course_ned_edit_form(null,
    array('course' => $course, 'colourschema' => $data->colourschema), 'post', '', array('class' => 'ned_settings')
);

$editform->set_data($data);

if ($editform->is_cancelled()) {
    if (empty($course)) {
        redirect($CFG->wwwroot);
    } else {
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
} else if ($data = $editform->get_data()) {

    $variable = 'showsection0';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'showonlysection0';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'showtabs';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'mainheading';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'tabcontent';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'completiontracking';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'activitytrackingbackground';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'locationoftrackingicons';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'showorphaned';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'topicheading';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'maxtabs';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'defaulttab';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'topictoshow';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'defaulttabwhenset';
    format_ned_update_course_setting($variable, $data->$variable);

    $variable = 'colourschema';
    if (isset($schema->id)) {
        format_ned_update_course_setting($variable, $schema->id);
        $data->colourschema = $schema->id;
    } else {
        if (!empty($data->$variable)) {
            format_ned_update_course_setting($variable, $data->$variable);
        } else {
            format_ned_update_course_setting($variable, 0);
        }
    }

    unset($SESSION->G8_selected_week[$course->id]);
    redirect($CFG->wwwroot."/course/view.php?id=$course->id" );
}

// Print the form.
$site = get_site();
$streditcoursesettings = get_string("editcoursesettings");
if (!empty($course)) {
    // Breadcrumb.
    $PAGE->navbar->add(get_string('pluginname', 'format_ned'));
    $PAGE->navbar->add(get_string('settings', 'format_ned'));

    $title = $streditcoursesettings;
    $fullname = $course->fullname;
} else {
    $title = "";
    $fullname = $site->fullname;
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditcoursesettings);

$editform->display();
echo $OUTPUT->footer();
