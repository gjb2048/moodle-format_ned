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

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    log.debug('NED Format Settings Form AMD');
    (function( $ ) {
        "use strict";

        $.fn.sectionFormat = function() {
            var locationTarget = $('#nedsectionlocation');
            var locationColourPresets = $('#managecolourpresets');
            var locationHeaderFormats = $('#nedsectionheaderformats');

            var checkSelect = function(us) {
                var chosen = us.find(':selected').val();
                log.debug('NED Format Settings Form AMD checkSelect chosen: ' + chosen);
                if (chosen == 0) {
                    locationTarget.hide();
                    locationColourPresets.hide();
                    locationHeaderFormats.hide();
                } else if (chosen == 2) {
                    locationTarget.show();
                    locationColourPresets.show();
                    locationHeaderFormats.hide();
                } else if (chosen == 3) {
                    locationColourPresets.show();
                    locationHeaderFormats.show();
                    locationTarget.hide();
                } else { // Effectively 1.
                    locationColourPresets.show();
                    locationTarget.hide();
                    locationHeaderFormats.hide();
                }
            };

            checkSelect(this);

            this.on('change', function (e) {
                checkSelect($(this));
            });
        }
    }($));
    return {
        init: function() {
            $(document).ready(function($) {
                $('select#id_sectionformat').sectionFormat();
            });
            log.debug('NED Format Settings Form AMD init.');
        }
    }
});
/* jshint ignore:end */
