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
require_once($CFG->dirroot.'/course/format/renderer.php');

class format_ned_renderer extends format_section_renderer_base {

    private $courseformat = null; // Our course format object as defined in lib.php.
    private $editing = false;
    private $settings = null;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        /* Since format_ned_renderer::section_edit_controls() only displays the 'Set current section' control when editing mode is
           on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any other managing
           capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');

        $this->editing = $page->user_is_editing();
    }

    /**
     * Set the course format from which we can then get the settings for our decisions.
     * @param format_ned $courseformat
     */
    public function set_courseformat($courseformat) {
        $this->courseformat = $courseformat; // Needed for settings retrieval.
        $this->settings = $this->courseformat->get_settings();
        $this->courserenderer = $this->page->get_renderer('format_ned', 'course');
        $this->courserenderer->set_settings(
            $this->settings['activitytrackingbackground'],
            $this->settings['locationoftrackingicons']
        );
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        $classes = 'ned';
        if (!$this->editing) {
            if ($this->settings['locationoftrackingicons'] == \format_ned\toolbox::$nediconsleft) {
                $classes .= ' '.\format_ned\toolbox::$nediconsleft;
            }
            // Temporarily disabled...
            if ((false) && ($this->settings['sectioncontentjustification'])) {
                $classes .= ' sectioncontentjustification';
            }
        }
        return html_writer::start_tag('ul', array('class' => $classes));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {
        if (!$this->editing) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if ($section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthistopic = get_string('markedthistopic');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                                               'name' => $highlightoff,
                                               'pixattr' => array('class' => '', 'alt' => $markedthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markedthistopic,
                                                   'data-action' => 'removemarker'));
            } else {
                $url->param('marker', $section->section);
                $markthistopic = get_string('markthistopic');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                                               'name' => $highlight,
                                               'pixattr' => array('class' => '', 'alt' => $markthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markthistopic,
                                                   'data-action' => 'setmarker'));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    /**
     * Returns the 'Your progress' help icon, if completion tracking is enabled.
     *
     * @return string HTML code for help icon, or blank if not needed
     */
    public function display_completion_help_icon(completion_info $completioninfo) {
        if ($this->settings['progresstooltip'] == 0) {  // Hide!
            return '';
        }

        global $PAGE, $OUTPUT;
        $result = '';
        if ($completioninfo->is_enabled() && !$PAGE->user_is_editing() && isloggedin() && !isguestuser()) {
            $completionprogressclass = 'completionprogress';
            if ($this->settings['locationoftrackingicons'] == \format_ned\toolbox::$nediconsleft) {
                $completionprogressclass .= ' nediconsleft';
            }
            if ($this->settings['progresstooltip'] == 1) {
                $helpicon = $OUTPUT->help_icon('completioniconsnomanual', 'format_ned');
            } else {
                $helpicon = $OUTPUT->help_icon('completionicons', 'completion');
            }
            $result .= html_writer::tag('div',
                    $helpicon.
                    $OUTPUT->pix_icon('t/sort_desc', ''),
                    array('id' => 'completionprogressid', 'class' => $completionprogressclass));
        }
        return $result;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course object.
     * @param array $sections (argument not used).
     * @param array $mods (argument not used).
     * @param array $modnames (argument not used).
     * @param array $modnamesused (argument not used).
     * @param int $displaysection The section number in the course which is being displayed.
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $modinfo = get_fast_modinfo($course);

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo html_writer::start_tag('div', array('class' => 'completionprogresshelp'));
        echo $this->display_completion_help_icon($completioninfo);
        echo html_writer::end_tag('div');

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection, $course->id);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $this->editing) {
            if (($this->editing) or ($this->settings['showsection0'] == 1)) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $context = context_course::instance($course->id);
        if (($this->settings['viewsectionforwardbacklinks'] == 0) ||
            (($this->settings['viewsectionforwardbacklinks'] == 1) && (has_capability('moodle/course:update', $context)))) {
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        }
        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        if (($this->settings['viewsectionforwardbacklinks'] == 0) ||
            (($this->settings['viewsectionforwardbacklinks'] == 1) && (has_capability('moodle/course:update', $context)))) {
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        }
        if (($this->settings['viewjumptomenu'] == 0) ||
            (($this->settings['viewjumptomenu'] == 1) && (has_capability('moodle/course:update', $context)))) {
            $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
                array('class' => 'mdl-align'));
        }
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page.
     *
     * @param stdClass $course The course object.
     * @param array $sections (argument not used).
     * @param array $mods (argument not used).
     * @param array $modnames (argument not used).
     * @param array $modnamesused (argument not used).
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        $modinfo = get_fast_modinfo($course);

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo html_writer::start_tag('div', array('class' => 'completionprogresshelp'));
        echo $this->display_completion_help_icon($completioninfo);
        echo html_writer::end_tag('div');
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard.
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections.
        echo $this->start_section_list();
        $numsections = $this->courseformat->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                if (($this->editing) or ($this->settings['showsection0'] > 0)) {
                    // Section 0 is displayed a little different then the others.
                    if ($thissection->summary or !empty($modinfo->sections[0]) or $this->editing) {
                        echo $this->section_header($thissection, $course, false, 0);
                        echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                        echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                        echo $this->section_footer();
                    }
                }
                if ((!$this->editing) and ($this->settings['showsection0'] == 2)) {
                    break;
                } else {
                    continue;
                }
            }
            if ($section > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                continue;
            }
            /* Show the section if the user is permitted to access it, OR if it's not available
               but there is some available info text which explains the reason & should display. */
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo));
            if (!$showsection) {
                /* If the hiddensections option is set to 'show hidden sections in collapsed
                   form', then display the hidden section message - UNLESS the section is
                   hidden by the availability system, which is set to hide the reason. */
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section, $course->id);
                }

                continue;
            }

            if (!$this->editing && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($this->editing and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo $this->change_number_sections($course, 0);
        } else {
            echo $this->end_section_list();
        }
    }
}
