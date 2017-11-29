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
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/course/edit_form.php');

class course_ned_edit_form extends moodleform {

    public function definition() {
        $mform = &$this->_form;

        $shfdata = $this->_customdata['sectionheaderformats'];

        $mform->addElement('hidden', 'id', $this->_customdata['courseid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'nedformat', get_string('format', 'format_ned'));

        $mform->addElement('static', 'formatinfo', get_string('formatinfo', 'format_ned'),
            '<a target="_blank" href="//ned.ca/ned-format">ned.ca/ned-format</a>');

        if (is_siteadmin()) {
            $sitesettings = new moodle_url('/admin/settings.php?section=formatsettingned');
            $mform->addElement('static', 'sitewidesettings', get_string('sitewidesettings', 'format_ned'),
                '<a href="'.$sitesettings.'">'.get_string('opensitesettings', 'format_ned').'</a>');
        }

        $choices = array(
            0 => get_string('sectionformatmoodle', 'format_ned'),
            1 => get_string('sectionformatframed', 'format_ned'),
            3 => get_string('sectionformatframedformatted', 'format_ned')
        );
        if (get_config('format_ned', 'framedsectionscustomheader') == 1) { // Show - see settings.php.
            $choices[2] = get_string('sectionformatframedcustom', 'format_ned');
        }
        $label = get_string('sectionformat', 'format_ned');
        $mform->addElement('select', 'sectionformat', $label, $choices);
        unset($choices);

        $mform->addElement('html', '<div id="nedsectionlocation">');
        $choices = array(
            0 => get_string('hide'),
            1 => get_string('showsectionheader', 'format_ned'),
            2 => get_string('showsectionbody', 'format_ned')
        );
        $label = get_string('sectionnamelocation', 'format_ned');
        $mform->addElement('select', 'sectionnamelocation', $label, $choices);
        unset($choices);
        $mform->disabledIf('sectionnamelocation', 'sectionformat', 'neq', 2);

        $choices = array(
            0 => get_string('showsectionheader', 'format_ned'),
            1 => get_string('showsectionbody', 'format_ned')
        );
        $label = get_string('sectionsummarylocation', 'format_ned');
        $mform->addElement('select', 'sectionsummarylocation', $label, $choices);
        unset($choices);
        $mform->disabledIf('sectionsummarylocation', 'sectionformat', 'neq', 2);
        $mform->addElement('html', '</div>');

        // List of colour presets.
        global $DB;
        $colourpresetitems = array(0 => get_string('colourpresetmoodle', 'format_ned'));
        if ($presets = $DB->get_records('format_ned_colour', null, null, 'id,name')) {
            foreach ($presets as $preset) {
                $colourpresetitems[$preset->id] = $preset->name;
            }
        } else {
            $colourpresetitems[1] = 'Embassy Green';
            $colourpresetitems[2] = 'Blues on Whyte';
        }

        $mform->addElement('html', '<div id="managecolourpresets">');

        $label = get_string('colourpreset', 'format_ned');
        $mform->addElement('select', 'colourpreset', $label, $colourpresetitems);

        static $shfrows = array('sectionheaderformatone', 'sectionheaderformattwo', 'sectionheaderformatthree');
        $sectionheaderformatsnedefaultformatname = array();
        foreach ($shfrows as $shfrow) {
            if (($shfdata[$shfrow]['active'] == 1) && ($shfdata[$shfrow]['colourpreset'] == -1)) { // Active and NED Default.
                $sectionheaderformatsnedefaultformatname[] = $shfdata[$shfrow]['name'];
            }
        }
        if (!empty($sectionheaderformatsnedefaultformatname)) {
            // Must be > 0 and <= 3.
            $sectionheaderformatsnedefaultformatnamecount = count($sectionheaderformatsnedefaultformatname);
            switch ($sectionheaderformatsnedefaultformatnamecount) {
                case 1:
                    $shfnfnames = get_string('appliestoone', 'format_ned', array('one' => $sectionheaderformatsnedefaultformatname[0]));
                break;
                case 2:
                    $shfnfnames = get_string('appliestotwo', 'format_ned', array(
                        'one' => $sectionheaderformatsnedefaultformatname[0],
                        'two' => $sectionheaderformatsnedefaultformatname[1]
                    ));
                break;
                case 3:
                    $shfnfnames = get_string('appliestothree', 'format_ned', array(
                        'one' => $sectionheaderformatsnedefaultformatname[0],
                        'two' => $sectionheaderformatsnedefaultformatname[1],
                        'three' => $sectionheaderformatsnedefaultformatname[2]
                    ));
                break;
            }
            $mform->addElement('html', '<span id="cpappliesto">'.get_string('appliesto', 'format_ned').$shfnfnames.'</span>');
        }

        $mform->addElement('html', '</div>');

        $mform->addElement('header', 'nedformat', get_string('othersettings', 'format_ned'));

        $choices = array(
            0 => get_string('hide'),
            1 => get_string('show'),
            3 => get_string('showsection0editmode', 'format_ned'),
            2 => get_string('showonlysection0', 'format_ned')
        );
        $label = get_string('showsection0', 'format_ned');
        $mform->addElement('select', 'showsection0', $label, $choices);
        unset($choices);

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

        $mform->addElement('hidden', 'nedsettingsform', 1);
        $mform->setType('nedsettingsform', PARAM_INT);

        $this->add_action_buttons();
    }

}