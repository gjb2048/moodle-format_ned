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

class ned_admin_setting_headerformats extends admin_setting_configtext {

    /** @var string Error message to show if validation fails */
    public $error;

    /** @var string Default stored here as do not want the core code to show it on the page. */
    public $defaultsetting;

    /**
     * NED admin setting header formats constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     * config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param int $error Error message to show if validation fails.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $error) {
        $this->error = $error;
        $this->defaultsetting = $defaultsetting;
        parent::__construct($name, $visiblename, $description, '');
    }

    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        error_log('write_setting: '.print_r($data, true));

        // return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
        return '';
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        //$validated = parent::validate($data); // Pass parent validation first.
        $validated = true;

        return $validated;
    }

    /**
     * Return an XHTML string for the setting
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
//error_log('output_html: '.print_r($data, true));
$data['a'] = 'One';
$data['b'] = 'Two';

        $default = $this->get_defaultsetting();
        $headers = array();
        $headers[] = get_string('shflname', 'format_ned');
        $headers[] = get_string('shfllc', 'format_ned');
        $headers[] = get_string('shflmc', 'format_ned');
        $headers[] = get_string('shflrc', 'format_ned');
        $headers[] = get_string('colourpreset', 'format_ned');

        //$shfsdata = json_decode($this->defaultsetting, true);
        $shfsdata = json_decode(format_ned::get_section_header_format_default(), true);

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

        /*
        $sectionheaderformatonecolourpresetvalues = '[';
        foreach ($colourpresetitems as $colourpresetitemkey => $colourpresetitem) {
            $sectionheaderformatonecolourpresetvalues .= '{"key": "'.$colourpresetitemkey.'", "item": "'.$colourpresetitem.'"';
            if ($shfsdata['sectionheaderformatone']['colourpreset'] == $colourpresetitemkey) {
                $sectionheaderformatonecolourpresetvalues .= '"selected": true';
            }
            $sectionheaderformatonecolourpresetvalues .= '},';
        }
        $sectionheaderformatonecolourpresetvalues .= ']';
        //error_log($sectionheaderformatonecolourpresetvalues);
        //$sectionheaderformatonecolourpresetvalues .= '[{"value": "Moodle default", "text": "0"},{"value": "Embassy Green", "text": "1"},{"value": "Blues on Whyte", "text": "2"},{"value": "Odd", "text": "4"},{"value": "WB", "text": "5"},{"value": "Ooo", "text": "6"}]';
        */
        $sectionheaderformatonecolourpresetvalues = '<option value="-1"';
        if ($shfsdata['sectionheaderformatone']['colourpreset'] == -1) {
            $sectionheaderformatonecolourpresetvalues .= ' selected="selected"';
        }
        $sectionheaderformatonecolourpresetvalues .= '>'.get_string('shfldefault', 'format_ned').'</option>';
        foreach ($colourpresetitems as $colourpresetitemkey => $colourpresetitem) {
            $sectionheaderformatonecolourpresetvalues .= '<option value="'.$colourpresetitemkey.'"';
            if ($shfsdata['sectionheaderformatone']['colourpreset'] == $colourpresetitemkey) {
                $sectionheaderformatonecolourpresetvalues .= ' selected="selected"';
            }
            $sectionheaderformatonecolourpresetvalues .= '>'.$colourpresetitem.'</option>';
        }

        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'headers' => $headers,
            'sectionheaderformatoneactive' => $shfsdata['sectionheaderformatone']['active'],
            'sectionheaderformatonename' => $shfsdata['sectionheaderformatone']['name'],
            'sectionheaderformatoneleftcolumnactive' => $shfsdata['sectionheaderformatone']['leftcolumn']['active'],
            'sectionheaderformatoneleftcolumnvalue' => $shfsdata['sectionheaderformatone']['leftcolumn']['value'],
            'sectionheaderformatonemiddlecolumnactive' => $shfsdata['sectionheaderformatone']['middlecolumn']['active'],
            'sectionheaderformatonemiddlecolumnvalue' => $shfsdata['sectionheaderformatone']['middlecolumn']['value'],
            'sectionheaderformatonerightcolumnactive' => $shfsdata['sectionheaderformatone']['rightcolumn']['active'],
            'sectionheaderformatonerightcolumnvalue' => $shfsdata['sectionheaderformatone']['rightcolumn']['value'],
            'sectionheaderformatonecolourpresetvalues' => $sectionheaderformatonecolourpresetvalues,
            'value_a' => $data['a'],
            'value_b' => $data['b'],
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('format_ned/admin_setting_headerformats', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}
