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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/edit_form.php');

class course_ned_edit_form extends moodleform {

    public function definition() {
        $mform = &$this->_form;

        $mform->addElement('hidden', 'id', $this->_customdata['courseid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'nedformat', get_string('format', 'format_ned'));

        $mform->addElement('static', 'formatinfo', get_string('formatinfo', 'format_ned'),
            '<a target="_blank" href="//ned.ca/ned-format">ned.ca/ned-format</a>');

        $choices = array(
            0 => get_string('sectionformatmoodle', 'format_ned'),
            1 => get_string('sectionformatframed', 'format_ned')
        );
        $label = get_string('sectionformat', 'format_ned');
        $mform->addElement('select', 'sectionformat', $label, $choices);
        unset($choices);

        $choices = array(
            0 => get_string('hide'),
            1 => get_string('showsectionheader', 'format_ned'),
            2 => get_string('showsectionbody', 'format_ned')
        );
        $label = get_string('sectionnamelocation', 'format_ned');
        $mform->addElement('select', 'sectionnamelocation', $label, $choices);
        unset($choices);
        $mform->disabledIf('sectionnamelocation', 'sectionformat', 'neq', 1);

        $choices = array(
            0 => get_string('showsectionheader', 'format_ned'),
            1 => get_string('showsectionbody', 'format_ned')
        );
        $label = get_string('sectionsummarylocation', 'format_ned');
        $mform->addElement('select', 'sectionsummarylocation', $label, $choices);
        unset($choices);
        $mform->disabledIf('sectionsummarylocation', 'sectionformat', 'neq', 1);

        /*$colourpresetelements = array();
        $colourpresetitems = array(
            1 => get_string('colourpresetmoodle', 'format_ned'),
            2 => get_string('colourpresetembassygreen', 'format_ned'),
            3 => get_string('colourpresetbluesonwhyte', 'format_ned')
        );
        $label = get_string('colourpreset', 'format_ned');
        $colourpresetelements[] =& $mform->createElement('select', 'colourpreset', '', $colourpresetitems);
        //unset($colourpresetitems);
        $managecolourpresetshtml = '<a href="#" class="btn">'.get_string('managecolourpresets', 'format_ned').'</a>';
        $colourpresetelements[] =& $mform->createElement('html', $managecolourpresetshtml);
        $mform->addGroup($colourpresetelements, 'colourpresetelements', $label, array(' '), false);
		*/
        $mform->addElement('html', '<div class="managecolourpresets">');
        // Temporary list until DB.
        $colourpresetitems = array(
            0 => get_string('colourpresetmoodle', 'format_ned'),
            1 => 'Embassy Green',
            2 => 'Blues on Whyte'
        );
        $label = get_string('colourpreset', 'format_ned');
        $mform->addElement('select', 'colourpreset', $label, $colourpresetitems);
        unset($colourpresetitems);
        $managecolourpresetshtml = '<a href="#" class="btn btn-secondary">'.get_string('managecolourpresets', 'format_ned').'</a>';
        $mform->addElement('html', $managecolourpresetshtml);
        $mform->addElement('html', '</div>');


        $mform->addElement('header', 'nedformat', get_string('othersettings', 'format_ned'));

        $choices = array(
            0 => get_string('hide'),
            1 => get_string('show'),
            2 => get_string('showonlysection0', 'format_ned')
        );
        $label = get_string('showsection0', 'format_ned');
        $mform->addElement('select', 'showsection0', $label, $choices);
        unset($choices);

        $activitytrackingbackgroundoptions = array(
            0 => get_string('hide'),
            1 => get_string('show')
        );
        $mform->addElement('select', 'activitytrackingbackground',
            get_string('activitytrackingbackground', 'format_ned'), $activitytrackingbackgroundoptions
        );
        unset($activitytrackingbackgroundoptions);

        $locationoftrackingiconsoptions = array(
            \format_ned\toolbox::$moodleicons => get_string('moodleicons', 'format_ned'),
            \format_ned\toolbox::$nediconsleft => get_string('nediconsleft', 'format_ned'),
            \format_ned\toolbox::$nediconsright => get_string('nediconsright', 'format_ned')
        );
        $mform->addElement('select', 'locationoftrackingicons',
            get_string('locationoftrackingicons', 'format_ned'), $locationoftrackingiconsoptions
        );
        unset($locationoftrackingiconsoptions);

        // Hide!
        if (false) {
            $choices = array(
                0 => get_string('no'),
                1 => get_string('yes')
            );
            $label = get_string('sectioncontentjustification', 'format_ned');
            $mform->addElement('select', 'sectioncontentjustification', $label, $choices);
            unset($choices);
        }

        $viewjumptomenuoptions = array(
            0 => get_string('everyone', 'format_ned'),
            1 => get_string('courseeditors', 'format_ned'),
            2 => get_string('nobody', 'format_ned')
        );
        $mform->addElement('select', 'viewjumptomenu',
            get_string('viewjumptomenu', 'format_ned'), $viewjumptomenuoptions
        );
        unset($viewjumptomenuoptions);

        $viewsectionforwardbacklinksoptions = array(
            0 => get_string('everyone', 'format_ned'),
            1 => get_string('courseeditors', 'format_ned'),
            2 => get_string('nobody', 'format_ned')
        );
        $mform->addElement('select', 'viewsectionforwardbacklinks',
            get_string('viewsectionforwardbacklinks', 'format_ned'), $viewsectionforwardbacklinksoptions
        );
        unset($viewsectionforwardbacklinksoptions);

        $progresstooltipoptions = array(
            0 => get_string('hideicon', 'format_ned'),
            1 => get_string('hidemanualcompletion', 'format_ned'),
            2 => get_string('showalldescriptions', 'format_ned')
        );
        $mform->addElement('select', 'progresstooltip',
            get_string('progresstooltip', 'format_ned'), $progresstooltipoptions
        );
        unset($progresstooltipoptions);

        $this->add_action_buttons();
    }

}