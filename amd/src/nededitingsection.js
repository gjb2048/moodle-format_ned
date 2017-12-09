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
                    var section = $(this).parent('.left').parent('.section');
                    section.find('.content .section').toggle();
                    section.find('.content .addresourcemodchooser').toggle();
                    if (section.hasClass('closed')) {
                        section.removeClass('closed').addClass('open');
                    } else {
                        section.removeClass('open').addClass('closed');
                    }
                });
                // All toggles compress.
                $('#nededitingsectioncompressed').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').hide();
                        $(this).find('.content .addresourcemodchooser').hide();
                        if ($(this).hasClass('open')) {
                            $(this).removeClass('open').addClass('closed');
                        }
                    });
                });
                // All toggles expand.
                $('#nededitingsectionexpanded').click(function () {
                    $('ul.nededitingsection li.section').each(function () {
                        $(this).find('.content .section').show();
                        $(this).find('.content .addresourcemodchooser').show();
                        if ($(this).hasClass('closed')) {
                            $(this).removeClass('closed').addClass('open');
                        }
                    });
                });
            });
            log.debug('NED Format Editing Section AMD init.');
        }
    }
});
/* jshint ignore:end */
