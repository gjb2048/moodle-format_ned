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

        $mform->addElement('header', 'nedformat', get_string('othersettings', 'format_ned'));

        $choices = array();
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

        $choices = array(
            0 => get_string('no'),
            1 => get_string('yes')
        );
        $label = get_string('sectioncontentjustification', 'format_ned');
        $mform->addElement('select', 'sectioncontentjustification', $label, $choices);
        unset($choices);

        $this->add_action_buttons();
    }

}