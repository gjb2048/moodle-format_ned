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

        $choices = array(
            0 => get_string('sectionformatmoodle', 'format_ned'),
            1 => get_string('sectionformatframed', 'format_ned'),
            2 => get_string('sectionformatframedcustom', 'format_ned'),
            3 => get_string('sectionformatframedpreformatted', 'format_ned')
        );
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
        $managecolourpresetshtmlurl = new moodle_url('/course/format/ned/colourpreset.php',
            array('courseid' => $this->_customdata['courseid']));
        $managecolourpresetshtml = '<a href="'.$managecolourpresetshtmlurl.'" class="btn btn-secondary">'.
            get_string('managecolourpresets', 'format_ned').'</a>';
        $mform->addElement('html', $managecolourpresetshtml);
        $mform->addElement('html', '</div>');

        $mform->addElement('html', '<div id="nedsectionheaderformats">');

        $sectionheaderformatslabelsgroup = array();
        $sectionheaderformatsnamelabelscontent = '<div class="nedhfeditcolumns">';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflname', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shfllc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflmc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflrc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('colourpreset', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '</div>';
        $sectionheaderformatslabelsgroup[] =& $mform->createElement('static', 'hflcolumns', '', $sectionheaderformatsnamelabelscontent);
        $mform->addGroup($sectionheaderformatslabelsgroup, 'sectionheaderformatslabelsgroup', get_string('sectionheaderformats', 'format_ned'),
            array('<span class="nedshfsep"></span>'), false);

        $shfrows = array('sectionheaderformatone', 'sectionheaderformattwo', 'sectionheaderformatthree');
        foreach ($shfrows as $shfrow) {
            $shfgroupdataname = $shfrow.'data';
            $shfgroupname = $shfrow.'group';
            $$shfgroupdataname = array();

            // Note: Changed from 'html' type to 'static' for the Clean theme.
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditcolumnsstart'.$shfrow, '', '<div class="nedhfeditcolumns">');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditcolumnstart'.$shfrow, '', '<div class="nedhfeditcolumn">');
            // Active.
            ${$shfgroupdataname}[] =& $mform->createElement('checkbox', $shfrow.'active', null, '');
            if (!empty($shfdata[$shfrow]['active'])) {
                $mform->setDefault($shfrow.'active', 'checked');
            }

            // Name.
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditleftcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            ${$shfgroupdataname}[] =& $mform->createElement('text', $shfrow.'name');
            $mform->setDefault($shfrow.'name', $shfdata[$shfrow]['name']);
            $mform->setType($shfrow.'name', PARAM_TEXT);
            $mform->disabledIf($shfrow.'name', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditnamecolumn'.$shfrow, '', '</div></div><div class="nedhfeditcolumn">');

            // Left column active.
            ${$shfgroupdataname}[] =& $mform->createElement('checkbox', $shfrow.'leftcolumnactive', null, '');
            if (!empty($shfdata[$shfrow]['leftcolumn']['active'])) {
                $mform->setDefault($shfrow.'leftcolumnactive', 'checked');
            }
            $mform->disabledIf($shfrow.'leftcolumnactive', $shfrow.'active');

            // Left column value.
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditleftcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            ${$shfgroupdataname}[] =& $mform->createElement('text', $shfrow.'leftcolumnvalue');
            $mform->setDefault($shfrow.'leftcolumnvalue', $shfdata[$shfrow]['leftcolumn']['value']);
            $mform->setType($shfrow.'leftcolumnvalue', PARAM_TEXT);
            $mform->disabledIf($shfrow.'leftcolumnvalue', $shfrow.'leftcolumnactive');
            $mform->disabledIf($shfrow.'leftcolumnvalue', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditleftcolumn'.$shfrow, '', '</div></div><div class="nedhfeditcolumn">');

            // Middle column active.
            ${$shfgroupdataname}[] =& $mform->createElement('checkbox', $shfrow.'middlecolumnactive', null, '');
            if (!empty($shfdata[$shfrow]['middlecolumn']['active'])) {
                $mform->setDefault($shfrow.'middlecolumnactive', 'checked');
            }
            $mform->disabledIf($shfrow.'middlecolumnactive', $shfrow.'active');

            // Middle column value.
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditmiddlecolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            ${$shfgroupdataname}[] =& $mform->createElement('text', $shfrow.'middlecolumnvalue');
            $mform->setDefault($shfrow.'middlecolumnvalue', $shfdata[$shfrow]['middlecolumn']['value']);
            $mform->setType($shfrow.'middlecolumnvalue', PARAM_TEXT);
            $mform->disabledIf($shfrow.'middlecolumnvalue', $shfrow.'middlecolumnactive');
            $mform->disabledIf($shfrow.'middlecolumnvalue', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditmiddlecolumn'.$shfrow, '', '</div></div><div class="nedhfeditcolumn">');

            // Right column active.
            ${$shfgroupdataname}[] =& $mform->createElement('checkbox', $shfrow.'rightcolumnactive', null, '');
            if (!empty($shfdata[$shfrow]['rightcolumn']['active'])) {
                $mform->setDefault($shfrow.'rightcolumnactive', 'checked');
            }
            $mform->disabledIf($shfrow.'rightcolumnactive', $shfrow.'active');

            // Right column value.
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditrightcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            ${$shfgroupdataname}[] =& $mform->createElement('text', $shfrow.'rightcolumnvalue');
            $mform->setDefault($shfrow.'rightcolumnvalue', $shfdata[$shfrow]['rightcolumn']['value']);
            $mform->setType($shfrow.'rightcolumnvalue', PARAM_TEXT);
            $mform->disabledIf($shfrow.'rightcolumnvalue', $shfrow.'rightcolumnactive');
            $mform->disabledIf($shfrow.'rightcolumnvalue', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditrightcolumn'.$shfrow, '', '</div></div><div class="nedhfeditcolumn">');

            // Colour preset.
            $shfrowcolourpreset = $shfrow.'colourpreset';
            $shfrowcolourpresetarray = array(-1 => get_string('shfldefault', 'format_ned'));
            foreach ($colourpresetitems as $cpikey => $cpivalue) {
                $shfrowcolourpresetarray[$cpikey] = $cpivalue;
            }
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditcolourcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            $$shfrowcolourpreset =& $mform->createElement('select', $shfrow.'colourpreset', null, $shfrowcolourpresetarray);
            ${$shfgroupdataname}[] = $$shfrowcolourpreset;
            // Does not work, despite the documentation! $$shfrowcolourpreset->setSelected($shfrow.'colourpreset', $shfdata[$shfrow]['colourpreset']);
            $mform->setDefault($shfrow.'colourpreset', $shfdata[$shfrow]['colourpreset']);
            $mform->disabledIf($shfrow.'colourpreset', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditend'.$shfrow, '', '</div></div></div>');
            $mform->addGroup($$shfgroupdataname, $shfgroupname, '', array('<span class="nedshfsep"></span>'), false);
        }

        $mform->addElement('html', '</div>');
        unset($colourpresetitems);

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

        $activityresourcemouseoveroptions = array(
            0 => get_string('hide'),
            1 => get_string('show')
        );
        $mform->addElement('select', 'activityresourcemouseover',
            get_string('activityresourcemouseover', 'format_ned'), $activityresourcemouseoveroptions
        );
        unset($activityresourcemouseoveroptions);

        $mform->addElement('hidden', 'nedsettingsform', 1);
        $mform->setType('nedsettingsform', PARAM_INT);

        $this->add_action_buttons();
    }

}