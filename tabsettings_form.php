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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/edit_form.php');

class course_ned_edit_form extends moodleform {

    public function definition() {
        global $DB;
        $mform = &$this->_form;

        $course = $this->_customdata['course'];

        $mform->addElement('hidden', 'id', $this->_customdata['course']->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'defaulttabwhenset', time());
        $mform->setType('defaulttabwhenset', PARAM_INT);

        $mform->addElement('header', 'fncoursetabs', 'Tabs');

        $mform->addElement('static', 'blockinfo', get_string('blockinfo', 'format_ned'),
            '<a target="_blank" href="http://ned.ca/tabs">http://ned.ca/tabs</a>');

        $showhideoptions = array(
            '1' => get_string('show', 'format_ned'),
            '0' => get_string('hide', 'format_ned')
        );
        $mform->addElement('select', 'showtabs', get_string('tabs', 'format_ned'),
            $showhideoptions);
        $mform->setDefault('showtabs', 1);

        $mform->addElement('select', 'completiontracking', get_string('completiontracking', 'format_ned'),
            $showhideoptions);

        // For mainheading for the course.
        $label = get_string('mainheading', 'format_ned');
        $mform->addElement('text', 'mainheading', $label, 'maxlength="24" size="25"');
        $mform->setDefault('mainheading', get_string('defaultmainheading', 'format_ned'));
        $mform->setType('mainheading', PARAM_TEXT);

        // For topic heading for example Week Section.
        $label = get_string('topicheading', 'format_ned');
        $mform->addElement('text', 'topicheading', $label, 'maxlength="24" size="25"');
        $mform->setDefault('topicheading', get_string('defaulttopicheading', 'format_ned'));
        $mform->setType('topicheading', PARAM_TEXT);

        $tabcontentoptions = array(
            'usesectionnumbers' => get_string('usesectionnumbers', 'format_ned'),
            'usesectiontitles' => get_string('usesectiontitles', 'format_ned')
        );
        $mform->addElement('select', 'tabcontent', get_string('tabcontent', 'format_ned'), $tabcontentoptions);

        // For changing the number of tab to show before next link.
        $numberoftabs = array();
        for ($i = 12; $i <= 20; $i++) {
            $numberoftabs[$i] = $i;
        }

        $mform->addElement('select', 'maxtabs', get_string('setnumberoftabs', 'format_ned'), $numberoftabs);
        $mform->setDefault('maxtabs', $numberoftabs[12]);

        // Work to be done for default tab.
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'defaulttab', '',
            get_string('default_tab_text', 'format_ned'), 'option1',
            array('checked' => true, 'class' => 'padding_before_radio', 'style' => 'padding-left:10px;')
        );
        // Add second option if the course completion is enabled.
        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            $radioarray[] = $mform->createElement('radio', 'defaulttab', '',
                get_string('default_tab_notattempted_text', 'format_ned'), 'option2');
        }

        $radioarray[] = $mform->createElement('radio', 'defaulttab', '',
            get_string('default_tab_specifyweek_text', 'format_ned'), 'option3');
        $mform->addGroup($radioarray, 'radioar', get_string('label_deafulttab_text', 'format_ned'), array('<br />'), false);
        $mform->setDefault('defaulttab', 'option1');

        $timenow = time();
        $weekdate = $course->startdate;
        $weekdate += 7200;
        $weekofseconds = 604800;
        $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);

        // Calculate the current week based on today's date and the starting date of the course.
        $currentweek = ($timenow > $course->startdate) ? (int) ((($timenow - $course->startdate) / $weekofseconds) + 1) : 0;

        $currentweek = min($currentweek, $course->numsections);
        $topiclist = array();
        if ($currentweek > 0) {
            for ($i = 1; $i <= $currentweek; $i++) {
                $topiclist[$i] = $i;
            }
        } else {
            $topiclist[1] = 1;
        }

        $mform->addElement('select', 'topictoshow', '', $topiclist, array('class' => 'ddl_padding'));
        $mform->setDefault('topictoshow', $topiclist[1]);

        $mform->addElement('header', 'fncoursecolours', get_string('colours', 'format_ned'));

        $colourschemaoptions = $DB->get_records_menu('format_ned_colour');

        $saveasarray = array();
        $colourschemaselect = &$mform->createElement('select', 'colourschema', '', $colourschemaoptions);
        $colourschemaselect->setSelected($this->_customdata['colourschema']);
        $saveasarray[] = $colourschemaselect;
        $saveasarray[] = &$mform->createElement('button', 'managecolourschemas',
            get_string('managecolourschemas', 'format_ned')
        );
        $mform->addGroup($saveasarray, 'saveasarr', get_string('loadcolourschema', 'format_ned'), array(' '), false);

        $mform->addElement('header', 'sections', get_string('sections', 'format_ned'));

        $choices['0'] = get_string("hide");
        $choices['1'] = get_string("show");
        $label = get_string('showsection0', 'format_ned');
        $mform->addElement('select', 'showsection0', $label, $choices);
        $mform->setDefault('showsection0', $choices['0']);
        unset($choices);

        $choices['0'] = get_string("no");
        $choices['1'] = get_string("yes");
        $label = get_string('showonlysection0', 'format_ned');
        $mform->addElement('select', 'showonlysection0', $label, $choices);
        $mform->setDefault('showonlysection0', $choices['0']);
        unset($choices);

        $activitytrackingbackgroundoptions = array(
            '1' => get_string('show', 'format_ned'),
            '0' => get_string('hide', 'format_ned')
        );
        $mform->addElement('select', 'activitytrackingbackground',
            get_string('activitytrackingbackground', 'format_ned'), $activitytrackingbackgroundoptions
        );

        $locationoftrackingiconsoptions = array(
            'moodleicons' => get_string('moodleicons', 'format_ned'),
            'nediconsleft' => get_string('nediconsleft', 'format_ned'),
            'nediconsright' => get_string('nediconsright', 'format_ned'),
        );
        $mform->addElement('select', 'locationoftrackingicons',
            get_string('locationoftrackingicons', 'format_ned'), $locationoftrackingiconsoptions
        );

        $choices['0'] = get_string("no");
        $choices['1'] = get_string("yes");
        $label = get_string('showorphaned', 'format_ned');
        $mform->addElement('select', 'showorphaned', $label, $choices);
        $mform->setDefault('showorphaned', $choices['0']);
        unset($choices);

        $this->add_action_buttons();
    }

}