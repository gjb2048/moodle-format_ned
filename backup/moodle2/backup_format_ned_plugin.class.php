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
 * @developer  2017 Michael Gardener
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Specialised restore for format_ned
 *
 * Processes custom options
 *
 * @package   format_ned
 * @category  backup
 * @developer 2017 Michael Gardener
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_format_ned_plugin extends backup_format_plugin {
    /**
     * Returns the format information to attach to section element
     */
    protected function define_section_plugin_structure() {
        $plugin = $this->get_plugin_element(null, $this->get_format_condition(), 'ned');
        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name(), array('id'),
            array('name', 'value')
        );
        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);
        // Set source to populate the data.
        $pluginwrapper->set_source_table('format_ned', array('sectionid' => backup::VAR_SECTIONID));
        // Don't need to annotate ids nor files.
        return $plugin;
    }
}
