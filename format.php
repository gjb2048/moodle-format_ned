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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Horrible backwards compatible parameter aliasing....
if ($ctopic = optional_param('ctopics', 0, PARAM_INT)) { // Collapsed Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $ctopic);
    debugging('Outdated collapsed topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($topic = optional_param('topic', 0, PARAM_INT)) { // Topics and Grid old section parameter.
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic / grid param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($week = optional_param('week', 0, PARAM_INT)) { // Weeks old section parameter.
    $url = $PAGE->url;
    $url->param('section', $week);
    debugging('Outdated week param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing....

$context = context_course::instance($course->id);
// Retrieve course format option fields and add them to the $course object.
$courseformat = course_get_format($course);

$formatdisplaysectionno = $courseformat->get_displaysection();
if (!empty($formatdisplaysectionno)) {
    $url = $PAGE->url;
    $url->param('section', $formatdisplaysectionno);
    $PAGE->set_url($url);
    $displaysection = $formatdisplaysectionno;
}

if ($courseformat->get_setting('sectionformat') >= 1) { // Framed sections.
    $formatcolourpreset = $courseformat->get_setting('colourpreset');
    if (!empty($formatcolourpreset)) { // 0 is 'Moodle default'.
        global $DB;
        if ($preset = $DB->get_record('format_ned_colour', array('id' => $formatcolourpreset))) {
            echo '<style type="text/css" media="screen">';
            echo '/* <![CDATA[ */';

            echo 'ul.ned-framedsections .section.main {';
            echo 'background-color: #'.$preset->framedsectionbgcolour.';';
            echo '}';

            echo '.ned-framedsections .section .header .sectionname,';
            echo '.ned-framedsections .section .header .sectionname a,';
            echo '.ned-framedsections .section .header .sectionname a:hover,';
            echo '.ned-framedsections .section .header .summary,';
            echo '.ned-framedsections .section .left,';
            echo '.ned-framedsections .section .right a.toggle-display,';
            echo '.ned-framedsections .section .right a.toggle-display:hover {';
            echo 'color: #'.$preset->framedsectionheadertxtcolour.';';
            echo '}';

            echo '.jsenabled .ned-framedsections .right .moodle-actionmenu[data-enhance] .toggle-display.textmenu .caret {';
            echo 'border-top-color: #'.$preset->framedsectionheadertxtcolour.';';
            echo '}';

            echo '/* ]]> */';
            echo '</style>';
        } /* else Should not happen as when presets are deleted then courses are updated, but in a
             multi-user environment then could happen if deleted at the same time as page load. */
    }
}

$course = $courseformat->get_course();

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

$renderer = $PAGE->get_renderer('format_ned');
$renderer->set_courseformat($courseformat);

if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
    $nedsettingsurl = new moodle_url('/course/format/ned/nedsettings.php', array('id' => $course->id));
    echo html_writer::link($nedsettingsurl,
        $OUTPUT->pix_icon('t/edit', get_string('editcoursesettings')),
        array('title' => get_string('editcoursesettings'), 'class' => 'nededitsection'));
}

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/ned/format.js');
