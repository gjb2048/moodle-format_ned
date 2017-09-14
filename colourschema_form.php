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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class colourschema_form extends moodleform {
    public function definition() {

        global $CFG, $OUTPUT;

        $mform = $this->_form;
        $mform->addElement('header', '', get_string('colourschema', 'format_ned'), '');

        // TODO: Make responsive and not a table.
        $mform->addElement('html', '<table style="width:100%"><tr><td>');

        MoodleQuickForm::registerElementType('fnedcolourpopup',
            "$CFG->dirroot/course/format/ned/js/fned_colourpopup.php", 'MoodleQuickForm_fnedcolourpopup');

        $mform->addElement('text', 'name', get_string('name', 'format_ned'));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('fnedcolourpopup', 'framedsectionbgcolour',
            get_string('framedsectionbgcolour', 'format_ned'), 'maxlength="6" size="6"');
        $mform->setType('framedsectionbgcolour', PARAM_ALPHANUM);
        $mform->addRule('framedsectionbgcolour', null, 'required', null, 'client');

        $mform->addElement('fnedcolourpopup', 'framedsectionheadertxtcolour',
            get_string('framedsectionheadertxtcolour', 'format_ned'), 'maxlength="6" size="6"');
        $mform->setType('framedsectionheadertxtcolour', PARAM_ALPHANUM);
        $mform->addRule('framedsectionheadertxtcolour', null, 'required', null, 'client');

        $mform->addElement('html', '</td><td width="320px">');

        $mform->addElement('html', '<img src="'.$OUTPUT->image_url('ned_tabs_colourkey', 'format_ned').'" />');

        $mform->addElement('html', '</td></tr></table>');

        $mform->addElement('hidden', 'add');
        $mform->setType('add', PARAM_INT);

        $mform->addElement('hidden', 'edit');
        $mform->setType('edit', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, get_string('submit'));
    }

    public function validation($data, $files) {
        $errors = array();
        return $errors;
    }
}