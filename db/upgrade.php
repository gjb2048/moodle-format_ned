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
 * Upgrade script for format_ned
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_ned_upgrade($oldversion) {
    global $CFG;

    require_once($CFG->dirroot . '/course/format/ned/db/upgradelib.php');

    if ($oldversion < 2017061900) {

        // Remove 'numsections' option and hide or delete orphaned sections.
        format_ned_upgrade_remove_numsections();

        upgrade_plugin_savepoint(true, 2017061900, 'format', 'ned');
    }

    if ($oldversion < 2017061903) {
        global $DB;
        $dbman = $DB->get_manager();

        // Define table format_ned_colour to be created.
        $table = new xmldb_table('format_ned_colour');

        // Adding fields to table format_ned_colour.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '');
        $table->add_field('framedsectionbgcolour', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('framedsectionheadertxtcolour', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('predefined', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table format_ned_colour.
        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for format_ned_colour.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);

            $recone = new stdClass();
            $rectwo = new stdClass();
            $recone->name = 'Embassy Green';
            $rectwo->name = 'Blues on Whyte';
            $recone->framedsectionbgcolour = '9DBB61';
            $rectwo->framedsectionbgcolour = 'FFFFFF';
            $recone->framedsectionheadertxtcolour = 'FFFF33';
            $rectwo->framedsectionheadertxtcolour = '7CAAFE';
            $recone->predefined = 1;
            $rectwo->predefined = 1;
            $recone->timecreated = time();
            $rectwo->timecreated = time();
            $recone->timemodified = time();
            $rectwo->timemodified = time();

            $DB->insert_record('format_ned_colour', $recone);
            $DB->insert_record('format_ned_colour', $rectwo);
        }

        // NED savepoint reached.
        upgrade_plugin_savepoint(true, 2017061903, 'format', 'ned');
    }

    return true;
}
