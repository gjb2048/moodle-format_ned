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
require_once('colourpreset_form.php');
require_once($CFG->dirroot.'/course/format/ned/lib.php');

$courseid = required_param('courseid', PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$process = optional_param('process', 0, PARAM_INT);

$courseformat = course_get_format($courseid);
$course = $courseformat->get_course();
require_login($course);

// Permission.
$coursecontext = context_course::instance($courseid);
require_capability('moodle/course:update', $coursecontext);

$PAGE->set_url('/course/format/ned/colourpreset_delete.php',
    array('delete' => $delete, 'courseid' => $courseid)
);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('course');
$title = get_string('delete');
$heading = $SITE->fullname;
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

// Breadcrumb.
$PAGE->navbar->add(get_string('pluginname', 'format_ned'));
$PAGE->navbar->add(get_string('settings'),
    new moodle_url('/course/format/ned/nedsettings.php', array('id' => $courseid))
);
$PAGE->navbar->add(get_string('colourpresets', 'format_ned'),
    new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid))
);
$PAGE->navbar->add($title);

global $DB;
if (!$toform = $DB->get_record('format_ned_colour', array('id' => $delete, 'predefined' => 0))) {
    redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)));
}

$colourpreset = $DB->get_record('format_ned_colour', array('id' => $delete, 'predefined' => 0), '*', MUST_EXIST);

if ($process) {
    require_sesskey();
    // Throws an exception if fails and thus following code won't run.
    $DB->delete_records('format_ned_colour', array('id' => $delete, 'predefined' => 0));

    // Update existing courses with the preset to the first default if they are using the deleted preset.
    if ($nedcourses = $DB->get_records('course', array('format' => 'ned'), null, 'id')) {
        foreach($nedcourses as $nedcourse) {
            $courseformat = course_get_format($nedcourse->id);
            $formatcolourpreset = $courseformat->get_setting('colourpreset');
            if (!empty($formatcolourpreset) && ($formatcolourpreset == $delete)) { // 0 is 'Moodle default'.
                $courseformat->reset_colourpreset();
            }
        }
    }

    redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)),
        get_string('successful', 'format_ned'), 1
    );
    die;
} else {
    echo $OUTPUT->header();
    echo html_writer::tag('h1', $title, array('class' => 'page-title'));
    echo $OUTPUT->confirm('<div><strong>'.
        get_string('colourpreset', 'format_ned').': </strong>'.$colourpreset->name.
        '<br><br>'.
        '</div>'.
        get_string('deleteconfirmmsg', 'format_ned').'<br><br>',
        new moodle_url('/course/format/ned/colourpreset_delete.php',
            array('courseid' => $courseid, 'delete' => $delete, 'process' => 1)
        ),
        new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid))
    );
    echo $OUTPUT->footer();
}