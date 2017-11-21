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
                $('ul.nededitingsection li.section .left .nededitingsectionpix').click(function (e) {
                    $(this).parent('.left').parent('.section').find('.content .section').toggle();
                    if ($(this).hasClass('closed')) {
                        $(this).removeClass('closed').addClass('open');
                    } else {
                        $(this).removeClass('open').addClass('closed');
                    }
                });
                // All toggles compress.
                $('#nededitingsectioncompressed').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').hide();
                        var nededitingsectionpix = $(this).find('.left .nededitingsectionpix');
                        if ($(nededitingsectionpix).hasClass('open')) {
                            $(nededitingsectionpix).removeClass('open').addClass('closed');
                        }
                    });
                });
                // All toggles expand.
                $('#nededitingsectionexpanded').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').show();
                        var nededitingsectionpix = $(this).find('.left .nededitingsectionpix');
                        if ($(nededitingsectionpix).hasClass('closed')) {
                            $(nededitingsectionpix).removeClass('closed').addClass('open');
                        }
                    });
                });
            });
            log.debug('NED Format Editing Section AMD init.');
        }
    }
});
/* jshint ignore:end */
