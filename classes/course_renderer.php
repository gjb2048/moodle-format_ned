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
    private $relocateactivitydescription;

    /**
     * Override the constructor so that we can initialise the relocate activity description
     * as it will be used many times.
     *
     * @param moodle_page $page
     * @param string $target
     */
    public function __construct(moodle_page $page, $target) {
        $this->relocateactivitydescription = get_config('format_ned', 'relocateactivitydescription');
        parent::__construct($page, $target);
    }

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
    public function course_section_cm_list_item($course, &$completioninfo, cm_info $mod, $sectionreturn,
        $displayoptions = array()) {
        if (!$this->activitytrackingbackground) {
            return parent::course_section_cm_list_item($course, $completioninfo, $mod, $sectionreturn, $displayoptions);
        }
        $output = '';
        if ($modulehtml = $this->course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {

            $completionstate = $this->get_completion_state($course, $completioninfo, $mod, true, true);

            $modclasses = 'activity '.$mod->modname.' modtype_'.$mod->modname.' '.$mod->extraclasses;
            if ($completionstate['completionstate']) {
                $modclasses .= ' completion-background completion-'.$completionstate['completionstate'];
                if ($completionstate['assgnmentstatus']) {
                    $modclasses .= ' completion-'.$completionstate['assgnmentstatus'];
                }
            }
            $output .= html_writer::tag('li', $modulehtml, array('class' => $modclasses, 'id' => 'module-' . $mod->id));
        }
        return $output;
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        if ($this->relocateactivitydescription == 0) { // No change.
            return parent::course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions);
        }
        // Output here will only be for 'above'.

        $output = '';
        /* We return empty string (because course module will not be displayed at all)
           if:
           1) The activity is not visible to users
           and
           2) The 'availableinfo' is empty, i.e. the activity was
              hidden in a way that leaves no info, such as using the
              eye icon. */
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent.
        $output .= html_writer::start_tag('div');

        // Display the link to the module (or do nothing if module has no url).
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        // Content part.  This will normally be the summary but can be the content of a label.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;

        /* If there is content AND a link, then display the content here
           (BEFORE any icons).  Otherwise it will be after in the 'parent' call.
           Thus $this->relocateactivitydescription will be in an 'Above' state.
        */
        if (!empty($url)) {
            $output .= $contentpart;
        }

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;

            // Module can put text after the link (e.g. forum unread).
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        /* If there is content but NO link (eg label), then display the
           content here (BEFORE any icons). In this case icons must be
           displayed after the content so that it makes more sense visually
           and for accessibility reasons, e.g. if you have a one-line label
           it should work similarly (at least in terms of ordering) to an
           activity. */
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $classes = 'actions';
            if (!empty($url)) { // Move to the bottom only if the description is above.
                $classes .= ' nediconsbottom';
            }
            $output .= html_writer::span($modicons, $classes);
        }

        // Show availability info (if module is not available).
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        $output .= html_writer::end_tag('div'); // $indentclasses.

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Renders html to display the module content on the course page (i.e. text of the labels)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_text(cm_info $mod, $displayoptions = array()) {
        if ($this->relocateactivitydescription == 0) { // No change.
            return parent::course_section_cm_text($mod, $displayoptions);
        }

        $output = '';
        if (!$mod->is_visible_on_course_page()) {
            // nothing to be displayed to the user
            return $output;
        }
        $content = $mod->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);
        if ($mod->url && $mod->uservisible) {
            if ($content) {
                // If specified, display extra content after link.

                // If above the icon then will need to adjust the margin.
                if ($this->relocateactivitydescription == 2) { // Above icon.
                    $textclasses .= ' nedaboveicon';
                }

                $output = html_writer::tag('div', $content, array('class' => trim('contentafterlink '.$textclasses)));
            }
        } else {
            $groupinglabel = $mod->get_grouping_label($textclasses);

            // No link, so display only content.
            $output = html_writer::tag('div', $content . $groupinglabel,
                    array('class' => 'contentwithoutlink '.$textclasses));
        }
        return $output;
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // Check if we are currently in the process of moving a module with JavaScript disabled.
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one).
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // Do not display moving mod.
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                        array('class' => 'movehere'));
            }
        }

        /* Change to core, only output the section module list when there are modules to show,
           otherwise header will not be 8px when empty.  This is because of the 1em margin on
           it, which despite having no top margin still makes the header bigger than it should
           and removing / altering this margin then messes up the activity / resource margin
           when there are there / the alignment of the completion icons.
           Update: breaks Drag and Drop when editing, so need to show when doing so. */
        if (($this->page->user_is_editing()) || (!empty($sectionoutput))) {
            $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));
        }

        return $output;
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list_empty($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // Check if we are currently in the process of moving a module with JavaScript disabled.
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one).
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // Do not display moving mod.
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                        array('class' => 'movehere'));
            }
        }

        /* Change to core, only output the section module list when there are modules to show,
           otherwise header will not be 8px when empty.  This is because of the 1em margin on
           it, which despite having no top margin still makes the header bigger than it should
           and removing / altering this margin then messes up the activity / resource margin
           when there are there / the alignment of the completion icons.
           Update: breaks Drag and Drop when editing, so need to show when doing so. */
        if (($this->page->user_is_editing()) || (!empty($sectionoutput))) {
            $output .= html_writer::tag('ul', '', array('class' => 'section img-text', 'nedsectionno' => $section->section));
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

        $completionstate = $this->get_completion_state($course, $completioninfo, $mod, false, false);
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
                    $this->output->pix_icon('i/completion-'.$completionicon, $imgalt, 'format_ned'),
                        array('class' => 'btn btn-link'));
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('form');
            } else {
                // In auto mode, the icon is just an image.
                if ($completionicon == \format_ned\toolbox::$auton) {
                    $assgnmentstatus = $this->is_saved_or_submitted($mod);
                    if ($assgnmentstatus) {
                        // Use state icon.
                        $completionicon = $assgnmentstatus;
                        $imgalt = get_string('completion-alt-'.$completionicon, 'format_ned', $formattedname);
                    }
                }
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
     * @param boolean $returnassgnmentstatus Request assignment status.
     * @return array containing the competition state, data if fetched and assignment status if known.
     */
    protected function get_completion_state($course, &$completioninfo, cm_info $mod, $returnnotset, $returnassgnmentstatus) {
        if ($completioninfo === null) {
            $completioninfo = new completion_info($course);
        }
        $completion = $completioninfo->is_enabled($mod);
        $assgnmentstatus = false;
        $completionstate = '';
        $completiondata = null;
        if ($completion == COMPLETION_TRACKING_NONE) {
            if ($returnnotset) {
                $completionstate = \format_ned\toolbox::$notset;
            }
        } else {
            if ($this->page->user_is_editing()) {
                switch ($completion) {
                    case COMPLETION_TRACKING_MANUAL :
                        $completionstate = \format_ned\toolbox::$manualenabled;
                        break;
                    case COMPLETION_TRACKING_AUTOMATIC :
                        $completionstate = \format_ned\toolbox::$autoenabled;
                        break;
                }
            } else if ($completion == COMPLETION_TRACKING_MANUAL) {
                $completiondata = $completioninfo->get_data($mod, true);
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionstate = \format_ned\toolbox::$manualn;
                        break;
                    case COMPLETION_COMPLETE:
                        $completionstate = \format_ned\toolbox::$manualy;
                        break;
                }
                if ($returnassgnmentstatus) {
                    $assgnmentstatus = $this->is_saved_or_submitted($mod);
                }
            } else { // Automatic.
                $completiondata = $completioninfo->get_data($mod, true);
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionstate = \format_ned\toolbox::$auton;
                        break;
                    case COMPLETION_COMPLETE:
                        $completionstate = \format_ned\toolbox::$autoy;
                        break;
                    case COMPLETION_COMPLETE_PASS:
                        $completionstate = \format_ned\toolbox::$autopass;
                        break;
                    case COMPLETION_COMPLETE_FAIL:
                        $completionstate = \format_ned\toolbox::$autofail;
                        break;
                }
                if ($returnassgnmentstatus) {
                    $assgnmentstatus = $this->is_saved_or_submitted($mod);
                }
            }
        }

        return array(
            'completionstate' => $completionstate,
            'completiondata' => $completiondata,
            'assgnmentstatus' => $assgnmentstatus
        );
    }

    /**
     * To get the assignment object and user submission
     *
     * @param module of the assignment
     * @return assignment object from assignment table
     * @todo Finish documenting this function
     */
    protected function is_saved_or_submitted($mod) {
        global $CFG, $DB, $SESSION, $USER;
        require_once($CFG->dirroot . '/mod/assignment/lib.php');

        if (isset($SESSION->completioncache)) {
            unset($SESSION->completioncache);
        }

        if ($mod->modname == 'assignment') {
            if (!($assignment = $DB->get_record('assignment', array('id' => $mod->instance)))) {
                return false; // Doesn't exist?
            }
            require_once($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php');
            $assignmentclass = "assignment_$assignment->assignmenttype";
            $assignmentinstance = new $assignmentclass($mod->id, $assignment, $mod);

            if (!($submission = $assignmentinstance->get_submission($USER->id)) || empty($submission->timemodified)) {
                return false;
            }

            switch ($assignment->assignmenttype) {
                case "upload":
                    if ($assignment->var4) { // If var4 enable then assignment can be saved.
                        if (!empty($submission->timemodified)
                            && (empty($submission->data2))
                            && (empty($submission->timemarked))) {
                            return \format_ned\toolbox::$saved;
                        } else if (!empty($submission->timemodified)
                            && ($submission->data2 = 'submitted')
                            && empty($submission->timemarked)) {
                            return \format_ned\toolbox::$submitted;
                        } else if (!empty($submission->timemodified)
                            && ($submission->data2 = 'submitted')
                            && ($submission->grade == -1)) {
                            return \format_ned\toolbox::$submitted;
                        }
                    } else if (empty($submission->timemarked)) {
                        return \format_ned\toolbox::$submitted;
                    }
                    break;
                case "uploadsingle":
                    if (empty($submission->timemarked)) {
                        return \format_ned\toolbox::$submitted;
                    }
                    break;
                case "online":
                    if (empty($submission->timemarked)) {
                        return \format_ned\toolbox::$submitted;
                    }
                    break;
                case "offline":
                    if (empty($submission->timemarked)) {
                        return \format_ned\toolbox::$submitted;
                    }
                    break;
            }
        } else if ($mod->modname == 'assign') {
            if (!($assignment = $DB->get_record('assign', array('id' => $mod->instance)))) {
                return false; // Doesn't exist.
            }

            if (!$submission = $DB->get_records('assign_submission',
                array('assignment' => $assignment->id, 'userid' => $USER->id), 'attemptnumber DESC', '*', 0, 1)) {
                return false;
            } else {
                $submission = reset($submission);
            }

            $attemptnumber = $submission->attemptnumber;

            if (($submission->status == 'reopened') && ($submission->attemptnumber > 0)) {
                $attemptnumber = $submission->attemptnumber - 1;
            }

            if ($submissionisgraded = $DB->get_records('assign_grades',
                array('assignment' => $assignment->id, 'userid' => $USER->id, 'attemptnumber' => $attemptnumber),
                'attemptnumber DESC', '*', 0, 1)) {

                $submissionisgraded = reset($submissionisgraded);
                if ($submissionisgraded->grade > -1) {
                    if (($submission->timemodified > $submissionisgraded->timemodified)
                        || ($submission->attemptnumber > $submissionisgraded->attemptnumber)) {
                        $graded = false;
                    } else {
                        $graded = true;
                    }
                } else {
                    $graded = false;
                }
            } else {
                $graded = false;
            }

            if ($submission->status == 'draft') {
                if ($graded) {
                    return \format_ned\toolbox::$submitted;
                } else {
                    return \format_ned\toolbox::$saved;
                }
            }
            if ($submission->status == 'reopened') {
                return \format_ned\toolbox::$submitted;
            }
            if ($submission->status == 'submitted') {
                if ($graded) {
                    return \format_ned\toolbox::$submitted;
                } else {
                    return \format_ned\toolbox::$waitinggrade;
                }
            }
        } else {
            return false;
        }
    }
}