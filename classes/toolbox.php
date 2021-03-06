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

namespace format_ned;

defined('MOODLE_INTERNAL') || die;

class toolbox {
    // Constants.

    // Completion icon position.
    public static $moodleicons = 'moodleicons';
    public static $nediconsleft = 'nediconsleft';
    public static $nediconsright = 'nediconsright';

    // Completion state.
    public static $autoenabled = 'auto-enabled';
    public static $autofail = 'auto-fail';
    public static $autopass = 'auto-pass';
    public static $auton = 'auto-n';
    public static $autoy = 'auto-y';
    public static $manualenabled = 'manual-enabled';
    public static $manualn = 'manual-n';
    public static $manualy = 'manual-y';
    public static $notset = 'notset';

    // Completion icon assignment state.
    public static $saved = 'saved';
    public static $submitted = 'submitted';
    public static $waitinggrade = 'waitinggrade';

    // Activity / resource mouseover effect.
    public static $activityresourcemouseover = 'nedactresmouseover';

    // Navigation.
    public static $mainpageparam = 'nedmainpage';

    // Compressed / expanded sections.
    public static $allcompressed = -2;
    public static $allexpanded = -1;
    public static $compressedsectionsparam = 'nedsectionstate';
}
