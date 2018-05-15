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
        init: function(data) {
            $(document).ready(function($) {
                var nededitingsectioncompressed = function() {
                    $('ul.nededitingsection li.section').each(function () {
                        var compressedmodeviewhideformat = $(this).find('.compressedmodeviewhide .compressedmodeviewhideformat');
                        var compressedmodeviewhidesectionname = $(this).find('.compressedmodeviewhide .sectioname');
                        var compressedmodeviewhidesummary = $(this).find('.compressedmodeviewhide .summary');
                        $(this).find('.content .section').hide();
                        $(this).find('.content .addresourcemodchooser').hide();
                        if ($(this).hasClass('open')) {
                            $(this).removeClass('open').addClass('closed');
                            if (compressedmodeviewhideformat.length) {
                                compressedmodeviewhideformat.show();
                            }
                            if (compressedmodeviewhidesummary.length) {
                                compressedmodeviewhidesummary.hide();
                            }
                            if (compressedmodeviewhidesectionname.length) {
                                compressedmodeviewhidesectionname.show();
                            }
                        }
                    });
                };

                var nededitingsectionexpanded = function() {
                    $('ul.nededitingsection li.section').each(function () {
                        var compressedmodeviewhideformat = $(this).find('.compressedmodeviewhide .compressedmodeviewhideformat');
                        var compressedmodeviewhidesectionname = $(this).find('.compressedmodeviewhide .sectioname');
                        var compressedmodeviewhidesummary = $(this).find('.compressedmodeviewhide .summary');
                        $(this).find('.content .section').show();
                        $(this).find('.content .addresourcemodchooser').show();
                        if ($(this).hasClass('closed')) {
                            $(this).removeClass('closed').addClass('open');
                            if (compressedmodeviewhideformat.length) {
                                compressedmodeviewhideformat.hide();
                            }
                            if (compressedmodeviewhidesummary.length) {
                                compressedmodeviewhidesummary.show();
                            }
                            if (compressedmodeviewhidesectionname.length) {
                                compressedmodeviewhidesectionname.hide();
                            }
                        }
                    });
                };

                var nededitingsectionexpand = function(section) {
                    var compressedmodeviewhideformat = section.find('.compressedmodeviewhide .compressedmodeviewhideformat');
                    var compressedmodeviewhidesectionname = section.find('.compressedmodeviewhide .sectioname');
                    var compressedmodeviewhidesummary = section.find('.compressedmodeviewhide .summary');
                    section.find('.content .section').toggle();
                    section.find('.content .addresourcemodchooser').toggle();
                    if (section.hasClass('closed')) {
                        section.removeClass('closed').addClass('open');
                        if (compressedmodeviewhideformat.length) {
                            compressedmodeviewhideformat.hide();
                        }
                        if (compressedmodeviewhidesummary.length) {
                            compressedmodeviewhidesummary.show();
                        }
                        if (compressedmodeviewhidesectionname.length) {
                            compressedmodeviewhidesectionname.hide();
                        }
                    } else {
                        section.removeClass('open').addClass('closed');
                        if (compressedmodeviewhideformat.length) {
                            compressedmodeviewhideformat.show();
                        }
                        if (compressedmodeviewhidesummary.length) {
                            compressedmodeviewhidesummary.hide();
                        }
                        if (compressedmodeviewhidesectionname.length) {
                            compressedmodeviewhidesectionname.show();
                        }
                    }
                };

                // Initial page load.
                if (data.nedsectionstate == data.allexpanded) {
                    nededitingsectionexpanded();
                } else {
                    nededitingsectioncompressed();
                    if (data.nedsectionstate > 0) {
                        var section = $('.section#section-' + data.nedsectionstate);
                        if (section.length) {
                            nededitingsectionexpand(section);
                        }
                    }
                }

                // Individual toggles on the page after load.
                $('ul.nededitingsection li.section .left .nededitingsectionpix').click(function (e) {
                    var section = $(this).parent('.left').parent('.section');
                    nededitingsectionexpand(section);
                });
                // All toggles compress.
                $('#nededitingsectioncompressed').click(nededitingsectioncompressed);
                // All toggles expand.
                $('#nededitingsectionexpanded').click(nededitingsectionexpanded);
            });
            log.debug('NED Format Editing Section AMD init.');
        }
    }
});
/* jshint ignore:end */
