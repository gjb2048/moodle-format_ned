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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');  // Must be included from a Moodle page.
}

require_once($CFG->dirroot. '/course/editsection_form.php');

/**
 * Form for editing course section
 */
class nededitsection_form extends editsection_form {

    public function definition() {

        $mform  = $this->_form;
        $course = $this->_customdata['course'];
        $sectioninfo = $this->_customdata['cs'];

        $mform->addElement('header', 'generalhdr', get_string('general'));

        /* Note: From 'update_section_format_options($data)' - If $data does not contain property with the option name,
                 the option will not be updated, therefore we still need it! */

        $mform->addElement('hidden', 'name', $sectioninfo->name);
        $mform->setType('name', PARAM_RAW);

        $mform->addElement('hidden', 'sectionno', $sectioninfo->section);
        $mform->setType('sectionno', PARAM_INT);

        $mform->addElement('html', '<div id="sectionheaderformat">');

        global $PAGE;
        $courseformat = course_get_format($course);
        $sectionheaderformats = $courseformat->get_setting('sectionheaderformats');
        $sectionheaderformat = $courseformat->get_setting('sectionheaderformat', $sectioninfo->section);
        $defaultstring = get_string('default');
        $defaultvalue = $courseformat->get_section_name_noshf($sectioninfo->section);
        $shfrows = array(1 => 'sectionheaderformatone', 2 => 'sectionheaderformattwo', 3 => 'sectionheaderformatthree');
        $formatchoices = array();
        $sectionheaderformatsdata = array();
        foreach ($shfrows as $shfrowskey => $shfrowsvalue) {
            if ($sectionheaderformats[$shfrowsvalue]['active'] == 1) {
                $formatchoices[$shfrowskey] = $sectionheaderformats[$shfrowsvalue]['name'];
                $sectionheaderformatsdata[$shfrowskey] = array();
                $sectionheaderformatsdata[$shfrowskey]['leftcolumn'] = array();
                $sectionheaderformatsdata[$shfrowskey]['leftcolumn']['active'] = $sectionheaderformats[$shfrowsvalue]['leftcolumn']['active'];
                $sectionheaderformatsdata[$shfrowskey]['leftcolumn']['value'] = $sectionheaderformats[$shfrowsvalue]['leftcolumn']['value'];
                if (empty($sectionheaderformatsdata[$shfrowskey]['leftcolumn']['value'])) {
                    $sectionheaderformatsdata[$shfrowskey]['leftcolumn']['value'] = '&nbsp;';
                }
                $sectionheaderformatsdata[$shfrowskey]['middlecolumn'] = array();
                $sectionheaderformatsdata[$shfrowskey]['middlecolumn']['active'] = $sectionheaderformats[$shfrowsvalue]['middlecolumn']['active'];
                $sectionheaderformatsdata[$shfrowskey]['middlecolumn']['value'] = $sectionheaderformats[$shfrowsvalue]['middlecolumn']['value'];
                if (empty($sectionheaderformatsdata[$shfrowskey]['middlecolumn']['value'])) {
                    $sectionheaderformatsdata[$shfrowskey]['middlecolumn']['value'] = '&nbsp;';
                }
                $sectionheaderformatsdata[$shfrowskey]['rightcolumn'] = array();
                $sectionheaderformatsdata[$shfrowskey]['rightcolumn']['active'] = $sectionheaderformats[$shfrowsvalue]['rightcolumn']['active'];
                $sectionheaderformatsdata[$shfrowskey]['rightcolumn']['value'] = $sectionheaderformats[$shfrowsvalue]['rightcolumn']['value'];
                if (empty($sectionheaderformatsdata[$shfrowskey]['rightcolumn']['value'])) {
                    $sectionheaderformatsdata[$shfrowskey]['rightcolumn']['value'] = '&nbsp;';
                }
                $sectionheaderformatsdata[$shfrowskey]['navigationname'] = array();
                if ($sectionheaderformats[$shfrowsvalue]['leftcolumn']['active'] == 1) {
                    $sectionheaderformatsdata[$shfrowskey]['navigationname'][1] = $sectionheaderformats[$shfrowsvalue]['leftcolumn']['value'];
                }
                if ($sectionheaderformats[$shfrowsvalue]['middlecolumn']['active'] == 1) {
                    $sectionheaderformatsdata[$shfrowskey]['navigationname'][2] = $sectionheaderformats[$shfrowsvalue]['middlecolumn']['value'];
                }
                if ($sectionheaderformats[$shfrowsvalue]['rightcolumn']['active'] == 1) {
                    $sectionheaderformatsdata[$shfrowskey]['navigationname'][3] = $sectionheaderformats[$shfrowsvalue]['rightcolumn']['value'];
                }
            }
        }
        $label = get_string('sectionheaderformat', 'format_ned');
        $mform->addElement('select', 'sectionheaderformat', $label, $formatchoices);
        $mform->setDefault('sectionheaderformat', $sectionheaderformat['headerformat']);
        unset($formatchoices);

        $sectionnamenavblockvaluedata = array(
            0 => $defaultvalue,
            1 => $sectionheaderformat['sectionname']['leftcolumn'],
            2 => $sectionheaderformat['sectionname']['middlecolumn'],
            3 => $sectionheaderformat['sectionname']['rightcolumn']
        );
        $PAGE->requires->js_call_amd('format_ned/nededitsectionform', 'init',
            array('data' => array(
                'sectionheaderformatsdata' => $sectionheaderformatsdata,
                'sectionnamenavblockvaluedata' => $sectionnamenavblockvaluedata[0], // JS gets the others from the values of the inputs.
                'defaultstring' => $defaultstring)
            )
        );

        // Section name in navigation block.
        $mform->addElement('html', '<div id="sectionnamenavblock">');
        $sectionnav = array(0 => $defaultstring); // 0 = Default, 1 = left column, 2 = middle column and 3 = right column.
        /* Only add the column name as an option if it is active.  The assocated nededitsectionform.js does this dynamically when
           'sectionheaderformat' changes. */
        if ($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['leftcolumn']['active'] == 1) {
            $sectionnav[1] = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['leftcolumn']['value'];
        }
        if ($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['middlecolumn']['active'] == 1) {
            $sectionnav[2] = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['middlecolumn']['value'];
        }
        if ($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['rightcolumn']['active'] == 1) {
            $sectionnav[3] = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['rightcolumn']['value'];
        }
        $label = get_string('shflnavigationname', 'format_ned');
        $mform->addElement('select', 'navigationname', $label, $sectionnav);
        $mform->setDefault('navigationname', $sectionheaderformat['navigationname']);
        unset($sectionnav);
        $mform->addElement('html', '<div id="sectionnamenavblockvalue">'.$sectionnamenavblockvaluedata[$sectionheaderformat['navigationname']].'</div>');
        $mform->addElement('html', '</div>');

        // Section name.
        $sectionheaderformatnamelabelsgroup = array();
        $sectionheaderformatnamelabelscontent = '<div class="nedshfeditcolumns">';
        $nedshfeditleftcolumnvalue = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['leftcolumn']['value'];
        if (empty($nedshfeditleftcolumnvalue)) {
            $nedshfeditleftcolumnvalue = '&nbsp;';
        }
        $sectionheaderformatnamelabelscontent .= '<span id="nedshfleftlabel" class="nedshfeditleftcolumn">'.$nedshfeditleftcolumnvalue.'</span>';
        $nedshfeditmiddlecolumnvalue = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['middlecolumn']['value'];
        if (empty($nedshfeditmiddlecolumnvalue)) {
            $nedshfeditmiddlecolumnvalue = '&nbsp;';
        }
        $sectionheaderformatnamelabelscontent .= '<span id="nedshfmiddlelabel" class="nedshfeditmiddlecolumn">'.$nedshfeditmiddlecolumnvalue.'</span>';
        $nedshfeditrightcolumnvalue = $sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['rightcolumn']['value'];
        if (empty($nedshfeditrightcolumnvalue)) {
            $nedshfeditrightcolumnvalue = '&nbsp;';
        }
        $sectionheaderformatnamelabelscontent .= '<span id="nedshfrightlabel" class="nedshfeditrightcolumn">'.$nedshfeditrightcolumnvalue.'</span>';
        $sectionheaderformatnamelabelscontent .= '</div>';
        $sectionheaderformatnamelabelsgroup[] =& $mform->createElement('static', 'shflcolumns', '', $sectionheaderformatnamelabelscontent);
        $mform->addGroup($sectionheaderformatnamelabelsgroup, 'sectionheaderformatnamelabelsgroup', get_string('sectionname'), array('<span class="nedshfsep"></span>'), false);

        $sectionheaderformatnamevaluesgroup = array();
        // Note: Changed from 'html' type to 'static' for the Clean theme.
        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('static', 'nedshfeditcolumns', '', '<div class="nedshfeditcolumns">');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('static', 'nedshfeditleftcolumn', '', '<div class="nedshfeditleftcolumn">');
        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('checkbox', 'shfcleftcolumn', null, '');
        if (!empty($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['leftcolumn']['active'])) {
            $mform->setDefault('shfcleftcolumn', 'checked');
        }
        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('text', 'shfvleftcolumn');
        $mform->setDefault('shfvleftcolumn', $sectionheaderformat['sectionname']['leftcolumn']);
        $mform->setType('shfvleftcolumn', PARAM_TEXT);
        $mform->disabledIf('shfvleftcolumn', 'shfcleftcolumn');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('static', 'nedshfeditmiddlecolumn', '', '</div><div class="nedshfeditmiddlecolumn">');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('checkbox', 'shfcmiddlecolumn', null, '');
        if (!empty($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['middlecolumn']['active'])) {
            $mform->setDefault('shfcmiddlecolumn', 'checked');
        }
        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('text', 'shfvmiddlecolumn');
        $mform->setDefault('shfvmiddlecolumn', $sectionheaderformat['sectionname']['middlecolumn']);
        $mform->setType('shfvmiddlecolumn', PARAM_TEXT);
        $mform->disabledIf('shfvmiddlecolumn', 'shfcmiddlecolumn');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('static', 'nedshfeditrightcolumn', '', '</div><div class="nedshfeditrightcolumn">');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('checkbox', 'shfcrightcolumn', null, '');
        if (!empty($sectionheaderformats[$shfrows[$sectionheaderformat['headerformat']]]['rightcolumn']['active'])) {
            $mform->setDefault('shfcrightcolumn', 'checked');
        }
        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('text', 'shfvrightcolumn');
        $mform->setDefault('shfvrightcolumn', $sectionheaderformat['sectionname']['rightcolumn']);
        $mform->setType('shfvrightcolumn', PARAM_TEXT);
        $mform->disabledIf('shfvrightcolumn', 'shfcrightcolumn');

        $sectionheaderformatnamevaluesgroup[] =& $mform->createElement('static', 'nedshfeditendrightcolumn', '', '</div></div>');
        $mform->addGroup($sectionheaderformatnamevaluesgroup, 'sectionheaderformatnamevaluesgroup', '', array('<span class="nedshfsep"></span>'), false);

        $mform->addElement('static', 'nedshfeditendgroup', '', '</div>');

        // Prepare course and the editor.
        $mform->addElement('editor', 'summary_editor', get_string('summary'), null, $this->_customdata['editoroptions']);
        $mform->addHelpButton('summary_editor', 'summary');
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Additional fields that course format has defined.
        $courseformat = course_get_format($course);
        $formatoptions = $courseformat->section_format_options(true);
        if (!empty($formatoptions)) {
            $elements = $courseformat->create_edit_form_elements($mform, true);
        }

        $mform->_registerCancelButton('cancel');
    }
}
