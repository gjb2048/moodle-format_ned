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
    private $displaysection = null; // Display section number if on a single section page.
    private $editing = false;
    private $settings = null;
    private $sectionheaderformatssetting = null; // JSON decode of 'sectionheaderformats' setting.
    private $progressiconshown = false;

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
     * @param format_ned $courseformat Reference to the course format object.
     * @param bool $singlesection If we are on a single section page.
     */
    public function set_courseformat($courseformat, $singlesection) {
        $this->courseformat = $courseformat; // Needed for settings retrieval.
        $this->settings = $this->courseformat->get_settings();
        if ($this->settings['sectionformat'] == 3) {
            $this->sectionheaderformatssetting = $this->courseformat->get_setting('sectionheaderformats');
        }
        $this->courserenderer = $this->page->get_renderer('format_ned', 'course');
        $this->courserenderer->set_settings(
            $this->settings['activitytrackingbackground'],
            $this->settings['locationoftrackingicons']
        );
        if (($this->settings['compressedsections'] == 1) && ($this->editing) &&
            (!$singlesection)) {
            $data = array('data' => array(
                'allcompressed' => \format_ned\toolbox::$allcompressed,
                'allexpanded' => \format_ned\toolbox::$allexpanded,
                 // If positive then expand that section only, if zero then currently not compressable so same as 'allcompressed'.
                'nedsectionstate' =>
                    optional_param(\format_ned\toolbox::$compressedsectionsparam, \format_ned\toolbox::$allcompressed, PARAM_INT)
            ));
            $this->page->requires->js_call_amd('format_ned/nededitingsection', 'init', $data);
        }
    }

    /**
     * Generate the starting container html for section 0.  No Framed sections.
     * @return string HTML to output.
     */
    protected function start_section0_list() {
        $classes = 'ned';
        if ($this->settings['sectionformat'] >= 1) { // Framed sections.
            $classes .= ' ned-framedsections-s0';
        }
        if (!is_null($this->displaysection)) {
            $classes .= ' ned-single-section';
        }
        if (!$this->editing) {
            if ($this->settings['locationoftrackingicons'] == \format_ned\toolbox::$nediconsleft) {
                $classes .= ' '.\format_ned\toolbox::$nediconsleft;
            }
            if ($this->settings['activityresourcemouseover'] == 1) {
                $classes .= ' '.\format_ned\toolbox::$activityresourcemouseover;
            }
            // Temporarily disabled...
            if ((false) && ($this->settings['sectioncontentjustification'])) {
                $classes .= ' sectioncontentjustification';
            }
        } else if ((is_null($this->displaysection)) && ($this->settings['compressedsections'] == 1)) {
            $classes .= ' nededitingsection';
        }
        return html_writer::start_tag('ul', array('class' => $classes));
    }

    /**
     * Generate the starting container html for a list of sections.
     * @return string HTML to output.
     */
    protected function start_section_list() {
        $classes = 'ned';

        if ($this->settings['sectionformat'] >= 1) { // Framed sections.
            $classes .= ' ned-framedsections';
            if ($this->settings['sectionformat'] == 2) { // Framed sections with custom header.
                $classes .= ' ned-framedsectionscustom';
            } else if ($this->settings['sectionformat'] == 3) { // Framed sections with formatted.
                $classes .= ' ned-framedsectionsformatted';
            }
        }
        if (!is_null($this->displaysection)) {
            $classes .= ' ned-single-section';
        }
        if (!$this->editing) {
            if ($this->settings['activityresourcemouseover'] == 1) {
                $classes .= ' '.\format_ned\toolbox::$activityresourcemouseover;
            }
            if ($this->settings['locationoftrackingicons'] == \format_ned\toolbox::$nediconsleft) {
                $classes .= ' '.\format_ned\toolbox::$nediconsleft;
            }
            // Temporarily disabled...
            if ((false) && ($this->settings['sectioncontentjustification'])) {
                $classes .= ' sectioncontentjustification';
            }
        } else if ((is_null($this->displaysection)) && ($this->settings['compressedsections'] == 1)) {
            $classes .= ' nededitingsection';
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
        if (empty($this->courseformat)) {
            $this->courseformat = course_get_format($course);
        }
        return $this->render($this->courseformat->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        if (empty($this->courseformat)) {
            $this->courseformat = course_get_format($course);
        }
        return $this->render($this->courseformat->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }
        if ((is_null($this->displaysection)) && ($this->settings['compressedsections'] == 1)) {
            $sectionstyle .= ' closed';
        }

        if (($section->section != 0) && ($this->settings['sectionformat'] == 3)) { // Framed sections + Formatted header.
            static $shfrows = array(1 => 'sectionheaderformatone', 2 => 'sectionheaderformattwo', 3 => 'sectionheaderformatthree');
            $sectionheaderformatdata = $this->courseformat->get_setting('sectionheaderformat', $section->section);
            $colourpreset = $this->sectionheaderformatssetting[$shfrows[$sectionheaderformatdata['headerformat']]]['colourpreset'];
            if ($colourpreset > 0) {
                $sectionstyle .= ' colourpreset-'.$colourpreset;
            }
        }

        // Note 'get_section_name(course, section)' just calls the format's lib.php 'get_section_name(section)'!
        $thesectionname = $this->courseformat->get_section_name($section);
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role' => 'region',
            'aria-label' => $thesectionname));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', $thesectionname, array('class' => 'hidden sectionname'));

        if (($this->settings['sectionformat'] == 0) ||
            ($this->settings['sectionformat'] == 1) ||
            (($this->settings['sectionformat'] == 2) &&
             (!empty($this->settings['sectionnamelocation'])))||  // 0 is hide otherwise show.
            (($this->settings['sectionformat'] == 3) && ($section->section == 0))) {
            $sectionnameclasses = '';
            if ($this->settings['sectionformat'] == 0) {
                $sectionnameclasses = ' accesshide';

                // When not on a section page, we display the section titles except the general section if null.
                $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

                // When on a section page, we only display the general section title, if title is not the default one.
                $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

                if ($hasnamenotsecpg || $hasnamesecpg) {
                    $sectionnameclasses = '';
                }
            }
            $sectionname = html_writer::tag('span', $this->section_title($section, $course));
            $sectionnamemarkup = $this->output->heading($sectionname, 3, 'sectionname' . $sectionnameclasses);
        } else {
            $sectionnamemarkup = '';
        }

        $summarymarkup = html_writer::start_tag('div', array('class' => 'summary'));
        $summarymarkup .= $this->format_summary_text($section);
        $summarymarkup .= html_writer::end_tag('div');

        if ($this->settings['sectionformat'] == 1) { // Framed sections.
            $o .= html_writer::tag('div', '', array('class' => 'header'));
        } else if ($this->settings['sectionformat'] == 2) { // Framed sections + Custom header.
            if ($this->settings['sectionnamelocation'] == 1) { // 1 is show in the section header.
                $sectionheadercontent = $sectionnamemarkup;
                if ($this->settings['sectionsummarylocation'] == 0) { // 0 is show in the section header.
                    $sectionheadercontent .= $summarymarkup;
                }
            } else {
                if ($this->settings['sectionsummarylocation'] == 0) { // 0 is show in the section header.
                    $sectionheadercontent = $summarymarkup;
                } else {
                    $sectionheadercontent = '';
                }
            }
            $o .= html_writer::tag('div', $sectionheadercontent, array('class' => 'header'));
        } else if ($this->settings['sectionformat'] == 3) { // Framed sections + Formatted header.
            if ($section->section != 0) {
                $hasheadercontent = false;
                $sectionheadercontent = '<div class="nedshfleftcolumn">';
                $leftcontent = '&nbsp;';
                if ($this->sectionheaderformatssetting[$shfrows[$sectionheaderformatdata['headerformat']]]['leftcolumn']['active'] == 1) {
                    if (!empty($sectionheaderformatdata['sectionname']['leftcolumn'])) {
                        $leftcontent = format_text($sectionheaderformatdata['sectionname']['leftcolumn']);
                        $hasheadercontent = true;
                    }
                }
                $sectionheadercontent .= '<span>'.$leftcontent.'</span></div><div class="nedshfmiddlecolumn">';
                $middlecontent = '&nbsp;';
                $middlecontentattr = '';
                if ($this->sectionheaderformatssetting[$shfrows[$sectionheaderformatdata['headerformat']]]['middlecolumn']['active'] == 1) {
                    if (!empty($sectionheaderformatdata['sectionname']['middlecolumn'])) {
                        $middlecontent = format_text($sectionheaderformatdata['sectionname']['middlecolumn']);
                        $hasheadercontent = true;
                        if ($this->sectionheaderformatssetting['shfmclt'] == 1) {
                            $middlecontentattr = ' class="nedshfmiddlecolumnlarge"';
                        }
                    }
                }
                $sectionheadercontent .= '<span'.$middlecontentattr.'>'.$middlecontent.'</span></div><div class="nedshfrightcolumn">';
                $rightcontent = '&nbsp;';
                if ($this->sectionheaderformatssetting[$shfrows[$sectionheaderformatdata['headerformat']]]['rightcolumn']['active'] == 1) {
                    if (!empty($sectionheaderformatdata['sectionname']['rightcolumn'])) {
                        $rightcontent = format_text($sectionheaderformatdata['sectionname']['rightcolumn']);
                        $hasheadercontent = true;
                    }
                }
                $sectionheadercontent .= '<span>'.$rightcontent.'</span></div>';
                if ($hasheadercontent) {
                    $sectionheaderheader = '<div class="nedshfcolumns nedshfcolumnswithcontent">'.$sectionheadercontent.'</div>';
                } else {
                    $sectionheaderheader = '<div class="nedshfcolumns nedshfcolumnswithoutcontent"></div>';
                }

                $o .= html_writer::tag('div', $sectionheaderheader, array('class' => 'header'));
            }
        }

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $rightclasses = 'right side';
        if ($section->section != 0) {
            $rightclasses .= ' nedrightside';
        }
        $o .= html_writer::tag('div', $rightcontent, array('class' => $rightclasses));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        if (($this->editing) && ($this->settings['sectionformat'] == 3)) { // Framed sections + Formatted header.
            $o .= html_writer::tag('h3', html_writer::tag('span', '', array('class' => 'inplaceeditable', 'data-itemid' => $section->id)), array('class' => 'sectionname hidden'));
        }

        // Heading in the body of the section.
        if (($this->settings['sectionformat'] == 0) ||
            ($this->settings['sectionformat'] == 1) ||
            (($this->settings['sectionformat'] == 2) &&
             ($this->settings['sectionnamelocation'] == 2))) { // 2 is show in the section body.
            $o .= $sectionnamemarkup;
        }

        $o .= $this->section_availability($section);

        // Section summary in the body of the section.
        if (($this->settings['sectionformat'] == 0) ||
            ($this->settings['sectionformat'] == 1) ||
            (($this->settings['sectionformat'] == 2) &&
             ($this->settings['sectionsummarylocation'] == 1)) || // 1 is show in the section body.
            ($this->settings['sectionformat'] == 3)) {
            if (($this->editing) &&
                ($this->settings['sectionformat'] != 2) &&
                (is_null($this->displaysection)) &&
                ($this->settings['compressedsections'] == 1) &&
                ($this->settings['compressedmodeview'] == 0)) {
                /* Not Framed sections + Custom header on editing a multiple section page (regardless of 'One section per page' or
                   'Show all sections on one page' 'Course layout') with compressed sections shown and compressed mode view is
                   'Hide summary section'.
                   Then we need a 'flip/flop' on the compressed / expanded toggle to show / hide the format section with the
                   summary itself.*/
                $o .= html_writer::start_tag('div', array('class' => 'compressedmodeviewhide'));
                if ($this->settings['sectionformat'] == 3) { // Framed sections + Formatted header.
                    $sectiontitle = get_string('compressedsectionsectionname', 'format_ned',
                        array('sectionno' => $section->section));
                    $sectiontitle .= $this->section_title($section, $course);
                    $o .= html_writer::tag('span', $sectiontitle, array('class' => 'sectioname'));
                } else {
                    $o .= html_writer::tag('div', get_string('compressedsectionformat', 'format_ned',
                        array('sectionno' => $section->section)), array('class' => 'compressedmodeviewhideformat'));
                }
                $o .= $summarymarkup;
                $o .= html_writer::end_tag('div');
            } else {
                $o .= $summarymarkup;
            }
        }

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div');
        if ($this->settings['sectionformat'] >= 1) { // Framed sections.
            $o .= html_writer::tag('div', '', array('class' => 'footer'));
        }
        $o .= html_writer::end_tag('li');

        return $o;
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

        // Do not have to worry about the edit key as not in the menu in 'section_right_content()' below but data still used.
        $mergedone = array_merge($controls, $parentcontrols);

        if (is_null($this->displaysection)) {
            $addsectionbelowurl = new moodle_url('/course/changenumsections.php',
                ['courseid' => $course->id, 'insertsection' => ($section->section + 1), 'sesskey' => sesskey()]);
            $addsectionbelowstr = get_string('addsectionbelow', 'format_ned');
            $addsectionbelowcontrol = array('addsectionbelow' => array('url' => $addsectionbelowurl, "icon" => 'i/down',
                'name' => $addsectionbelowstr,
                'pixattr' => array('alt' => $addsectionbelowstr),
                'attr' => array('title' => $addsectionbelowstr)));
            $mergedtwo = array();
            // If the delete key exists, we are going to insert our add section below control before it.
            if (array_key_exists("delete", $mergedone)) {
                // We can't use splice because we are using associative arrays.
                // Step through the array and merge the arrays.
                foreach ($mergedone as $key => $action) {
                    $mergedtwo[$key] = $action;
                    if ($key == "delete") {
                        // If we have come to the delete key, merge these controls here.
                        $mergedtwo = array_merge($mergedtwo, $addsectionbelowcontrol);
                    }
                }
            } else {
                $mergedtwo = array_merge($mergedone, $addsectionbelowcontrol);
            }
            return $mergedtwo;
        } else {
            return $mergedone;
        }
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = '';

        if ($this->editing) {
            if ($section->section != 0) {
                if (is_null($this->displaysection)) {
                    $sectionicon = $this->output->pix_icon('single_section', get_string('opensinglesection', 'format_ned',
                        array('sectionno' => $section->section)), 'format_ned', array('class' => 'nedsinglesectionicon'));
                    $sectionurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $section->section));
                    $o .= html_writer::link($sectionurl, $sectionicon);
                } else {
                    $sectionicon = $this->output->pix_icon('expand_section', get_string('maincoursepage'), 'format_ned',
                        array('class' => 'nedsinglesectionicon'));
                    $params = array('id' => $course->id);
                    if ($this->courseformat->get_mainpageoption()) {
                        $params[\format_ned\toolbox::$mainpageparam] = 1;
                        $params[\format_ned\toolbox::$compressedsectionsparam] = $section->section;
                    }
                    $sectionurl = new moodle_url('/course/view.php', $params);
                    $o .= html_writer::link($sectionurl, $sectionicon);
                }
            }
            if ((is_null($this->displaysection)) && ($this->settings['compressedsections'] == 1)) {
                $o .= html_writer::tag('span', '', array('class' => 'nededitingsectionpix'));
            }
        }

        $o .= parent::section_left_content($section, $course, $onsectionpage);

        return $o;
    }

    /**
     * Generate the edit control action menu
     *
     * @param array $controls The edit control items from section_edit_control_items
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function section_edit_control_menu($controls, $course, $section) {
        $o = "";
        if (!empty($controls)) {
            $menu = new action_menu();
            $menu->attributes['class'] .= ' section-actions';
            foreach ($controls as $value) {
                $url = empty($value['url']) ? '' : $value['url'];
                $icon = empty($value['icon']) ? '' : $value['icon'];
                $name = empty($value['name']) ? '' : $value['name'];
                $attr = empty($value['attr']) ? array() : $value['attr'];
                $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                $alt = empty($value['pixattr']['alt']) ? '' : $value['pixattr']['alt'];
                $al = new action_menu_link_secondary(
                    new moodle_url($url),
                    new pix_icon($icon, $alt, null, array('class' => "smallicon " . $class)),
                    $name,
                    $attr
                );
                $menu->add($al);
            }

            $o .= html_writer::div($this->render($menu), 'section_action_menu',
                array('data-sectionid' => $section->id));
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = '';

        $controls = $this->section_edit_control_items($course, $section, $onsectionpage);

        if (array_key_exists("edit", $controls)) {
            $icon = $this->output->pix_icon('edit_header', $controls['edit']['name'], 'format_ned', $controls['edit']['pixattr']);
            $o .= html_writer::link($controls['edit']['url'], $icon, $controls['edit']['attr']);

            unset($controls['edit']);
        }

        $o .= $this->section_edit_control_menu($controls, $course, $section);

        return $o;
    }

    /**
     * Returns the 'Your progress' help icon, if completion tracking is enabled.
     *
     * @return string HTML code for help icon, or blank if not needed.
     */
    public function display_completion_help_icon(completion_info $completioninfo, $courseid, $sectionno = null) {
        if (($this->progressiconshown) || ($this->settings['progresstooltip'] == 0)) {  // Already shown or Hide!
            return '';
        }

        global $PAGE;
        $result = '';
        if ($completioninfo->is_enabled() && !$PAGE->user_is_editing() && isloggedin() && !isguestuser()) {
            /* Only display the icon if there are displayed activities with completion on the page.
               Thus negating the JavaScript 'flash' as it does a 'display: none' and then not having
               a container with CSS height still there. */
            $showicon = false;
            $activitieswithcompletion = $completioninfo->get_activities();
            $modinfo = get_fast_modinfo($courseid);
            if (!empty($sectionno)) {
                $section = $modinfo->get_section_info($sectionno);
                if (!empty($modinfo->sections[$section->section])) {
                    foreach ($modinfo->sections[$section->section] as $modnumber) {
                        $mod = $modinfo->cms[$modnumber];
                        if (!$mod->uservisible && empty($mod->availableinfo)) {
                            continue;
                        }
                        if (array_key_exists($mod->id, $activitieswithcompletion)) {
                            $showicon = true;
                            break;
                        }
                    }
                }
            } else if (count($activitieswithcompletion) > 0) {
                $mods = $modinfo->get_cms();
                foreach ($mods as $mod) {
                    if (!$mod->uservisible) {
                        continue;
                    }
                    if (array_key_exists($mod->id, $activitieswithcompletion)) {
                        $showicon = true;
                        break;
                    }
                }
            }

            if ($showicon) {
                $result .= html_writer::start_tag('div', array('class' => 'completionprogresshelp'));
                $completionprogressclass = 'completionprogress';
                if ($this->settings['locationoftrackingicons'] == \format_ned\toolbox::$nediconsleft) {
                    $completionprogressclass .= ' nediconsleft';
                }
                if ($this->settings['sectionformat'] >= 1) { // Framed sections.
                    $completionprogressclass .= ' ned-framedsections';
                }
                if ($this->settings['progresstooltip'] == 1) {
                    $helpicon = $this->output->help_icon('completioniconsnomanual', 'format_ned');
                } else {
                    $helpicon = $this->output->help_icon('completionicons', 'completion');
                }
                $result .= html_writer::tag('div',
                    $helpicon.
                    $this->output->pix_icon('t/sort_desc', ''),
                    array('id' => 'completionprogressid', 'class' => $completionprogressclass));
                $result .= html_writer::end_tag('div');
                $this->progressiconshown = true;
            }
        }

        return $result;
    }

    /**
     * Generate a summary of a section for display on the 'course index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        if (($section->section != 0) && ($this->settings['sectionformat'] == 3)) { // Framed sections + Formatted header.
            static $shfrows = array(1 => 'sectionheaderformatone', 2 => 'sectionheaderformattwo', 3 => 'sectionheaderformatthree');
            $sectionheaderformatdata = $this->courseformat->get_setting('sectionheaderformat', $section->section);
            $colourpreset = $this->sectionheaderformatssetting[$shfrows[$sectionheaderformatdata['headerformat']]]['colourpreset'];
            if ($colourpreset > 0) {
                $classattr .= ' colourpreset-'.$colourpreset;
            }
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => $classattr, 'role' => 'region', 'aria-label' => $title));

        if ($this->settings['sectionformat'] >= 1) { // Framed sections.
            $o .= html_writer::tag('div', '', array('class' => 'header'));
        }
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');
        $o .= $this->section_activity_summary($section, $course, null);

        $o .= $this->section_availability($section);

        $o .= html_writer::end_tag('div');
        if ($this->settings['sectionformat'] >= 1) { // Framed sections.
            $o .= html_writer::tag('div', '', array('class' => 'footer'));
        }
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * States if section 0 should be shown.
     *
     * @return boolean Yes = true or No = false.
     */
    protected function showsection0() {
        $showsection0 = false;
        $showsection0setting = $this->courseformat->get_setting('showsection0');
        switch ($showsection0setting) {
            case 1: // Show.
                $showsection0 = true;
                break;
            case 2: // Only 0.
                $showsection0 = true;
                break;
            case 3: // Only in editing mode.
                if ($this->editing) {
                    $showsection0 = true;
                }
                break;
        }

        return $showsection0;
    }

    /**
     * Generate the html for the 'Jump to' menu.
     *
     * @param stdClass $course The course entry from DB.
     * @param array $sections  (argument not used) - core function definition is incorrect.
     * @param $displaysection the current displayed section number or false if on the main page.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        global $CFG;
        $o = '';
        $sectionmenu = array();

        if ($displaysection) {
            $mainpageurl = $this->courseformat->get_view_url(null);
            if ($this->courseformat->get_mainpageoption()) {
                $mainpageurl->param(\format_ned\toolbox::$mainpageparam, 1);
                $mainpageurl->param(\format_ned\toolbox::$compressedsectionsparam, $displaysection);
            }

            $sectionmenu[$mainpageurl->out(false)] = get_string('maincoursepage');
        }

        $modinfo = get_fast_modinfo($course);
        $section = 1;
        $numsections = $this->courseformat->get_last_section_number();
        // As the 'Jump to' menu is on a single section page then don't use 'anchors'.
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && (($displaysection === false) || ($section != $displaysection))) {
                $sectionurl = $this->courseformat->get_view_url($section);
                $sectionmenu[$sectionurl->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu_';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Generate the html for the NED Header on a course.
     *
     * @param stdClass $course The course object.
     * @param stdClass $context Course context.
     * @param int $displaysection The section number in the course which is being displayed or false if on the main page.
     */
    protected function get_ned_course_header($course, $context, $displaysection) {
        $o = html_writer::start_tag('div', array('class' => 'nedsectionmenu'));
        if ($this->editing) {

            if (has_capability('format/ned:formatupdate', $context)) {
                $nedsettingsurl = new moodle_url('/course/format/ned/nedsettings.php', array('id' => $course->id));
                $o .= html_writer::link($nedsettingsurl,
                    $this->pix_icon('ned_icon', get_string('editnedformatsettings', 'format_ned'), 'format_ned'),
                    array('title' => get_string('editnedformatsettings', 'format_ned'), 'class' => 'nededitsection'));
            }
        }

        $showjumptomenu = false;
        if (!$this->editing) {
            $viewjumptomenu = get_config('format_ned', 'viewjumptomenu');
            if (($viewjumptomenu == 0) ||
                (($viewjumptomenu == 1) && (has_capability('moodle/course:update', $context)))) {
                $showjumptomenu = true;
            }
        } else {
            $showjumptomenu = true;
        }
        if ($showjumptomenu) {
            $o .= html_writer::tag('div', $this->section_nav_selection($course, null, $displaysection));
        }

        if ($this->editing) {
            $courseupdatecapability = has_capability('moodle/course:update', $context);
            if (($this->courseformat->get_setting('compressedsections') == 1) && $courseupdatecapability) {
                if ($displaysection == false) {
                    // On main page.
                    $o .= html_writer::tag('span',
                        $this->pix_icon('compressed', '', 'format_ned'),
                        array('id' => 'nededitingsectioncompressed', 'title' => get_string('compressed', 'format_ned')));
                    $o .= html_writer::tag('span',
                        $this->pix_icon('expanded', '', 'format_ned'),
                        array('id' => 'nededitingsectionexpanded', 'title' => get_string('expanded', 'format_ned')));
                } else {
                    // On section page.
                    $mainpageurl = $this->courseformat->get_view_url(null);
                    $mainpageurl->param(\format_ned\toolbox::$mainpageparam, 1);
                    $mainpageurl->param(\format_ned\toolbox::$compressedsectionsparam, \format_ned\toolbox::$allcompressed);
                    $o .= html_writer::tag('a',
                        $this->pix_icon('compressed', '', 'format_ned'),
                        array('id' => 'nededitingsectioncompressed', 'href' => $mainpageurl->out(false),
                           'title' => get_string('compressed', 'format_ned')
                        ));
                    $mainpageurl->param(\format_ned\toolbox::$compressedsectionsparam, \format_ned\toolbox::$allexpanded);
                    $o .= html_writer::tag('a',
                        $this->pix_icon('expanded', '', 'format_ned'),
                        array('id' => 'nededitingsectionexpanded', 'href' => $mainpageurl->out(false),
                           'title' => get_string('expanded', 'format_ned')
                        ));
                }
            }
        }

        $o .= html_writer::end_tag('div');

        return $o;
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
        $this->displaysection = $displaysection;
        $modinfo = get_fast_modinfo($course);

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $context = context_course::instance($course->id);

        echo $this->get_ned_course_header($course, $context, $displaysection);

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection, $course->id);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $this->editing) {
            if ($this->showsection0()) {
                echo $this->start_section0_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $this->display_completion_help_icon($completioninfo, $course->id, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section ned-single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title without section navigation links.
        if ($this->settings['sectionformat'] == 0) {
            $sectiontitle = html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
            // Title attributes.
            $classes = 'sectionname';
            if (!$thissection->visible) {
                $classes .= ' dimmed_text';
            }
            $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
            $sectiontitle .= $this->output->heading($sectionname, 3, $classes);
            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;
        }

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);

        // Show completion help icon.
        if (empty($completioninfo)) {
            $completioninfo = new completion_info($course);
        }
        echo $this->display_completion_help_icon($completioninfo, $course->id, $displaysection);
        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Always section bottom navigation on a single section page.
        $viewsectionforwardbacklinks = get_config('format_ned', 'viewsectionforwardbacklinks');
        if (($viewsectionforwardbacklinks == 0) ||
            (($viewsectionforwardbacklinks == 1) && (has_capability('moodle/course:update', $context)))) {
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectionbottomnav = html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));

            echo html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
            echo $sectionbottomnav;
            echo html_writer::end_tag('div');
        }

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

        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard.
        echo $this->course_activity_clipboard($course, 0);

        echo $this->get_ned_course_header($course, $context, false);

        $numsections = $this->courseformat->get_last_section_number();

        // Section 0.
        if ($this->showsection0() || $this->editing) {
            // Section 0 is displayed a little different then the others.
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or !empty($modinfo->sections[0]) or $this->editing) {
                echo $this->start_section0_list();
                echo $this->section_header($thissection, $course, false, 0);
                // Don't display on the multiple section list page when "One section per page".
                if ($course->coursedisplay == COURSE_DISPLAY_SINGLEPAGE) {
                    // Possibly show completion help icon once.
                    $completioninfo = new completion_info($course);
                    echo $this->display_completion_help_icon($completioninfo, $course->id);
                }
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }
        if ($this->settings['showsection0'] == 2) {
            $numsections = 0; // Effectively don't show the other sections, only 0.
        }

        // Now the list of sections.
        echo $this->start_section_list();

        $section = 1;
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);

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
            } else {
                if (!$this->editing && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                    // Display section summary only.
                    echo $this->section_summary($thissection, $course, null);
                } else {
                    echo $this->section_header($thissection, $course, false, 0);
                    if ($thissection->uservisible) {
                        // Don't display on the multiple section list page when "One section per page".
                        if ($course->coursedisplay == COURSE_DISPLAY_SINGLEPAGE) {
                            // Possibly show completion help icon once.
                            if (empty($completioninfo)) {
                                $completioninfo = new completion_info($course);
                            }
                            echo $this->display_completion_help_icon($completioninfo, $course->id);
                        }
                        echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                        echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                    }
                    echo $this->section_footer();
                }
            }

            $section++;
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
