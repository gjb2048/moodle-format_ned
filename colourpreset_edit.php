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

$add = optional_param('add', 0, PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$duplicate = optional_param('duplicate', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);

$courseformat = course_get_format($courseid);
$course = $courseformat->get_course();
require_login($course);

// Permission.
$coursecontext = context_course::instance($courseid);
require_capability('moodle/course:update', $coursecontext);

if ($duplicate) {
    if (!$preset = $DB->get_record('format_ned_colour', array('id' => $duplicate))) {
        redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)));
    }
    $preset->name = $preset->name.' '.get_string('duplicatewithbrackets', 'format_ned');
    $preset->predefined = 0;
    unset($preset->id);
    unset($preset->timemodified);
    $preset->timecreated = time();
    $presetid = $DB->insert_record('format_ned_colour', $preset);
    redirect(new moodle_url('/course/format/ned/colourpreset_edit.php', array('courseid' => $courseid, 'edit' => $presetid)));
}

$PAGE->https_required();

$thispageurl = new moodle_url('/course/format/ned/colourpreset_edit.php',
    array('courseid' => $courseid, 'edit' => $edit, 'add' => $add)
);

$PAGE->set_url($thispageurl);
$PAGE->set_pagelayout('course');
$PAGE->set_context($coursecontext);
$PAGE->verify_https_required();

$name = get_string('addedit', 'format_ned');
$title = get_string('addedit', 'format_ned');
$heading = $SITE->fullname;

// Breadcrumb.
$PAGE->navbar->add(get_string('pluginname', 'format_ned'));
$PAGE->navbar->add(get_string('settings'),
    new moodle_url('/course/format/ned/nedsettings.php', array('id' => $courseid))
);
$PAGE->navbar->add(get_string('colourpresets', 'format_ned'),
    new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid))
);
$PAGE->navbar->add($name);

$PAGE->set_title($title);
$PAGE->set_heading($heading);

$mform = new colourpreset_form();

if ($edit) {
    if (!$toform = $DB->get_record('format_ned_colour', array('id' => $edit, 'predefined' => 0))) {
        redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)));
    }
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)));
} else if ($fromform = $mform->get_data()) {
    $rec = new stdClass();
    $rec->name = $fromform->name;
    $rec->framedsectionbgcolour = $fromform->framedsectionbgcolour;
    $rec->framedsectionheadertxtcolour = $fromform->framedsectionheadertxtcolour;
    $rec->framedsectionborderwidth = $fromform->framedsectionborderwidth;

    if ($add) {
        $rec->timecreated = time();
        $rec->id = $DB->insert_record('format_ned_colour', $rec);
        redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)),
            get_string('successful', 'format_ned'), 0);
    } else {
        $rec->id = $fromform->edit;
        $rec->timemodified = time();
        $DB->update_record('format_ned_colour', $rec);
        redirect(new moodle_url('/course/format/ned/colourpreset.php', array('courseid' => $courseid)),
            get_string('successful', 'format_ned'), 0);
    }
    exit;
}

echo $OUTPUT->header();

if ($edit) {
    $toform->edit = $edit;
} else {
    $toform = new stdClass();
    $toform->add = $add;
}
$toform->courseid = $courseid;
$mform->set_data($toform);

$mform->display();

echo $OUTPUT->footer();