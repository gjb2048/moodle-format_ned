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

$weareediting = $PAGE->user_is_editing();

$sectionformat = $courseformat->get_setting('sectionformat');
if ($sectionformat >= 1) { // Framed sections.
    global $DB;
    if ($sectionformat == 3) { // Framed sections + Formatted header.
        /* Build an array of sections with their colour preset value.  Any that are not '-1', the
           NED Default, as set above will need to be specified here. */
        static $shfrows = array(1 => 'sectionheaderformatone', 2 => 'sectionheaderformattwo', 3 => 'sectionheaderformatthree');
        $sectionheaderformats = $courseformat->get_setting('sectionheaderformats');
        $sectioncolourpresets = array(); // Indexed by colour preset.
        $numsections = $courseformat->get_last_section_number();
        $sectionno = 1;
        while ($sectionno <= $numsections) {
            $sectionformat = $courseformat->get_setting('sectionheaderformat', $sectionno);
            $sectioncolourpreset = $sectionheaderformats[$shfrows[$sectionformat['headerformat']]]['colourpreset'];
            if ($sectioncolourpreset != 0) { // Not Moodle Default.
                $sectioncolourpresets[$sectioncolourpreset] = $sectioncolourpreset;
            }
            $sectionno++;
        }
        // We now have an array of colour presets (or none!) that tell us which sections have chosen them.
        if (!empty($sectioncolourpresets)) {
            echo '<style type="text/css" media="screen">';
            echo '/* <![CDATA[ */';

            foreach ($sectioncolourpresets as $presetno) {
                if ($presetno == -1) {
                    // NED Default so see what the course colour preset is.
                    $formatcolourpreset = $courseformat->get_setting('colourpreset');
                    if (!empty($formatcolourpreset)) { // 0 is 'Moodle default'.
                        $sectionpreset = $DB->get_record('format_ned_colour', array('id' => $formatcolourpreset));
                    } else {
                        // Set to Moodle default so nothing to do.
                        continue;
                    }
                } else {
                    $sectionpreset = $DB->get_record('format_ned_colour', array('id' => $presetno));
                }
                /* Notes: If $sectionpreset is null then check that 'sectionheaderformats' in the 'course_format_options' table
                          in the database has not been corrupted and contains 'null's for the 'colourpreset'.
                          This can be caused by '$data[$shfrow.'colourpreset']' being 'null' in 'update_course_format_options()'
                          in lib.php.

                          li.colourpreset- is determined from section_header() and section_summary() in renderer.php.
                */

                if ($weareediting) {
                    echo 'ul.ned-framedsections li.colourpreset-'.$presetno.'.section.main .header,'.
                         'ul.ned-framedsections li.colourpreset-'.$presetno.'.section.main .footer {';
                } else {
                    echo 'ul.ned-framedsections li.colourpreset-'.$presetno.'.section.main {';
                }
                echo 'background-color: #'.$sectionpreset->framedsectionbgcolour.';';
                echo '}';

                if ($weareediting) {
                    echo 'ul.ned-framedsections li.colourpreset-'.$presetno.'.section.main .content {';
                    echo 'border-left-color: #'.$sectionpreset->framedsectionbgcolour.';';
                    echo 'border-right-color: #'.$sectionpreset->framedsectionbgcolour.';';
                    echo '}';
                }

                echo '.ned-framedsections li.colourpreset-'.$presetno.'.section .header {';
                echo 'color: #'.$sectionpreset->framedsectionheadertxtcolour.';';
                echo '}';

                echo 'body:not(.editing) .course-content ul.ned.ned-framedsections li.section.colourpreset-'.$presetno.' .left,'.
                     'body:not(.editing) .course-content ul.ned.ned-framedsections li.section.colourpreset-'.$presetno.' .right {';
                echo 'width: '.$sectionpreset->framedsectionborderwidth.'px;';
                echo '}';

                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main.colourpreset-'.$presetno.' .content {';
                echo 'margin: 0 '.$sectionpreset->framedsectionborderwidth.'px;';
                echo '}';

                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main.colourpreset-'.$presetno.' .header,'.
                     'body:not(.editing) .course-content ul.ned-framedsections .section.main.colourpreset-'.$presetno.' .footer,'.
                     'body:not(.editing) .course-content ul.ned-framedsections .section.main.colourpreset-'.$presetno.' .header .nedshfcolumnswithoutcontent {';
                echo 'min-height: '.$sectionpreset->framedsectionborderwidth.'px;';
                echo '}';
            }

            echo '/* ]]> */';
            echo '</style>';
        }
    } else {
        $formatcolourpreset = $courseformat->get_setting('colourpreset');
        if (!empty($formatcolourpreset)) { // 0 is 'Moodle default'.
            if ($preset = $DB->get_record('format_ned_colour', array('id' => $formatcolourpreset))) {
                echo '<style type="text/css" media="screen">';
                echo '/* <![CDATA[ */';

                if ($weareediting) {
                    echo 'ul.ned-framedsections .section.main .header,';
                    echo 'ul.ned-framedsections .section.main .footer {';
                    echo 'background-color: #'.$preset->framedsectionbgcolour.';';
                    echo '}';

                    echo 'ul.ned-framedsections .section.main .content {';
                    echo 'border-left-color: #'.$preset->framedsectionbgcolour.';';
                    echo 'border-right-color: #'.$preset->framedsectionbgcolour.';';
                    echo '}';
                } else {
                    echo 'ul.ned-framedsections .section.main {';
                    echo 'background-color: #'.$preset->framedsectionbgcolour.';';
                    echo '}';
                }

                echo '.ned-framedsections .section .header .sectionname,';
                echo '.ned-framedsections .section .header .sectionname a,';
                echo '.ned-framedsections .section .header .sectionname a:hover,';
                echo '.ned-framedsections .section .header .summary {';
                echo 'color: #'.$preset->framedsectionheadertxtcolour.';';
                echo '}';

                echo 'body:not(.editing) .course-content ul.ned.ned-framedsections li.section .left, ';
                echo 'body:not(.editing) .course-content ul.ned.ned-framedsections li.section .right {';
                echo 'width: '.$preset->framedsectionborderwidth.'px;';
                echo '}';
                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main .content {';
                echo 'margin: 0 '.$preset->framedsectionborderwidth.'px;';
                echo '}';
                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main .header, ';
                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main .footer, ';
                echo 'body:not(.editing) .course-content ul.ned-framedsections .section.main .header .nedshfcolumnswithoutcontent {';
                echo 'min-height: '.$preset->framedsectionborderwidth.'px;';
                echo '}';

                echo '/* ]]> */';
                echo '</style>';
            } /* else Should not happen as when presets are deleted then courses are updated, but in a
                 multi-user environment then could happen if deleted at the same time as page load. */
        }
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
$renderer->set_courseformat($courseformat, (!empty($displaysection)));

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/ned/format.js');
