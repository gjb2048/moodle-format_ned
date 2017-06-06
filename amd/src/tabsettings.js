/**
 * NED Format
 *
 * @package    course/format
 * @subpackage ned
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  2017 Gareth J Barnard
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;
    log.debug('NED AMD tabsettings');
    return {
        init: function() {
            $(document).ready(function($) {
                $('#id_managecolourschemas').click(function() {
                    var colourschema = $('#id_colourschema option:selected').val();
                    var courseid = $("[name='id']").val();
                    location.href = M.cfg.wwwroot + '/course/format/ned/colourschema_edit.php?courseid=' + courseid + '&edit=' + colourschema;
                });
            });
            log.debug('NED AMD tabsettings init');
        }
    }
});
/* jshint ignore:end */
