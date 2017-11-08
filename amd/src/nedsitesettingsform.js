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
    log.debug('NED Format Site Settings Form AMD');
    (function($) {
        "use strict";

        $.fn.defaultSectionFormat = function(data) {
            var defaultsectionformatoptionsdata = data.defaultsectionformatoptionsdata;
            var defaultSectionFormat = $('#id_s_format_ned_defaultsectionformat');

            var checkSelect = function(us) {
                var chosen = us.find(':selected').val();
                log.debug('NED Format Site Settings Form AMD checkSelect chosen: ' + chosen);

                // Change the section format name values.
                defaultSectionFormat.empty();
                $.each(defaultsectionformatoptionsdata, function(key, value) {
                    if (!((chosen == 0) && (key == 2))) { // Hide formatted sections + custom header.
                        defaultSectionFormat.append($("<option></option>")
                            .attr("value", key)
                            .text(value));
                    }
                });
            };

            // Initial check to remove the custom header option only if we are 'hide'.
            if (this.find(':selected').val() == 0) {
                checkSelect(this);
            }

            this.on('change', function (e) {
                checkSelect($(this));
            });
        }
    }($));

    return {
        init: function(data) {
            $(document).ready(function($) {
                $('select#id_s_format_ned_framedsectionscustomheader').defaultSectionFormat(data);

                if (window.JSON && window.JSON.stringify) {
                    log.debug('NED Format Site Settings Form AMD data: ' + JSON.stringify(data));
                }
            });
            log.debug('NED Format Site Settings Form AMD init.');
        }
    }
});
/* jshint ignore:end */
