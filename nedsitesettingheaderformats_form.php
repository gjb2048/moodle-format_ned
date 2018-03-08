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
require_once($CFG->dirroot.'/course/format/ned/lib.php');

class course_ned_sitesettingheaderformats_form extends moodleform {

    public function definition() {
        $mform = &$this->_form;

        $shfdata = $this->_customdata['sectionheaderformats'];

        $mform->addElement('static', 'formatinfo', get_string('formatinfo', 'format_ned'),
            '<a target="_blank" href="//ned.ca/ned-format">ned.ca/ned-format</a>');

        // List of colour presets.
        global $DB;
        $colourpresetitems = array(0 => get_string('colourpresetformattheme', 'format_ned'));
        if ($presets = $DB->get_records('format_ned_colour', null, null, 'id,name')) {
            foreach ($presets as $preset) {
                $colourpresetitems[$preset->id] = $preset->name;
            }
        } else {
            $colourpresetitems[1] = 'Embassy Green';
            $colourpresetitems[2] = 'Blues on Whyte';
        }

        // List of Navigation titles.
        $navigationtitleitems = array(
            1 => get_string('shfntl', 'format_ned'),
            2 => get_string('shfntm', 'format_ned'),
            3 => get_string('shfntr', 'format_ned')
        );

        $mform->addElement('html', '<div id="nedsectionheaderformats">');

        $sectionheaderformatslabelsgroup = array();
        $sectionheaderformatsnamelabelscontent = '<div class="nedhfeditcolumns">';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflname', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shfllc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflmc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shflrc', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('colourpreset', 'format_ned').'</span>';
        $sectionheaderformatsnamelabelscontent .= '<span class="nedhfeditcolumn">'.get_string('shfnt', 'format_ned').'</span>';
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
            $shfrowcolourpresetarray = array();
            foreach ($colourpresetitems as $cpikey => $cpivalue) {
                $shfrowcolourpresetarray[$cpikey] = $cpivalue;
            }
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditcolourcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            $$shfrowcolourpreset =& $mform->createElement('select', $shfrow.'colourpreset', null, $shfrowcolourpresetarray);
            ${$shfgroupdataname}[] = $$shfrowcolourpreset;
            // Does not work, despite the documentation! $$shfrowcolourpreset->setSelected($shfrow.'colourpreset', $shfdata[$shfrow]['colourpreset']);
            $mform->setDefault($shfrow.'colourpreset', $shfdata[$shfrow]['colourpreset']);
            $mform->disabledIf($shfrow.'colourpreset', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditcolourcolumn'.$shfrow, '', '</div></div><div class="nedhfeditcolumn">');

            // Navigation titles.
            $shfrownavigationtitle = $shfrow.'navigationtitle';
            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditnavigationcolumnvalue'.$shfrow, '', '<div class="nedhfeditcolumnvalue">');
            $$shfrownavigationtitle =& $mform->createElement('select', $shfrow.'navigationtitle', null, $navigationtitleitems);
            ${$shfgroupdataname}[] = $$shfrownavigationtitle;

            if (empty($shfdata[$shfrow]['navigationtitle'])) {
                $navigationtitledefaults = format_ned::get_section_header_formats_default_setting(true);
                $navigationtitledefault = $navigationtitledefaults[$shfrow]['navigationtitle'];
            } else {
                $navigationtitledefault = $shfdata[$shfrow]['navigationtitle'];
            }
            $mform->setDefault($shfrow.'navigationtitle', $navigationtitledefault);
            $mform->disabledIf($shfrow.'navigationtitle', $shfrow.'active');

            ${$shfgroupdataname}[] =& $mform->createElement('static', 'nedhfeditend'.$shfrow, '', '</div></div></div>');

            $mform->addGroup($$shfgroupdataname, $shfgroupname, '', array('<span class="nedshfsep"></span>'), false);
        }
        unset($colourpresetitems);
        unset($navigationtitleitems);

        $choices = array(
            0 => get_string('no'),
            1 => get_string('yes')
        );
        $label = get_string('shfmclt', 'format_ned');
        $mform->addElement('select', 'shfmclt', $label, $choices);
        $mform->setDefault('shfmclt', $shfdata['shfmclt']);
        unset($choices);

        $mform->addElement('html', '</div>');

        $this->add_action_buttons();
    }

}