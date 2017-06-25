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
 * @copyright  NED {@link http://ned.ca} 2017
 * @author     NED {@link http://ned.ca}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @developer  G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 */

defined('MOODLE_INTERNAL') || die;

class format_ned_course_renderer extends core_course_renderer {

    private $activitytrackingbackground;
    private $locationoftrackingicons;

    /**
     * Set the state of settings needed to make decisions upon.
     *
     * @param bool $activitytrackingbackground Add backgrounds to activites that have completion tracking.
     * @param string $locationoftrackingicons Where the location and type of completion tracking icons.
     */
    public function set_settings($activitytrackingbackground, $locationoftrackingicons) {
        $this->activitytrackingbackground = $activitytrackingbackground;
        $this->locationoftrackingicons = $locationoftrackingicons;
    }

    /**
     * Renders HTML to display one course module for display within a section.
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo.
     * @param cm_info $mod.
     * @param int|null $sectionreturn.
     * @param array $displayoptions.
     * @return String.
     */
    public function course_section_cm_list_item($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        if (!$this->activitytrackingbackground) {
            return parent::course_section_cm_list_item($course, $completioninfo, $mod, $sectionreturn, $displayoptions);
        }
        $output = '';
        if ($modulehtml = $this->course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {

            $completionclass = $this->get_completion_state($course, $completioninfo, $mod, true)['completionstate'];

            $modclasses = 'activity '.$mod->modname.' modtype_'.$mod->modname.' '.$mod->extraclasses;
            if ($completionclass) {
                $modclasses .= ' completion-background completion-'.$completionclass;
            }
            $output .= html_writer::tag('li', $modulehtml, array('class' => $modclasses, 'id' => 'module-' . $mod->id));
        }
        return $output;
    }

    /**
     * Renders html for completion box on course page.
     *
     * If completion is disabled, returns empty string.
     * If completion is automatic, returns an icon of the current completion state.
     * If completion is manual, returns a form (with an icon inside) that allows user to
     * toggle completion.
     *
     * @param stdClass $course course object
     * @param completion_info $completioninfo completion info for the course, it is recommended
     *     to fetch once for all modules in course/section for performance.
     * @param cm_info $mod module to show completion for.
     * @param array $displayoptions display options, not used in core.
     * @return string
     */
    public function course_section_cm_completion($course, &$completioninfo, cm_info $mod, $displayoptions = array()) {
        if ($this->locationoftrackingicons == \format_ned\toolbox::$moodleicons) {
            return parent::course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
        } // Else must be either nediconsleft or nediconsright.

        $output = '';
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }
        if ($completioninfo === null) {
            $completioninfo = new completion_info($course);
        }
        $completion = $completioninfo->is_enabled($mod);
        if ($completion == COMPLETION_TRACKING_NONE) {
            if ($this->page->user_is_editing()) {
                $output .= html_writer::span('&nbsp;', 'filler');
            }
            return $output;
        }

        $completionstate = $this->get_completion_state($course, $completioninfo, $mod, false);
        $completionicon = $completionstate['completionstate'];

        if ($completionicon) {
            $formattedname = $mod->get_formatted_name();
            $imgalt = get_string('completion-alt-'.$completionicon, 'completion', $formattedname);

            if ($this->page->user_is_editing()) {
                // When editing, the icon is just an image.
                $completionpixicon = new pix_icon('i/completion-'.$completionicon, $imgalt, 'format_ned',
                        array('title' => $imgalt, 'class' => 'iconsmall'));
                $output .= html_writer::tag('span', $this->output->render($completionpixicon),
                        array('class' => 'autocompletion'));
            } else if ($completion == COMPLETION_TRACKING_MANUAL) {
                global $CFG;
                $imgtitle = get_string('completion-title-'.$completionicon, 'completion', $formattedname);
                $newstate = $completionstate['completiondata']->completionstate == COMPLETION_COMPLETE ? COMPLETION_INCOMPLETE : COMPLETION_COMPLETE;
                // In manual mode the icon is a toggle form...

                /* If this completion state is used by the
                   conditional activities system, we need to turn
                   off the JS. */
                $extraclass = '';
                if (!empty($CFG->enableavailability) &&
                        core_availability\info::completion_value_used($course, $mod->id)) {
                    $extraclass = ' preventjs';
                }
                $output .= html_writer::start_tag('form', array('method' => 'post',
                    'action' => new moodle_url('/course/togglecompletion.php'),
                    'class' => 'togglecompletion'. $extraclass));
                $output .= html_writer::start_tag('div');
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'id', 'value' => $mod->id));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'modulename', 'value' => $mod->name));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'completionstate', 'value' => $newstate));
                $output .= html_writer::tag('button',
                    $this->output->pix_icon('i/completion-'.$completionicon, $imgalt, 'format_ned'), array('class' => 'btn btn-link'));
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('form');
            } else {
                // In auto mode, the icon is just an image.
                $completionpixicon = new pix_icon('i/completion-'.$completionicon, $imgalt, 'format_ned',
                        array('title' => $imgalt));
                $output .= html_writer::tag('span', $this->output->render($completionpixicon),
                        array('class' => 'autocompletion'));
            }
        }
        return $output;
    }

    /**
     * Helper method to get the competition state.
     *
     * @param stdClass $course course object.
     * @param completion_info $completioninfo completion info for the course, it is recommended
     *     to fetch once for all modules in course/section for performance.
     * @param cm_info $mod module to show completion for.
     * @param boolean $returnnotset If no tracking then return 'notset' as the state.
     * @return array containing the competition state and data if fetched.
     */
    protected function get_completion_state($course, &$completioninfo, cm_info $mod, $returnnotset) {
        if ($completioninfo === null) {
            $completioninfo = new completion_info($course);
        }
        $completion = $completioninfo->is_enabled($mod);
        $completionstate = '';
        $completiondata = null;
        if ($completion == COMPLETION_TRACKING_NONE) {
            if ($returnnotset) {
                $completionstate = 'notset';
            }
        } else {
            if ($this->page->user_is_editing()) {
                switch ($completion) {
                    case COMPLETION_TRACKING_MANUAL :
                        $completionstate = 'manual-enabled';
                        break;
                    case COMPLETION_TRACKING_AUTOMATIC :
                        $completionstate = 'auto-enabled';
                        break;
                }
            } else if ($completion == COMPLETION_TRACKING_MANUAL) {
                $completiondata = $completioninfo->get_data($mod, true);
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionstate = 'manual-n';
                        break;
                    case COMPLETION_COMPLETE:
                        $completionstate = 'manual-y';
                        break;
                }
            } else { // Automatic.
                $completiondata = $completioninfo->get_data($mod, true);
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionstate = 'auto-n';
                        break;
                    case COMPLETION_COMPLETE:
                        $completionstate = 'auto-y';
                        break;
                    case COMPLETION_COMPLETE_PASS:
                        $completionstate = 'auto-pass';
                        break;
                    case COMPLETION_COMPLETE_FAIL:
                        $completionstate = 'auto-fail';
                        break;
                }
            }
        }

        return array('completionstate' => $completionstate, 'completiondata' => $completiondata);
    }
}