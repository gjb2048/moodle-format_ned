/**
 * NED Format
 *
 * @package    format_ned
 * @subpackage NED
 * @copyright  NED {@link http://ned.ca}
 * @author     NED {@link http://ned.ca}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @developer  G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 */

/**
 * @namespace
 */
M.format_ned = M.format_ned || {};

M.format_ned.courseid = 0;
M.format_ned.ourYUI = false;
M.format_ned.userIsEditing = false;

/**
 * Initialise with the information supplied from the course format.
 * @param {Object} Y YUI instance
 * @param {String} theCourseId the id of the current course to allow for settings for each course.

 * @param {Boolean} theUserIsEditing User is editing (true) or or not (false).
 */
M.format_ned.init = function(Y, theCourseId, theUserIsEditing) {
    "use strict";
    // Init.
    this.ourYUI = Y;
    this.courseid = theCourseId;
    this.userIsEditing = theUserIsEditing;
    console.log('M.format_ned.init - Yey!');
};

M.format_ned.dragdrop = function() {
    console.log('M.format_ned.dragdrop - Yey!');
    //M.format_ned.ourYUI.use("moodle-course-dragdrop", function() { M.course.init_resource_dragdrop.setup_for_section('.course-content li.section');});
    //M.format_ned.ourYUI.use("moodle-course-dragdrop", function() {M.course.init_resource_dragdrop({"courseid":"5","ajaxurl":"/course/rest.php","config":{"resourceurl":"/course/rest.php","sectionurl":"/course/rest.php","pageparams":[]}});});
    M.format_ned.ourYUI.use("moodle-course-dragdrop", function() {
        console.log('M.course.init_resource_dragdrop: ' + M.course.init_resource_dragdrop);
        //console.log('M.course.dragdrop.resource: ' + M.course.dragdrop.resource);
        console.log('M.format_ned.courseid: ' + M.format_ned.courseid);
        M.course.init_resource_dragdrop({"courseid": M.format_ned.courseid,"ajaxurl":"/course/rest.php","config":{"resourceurl":"/course/rest.php","sectionurl":"/course/rest.php","pageparams":[]}});

        M.course = M.course || {};
        M.course.ned_init_resource_dragdrop = function(params) {
           new DRAGRESOURCE(params);
        };
        M.course.ned_init_resource_dragdrop({"courseid": M.format_ned.courseid,"ajaxurl":"/course/rest.php","config":{"resourceurl":"/course/rest.php","sectionurl":"/course/rest.php","pageparams":[]}});
    });
    /*M.format_ned.ourYUI.use("moodle-course-dragdrop-resource", function() {
        console.log('M.course.dragdrop.resource: ' + M.course.dragdrop.resource);
    });*/
};