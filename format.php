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

$sectionformat = $courseformat->get_setting('sectionformat');
if ($sectionformat >= 1) { // Framed sections.
    $formatcolourpreset = $courseformat->get_setting('colourpreset');
    if (!empty($formatcolourpreset)) { // 0 is 'Moodle default'.
        global $DB;
        if ($preset = $DB->get_record('format_ned_colour', array('id' => $formatcolourpreset))) {
            echo '<style type="text/css" media="screen">';
            echo '/* <![CDATA[ */';

            echo 'ul.ned-framedsections .section.main {';
            echo 'background-color: #'.$preset->framedsectionbgcolour.';';
            echo '}';

            if ($sectionformat == 3) { // Framed sections + Formatted header.
                echo '.ned-framedsections .section .header,';
            } else {
                echo '.ned-framedsections .section .header .sectionname,';
                echo '.ned-framedsections .section .header .sectionname a,';
                echo '.ned-framedsections .section .header .sectionname a:hover,';
                echo '.ned-framedsections .section .header .summary,';
            }
            echo '.ned-framedsections .section .left,';
            echo '.ned-framedsections .section .right a.toggle-display,';
            echo '.ned-framedsections .section .right a.toggle-display:hover,';
            echo '.ned-framedsections .section .right a.dropdown-toggle,';
            echo '.ned-framedsections .section .right a.dropdown-toggle:hover {';
            echo 'color: #'.$preset->framedsectionheadertxtcolour.';';
            echo '}';

            echo '.jsenabled .ned-framedsections .right .moodle-actionmenu[data-enhance] .toggle-display.textmenu .caret {';
            echo 'border-top-color: #'.$preset->framedsectionheadertxtcolour.';';
            echo '}';

            echo '/* ]]> */';
            echo '</style>';
        } /* else Should not happen as when presets are deleted then courses are updated, but in a
             multi-user environment then could happen if deleted at the same time as page load. */
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
                if ($sectioncolourpreset >= 0) { // Not NED Default.
                    if (empty($sectioncolourpresets[$sectioncolourpreset])) {
                        $sectioncolourpresets[$sectioncolourpreset] = array();
                    }
                    $sectioncolourpresets[$sectioncolourpreset][] = '#section-'.$sectionno; // Prefixing the CSS id selector here helps below.
                }
                $sectionno++;
            }
            /* We now have an array of colour presets (or none!) that tell us which sections have chosen them.
               Also $preset will contain a preset that we may need and don't need to fetch again from the database. */
            if (!empty($sectioncolourpresets)) {
                echo '<style type="text/css" media="screen">';
                echo '/* <![CDATA[ */';
                foreach ($sectioncolourpresets as $presetno => $sectionnos) {
                    if ($preset->id == $presetno) {
                        // No need for more CSS as the colour preset set for the section is the same as the course.
                        continue;
                    } else {
                        $sectionpreset = $DB->get_record('format_ned_colour', array('id' => $presetno));
                    }

                    $selectors = array();
                    foreach ($sectionnos as $sectionno) {
                        $selectors[] = 'ul.ned-framedsections '.$sectionno.'.section.main';
                    }
                    echo implode(',', $selectors).' {';
                    /* Note: If $sectionpreset is null then check that 'sectionheaderformats' in the 'course_format_options' table
                             in the database has not been corrupted and contains 'null's for the 'colourpreset'.
                             This can be caused by '$data[$shfrow.'colourpreset']' being 'null' in 'update_course_format_options()'
                             in lib.php. */
                    echo 'background-color: #'.$sectionpreset->framedsectionbgcolour.';';
                    echo '}';

                    $selectors = array();
                    foreach ($sectionnos as $sectionno) {
                        $selectors[] = '.ned-framedsections '.$sectionno.'.section .header,'.
                            '.ned-framedsections '.$sectionno.'.section .left,'.
                            '.ned-framedsections '.$sectionno.'.section .right a.toggle-display,'.
                            '.ned-framedsections '.$sectionno.'.section .right a.toggle-display:hover,';
                            '.ned-framedsections '.$sectionno.'.section .right a.dropdown-toggle,'.
                            '.ned-framedsections '.$sectionno.'.section .right a.dropdown-toggle:hover';
                    }
                    echo implode(',', $selectors).' {';
                    echo 'color: #'.$sectionpreset->framedsectionheadertxtcolour.';';
                    echo '}';

                    $selectors = array();
                    foreach ($sectionnos as $sectionno) {
                        $selectors[] = '.jsenabled .ned-framedsections '.$sectionno.' .right .moodle-actionmenu[data-enhance] .toggle-display.textmenu .caret';
                    }
                    echo implode(',', $selectors).' {';
                    echo 'border-top-color: #'.$sectionpreset->framedsectionheadertxtcolour.';';
                    echo '}';
                }
                echo '/* ]]> */';
                echo '</style>';
            }

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
$renderer->set_courseformat($courseformat);

if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
    $nedsettingsurl = new moodle_url('/course/format/ned/nedsettings.php', array('id' => $course->id));
    echo html_writer::link($nedsettingsurl,
        $OUTPUT->pix_icon('ned_icon', get_string('editnedformatsettings', 'format_ned'), 'format_ned'),
        array('title' => get_string('editnedformatsettings', 'format_ned'), 'class' => 'nededitsection'));
}

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/ned/format.js');
