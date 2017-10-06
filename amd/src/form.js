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
    log.debug('NED Format Form AMD');
    (function( $ ) {
        "use strict";

        $.fn.sectionFormat = function() {
            var target = $('#nedsectionlocation');

            var checkSelect = function(us) {
                var chosen = us.find(':selected').val();
                log.debug('NED Format Form AMD checkSelect chosen: ' + chosen);
                if (chosen == 2) {
                    target.show();
                } else {
                    target.hide();
                }
            };
            
            //var choose = this.find(':selected').val();
            checkSelect(this);

            this.on('change', function (e) {
                //var $select = $(e.target);
                //var choose = $select.find(':selected').val();
                //var choose = $(this).find(':selected').val();
                //log.debug('NED Format Form AMD sectionFormat change: ' + choose);
                //$(this).checkSelect();
                //this.checkSelect();
                checkSelect($(this));
           });
        }
    }($));
    return {
        init: function(data) {
            $(document).ready(function($) {
                $('select#id_sectionformat').sectionFormat();
            });
            log.debug('NED Format Form AMD init: ' + data.debug);
        }
    }
});
/* jshint ignore:end */
