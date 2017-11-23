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
            var url = M.cfg.wwwroot + '/course/format/ned/getsection.php'; // jshint ignore:line
            var us = this;

            log.debug('NED Format Editing Section AMD request url: ' + url);
            var request = $.ajax({
                url: url,
                method: "POST",
                cache: false,
                data: { sesskey: M.cfg.sesskey, courseid: courseid, sectionno: sectionno},
                dataType: "html"
            });
 
            request.done(function(html) {
                log.debug('NED Format Editing Section AMD request done: ' + html);
                $(us).replaceWith(html);
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

                //$().getSection(data.courseid, 1);

                // Individual toggles.
                $('ul.nededitingsection li.section .left .nededitingsectionpix').click(function (e) {
                    if ($(this).hasClass('closed')) {
                        $(this).removeClass('closed').addClass('open');
                    } else {
                        $(this).removeClass('open').addClass('closed');
                    }
                    $(this).parent('.left').parent('.section').find('.content .section').each(function() {
                        //$(this).html('<div>Me!</div>');
                        var sectionno = $(this).attr('nedsectionno');
                        if (sectionno != undefined) {
                            $(this).getSection(data.courseid, sectionno);
                            //Y.use("moodle-course-dragdrop", function() {M.course.init_resource_dragdrop({"courseid":"5","ajaxurl":"/course/rest.php","config":{"resourceurl":"/course/rest.php","sectionurl":"/course/rest.php","pageparams":[]}});});
                            //M.course.init_resource_dragdrop({"courseid":"5","ajaxurl":"/course/rest.php","config":{"resourceurl":"/course/rest.php","sectionurl":"/course/rest.php","pageparams":[]}});
                            //M.course.init_resource_dragdrop.setup_for_section('.course-content li.section');
                            //Y.use("moodle-course-dragdrop", function() { M.course.init_resource_dragdrop.setup_for_section('.course-content li.section');});
                            //log.debug('NED Format Editing Section AMD DD: ' + JSON.stringify(M.course.init_resource_dragdrop));
                            //log.debug('NED Format Editing Section AMD Y: ' + JSON.stringify(Y));
                            log.debug('NED Format Editing Section AMD M.cfg: ' + JSON.stringify(M.cfg));
                            log.debug('NED Format Editing Section AMD M.course.init_resource_dragdrop(NY): ' + JSON.stringify(M.course.init_resource_dragdrop));
                            Y.use("moodle-course-dragdrop", function() {
                                log.debug('NED Format Editing Section AMD M.course.init_resource_dragdrop(UY): ' + JSON.stringify(M.course.init_resource_dragdrop));
                                //log.debug('NED Format Editing Section AMD M.course(UY): ' + JSON.stringify(M.course));
                            });
                            log.debug('NED Format Editing Section AMD M.format_ned.init: ' + JSON.stringify(M.format_ned.init));
                            M.format_ned.dragdrop();
                        } else {
                            $(this).toggle();
                        }
                    });
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
