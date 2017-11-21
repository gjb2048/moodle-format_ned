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
    log.debug('NED Format Editing Section AMD');

    return {
        init: function() {
            $(document).ready(function($) {
                // Individual toggles.
                $('ul.nededitingsection li.section').off('click').click(function (e) {
                    // Somehow getting the 'click' twice!  So had to remove all click handlers on the selectors first.
                    var target = $(e.target);
                    if (target.is('ul.section')) return;
                    if (target.is('div.visibleifjs')) return;
                    $(this).find('.content .section').toggle();
                });
                // All toggles compress.
                $('#nededitingsectioncompressed').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').hide();
                    });
                });
                // All toggles expand.
                $('#nededitingsectionexpanded').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').show();
                    });
                });
            });
            log.debug('NED Format Editing Section AMD init.');
        }
    }
});
/* jshint ignore:end */
