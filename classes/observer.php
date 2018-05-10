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

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers supported by this format.
 */
class format_ned_observer {

    /**
     * Observer for the event course_content_deleted.
     *
     * Deletes the user preference entries for the given course upon course deletion.
     *
     * @param \core\event\course_content_deleted $event
     */
    public static function course_content_deleted(\core\event\course_content_deleted $event) {
        // Here for future development if needed to delete things specific to a given course.
        // Note: Now implemened colour and removed the need for course specific entries.
    }

    /**
     * Observer for the event course_deleted.
     *
     * Deletes format options that are kept in format_ned table for the given course upon course deletion.
     *
     * @param \core\event\course_deleted $event
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;
        $data = $event->get_data();
        $DB->delete_records('format_ned', array('courseid' => $data['objectid']));
    }

    /**
     * Observer for the event course_section_deleted.
     *
     * Deletes format options that are kept in format_ned table for the given course upon section deletion.
     *
     * @param \core\event\course_section_deleted
     */
    public static function course_section_deleted(\core\event\course_section_deleted $event) {
        global $DB;
        $data = $event->get_data();
        $DB->delete_records('format_ned', array('sectionid' => $data['objectid']));

        // Purge cache to rebuild after section deleted.
        $headercache = cache::make('format_ned', 'headerformat');
        $headercache->purge();
    }

    /**
     * Observer for the event course_section_created.
     *
     * @param \core\event\course_section_created
     */
    public static function course_section_created(\core\event\course_section_created $event) {
        // Purge cache to rebuild after section created.
        $headercache = cache::make('format_ned', 'headerformat');
        $headercache->purge();
    }
}
