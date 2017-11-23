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

    (function($) {
        "use strict";

        $.fn.getSection = function(courseid, sectionno) {
            var url = M.cfg.wwwroot + '/course/format/ned/getsection.php?sesskey=' + M.cfg.sesskey + '&courseid=' + encodeURI(courseid) + '&sectionno=' + encodeURI(sectionno); // jshint ignore:line
            log.debug('NED Format Editing Section AMD request url: ' + url);
            var request = $.ajax({
                url: url,
                method: "GET",
                cache: false,
                dataType: "html"
            });
 
            request.done(function(msg) {
                log.debug('NED Format Editing Section AMD request done: ' + msg);
                $('.nedlog').html(msg);
            });
 
            request.fail(function(jqXHR, textStatus) {
                log.debug('NED Format Editing Section AMD request failed: ' + textStatus);
            });

        }
    }($));

    return {
        init: function(data) {
            $(document).ready(function($) {
                if (window.JSON && window.JSON.stringify) {
                    log.debug('NED Format Editing Section AMD data: ' + JSON.stringify(data));
                }

                $().getSection(data.courseid, 4);

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
