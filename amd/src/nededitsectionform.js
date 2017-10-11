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
    log.debug('NED Format Edit Section Form AMD');
    (function( $ ) {
        "use strict";

        $.fn.sectionHeaderFormat = function(data) {
            var sectionheaderformatsdata = data.sectionheaderformatsdata;
            var leftLabel = $('#nedshfleftlabel');
            var middleLabel = $('#nedshfmiddlelabel');
            var rightLabel = $('#nedshfrightlabel');
            var leftActive = $('#id_shfcleftcolumn');
            var middleActive = $('#id_shfcmiddlecolumn');
            var rightActive = $('#id_shfcrightcolumn');
            var leftValue = $('#id_shfvleftcolumn');
            var middleValue = $('#id_shfvmiddlecolumn');
            var rightValue = $('#id_shfvrightcolumn');

            var checkSelect = function(us) {
                var chosen = us.find(':selected').val();
                log.debug('NED Format Edit Section Form AMD checkSelect chosen: ' + chosen);

                leftLabel.text(sectionheaderformatsdata[chosen]['leftcolumn']['value']);
                middleLabel.text(sectionheaderformatsdata[chosen]['middlecolumn']['value']);
                rightLabel.text(sectionheaderformatsdata[chosen]['rightcolumn']['value']);

                /* For some reason the disabledIf JS does not react to this, however is still
                   needed as the state is changed and the PHP 'update_section_format_options()' in lib.php
                   does see the change and make decisions upon it. */
                if (sectionheaderformatsdata[chosen]['leftcolumn']['active'] == 1) {
                    leftActive.prop("checked", true);
                    leftValue.prop("disabled", false);
                } else {
                    leftActive.prop("checked", false);
                    leftValue.prop("disabled", true);
                }
                if (sectionheaderformatsdata[chosen]['middlecolumn']['active'] == 1) {
                    middleActive.prop("checked", true);
                    middleValue.prop("disabled", false);
                } else {
                    middleActive.prop("checked", false);
                    middleValue.prop("disabled", true);
                }
                if (sectionheaderformatsdata[chosen]['rightcolumn']['active'] == 1) {
                    rightActive.prop("checked", true);
                    rightValue.prop("disabled", false);
                } else {
                    rightActive.prop("checked", false);
                    rightValue.prop("disabled", true);
                }
            };

            this.on('change', function (e) {
                checkSelect($(this));
            });
        }
    }($));
    return {
        init: function(data) {
            $(document).ready(function($) {
                $('select#id_sectionheaderformat').sectionHeaderFormat(data);
                /*if ($('#id_shfcleftcolumn').prop("checked")) {
                    $('#id_shfcleftcolumn').prop("checked", false);
                }*/
                if (window.JSON && window.JSON.stringify) {
                   log.debug('NED Format Edit Section Form AMD data: ' + JSON.stringify(data));
                   //log.debug('NED Format Edit Section Form AMD data: ' + JSON.stringify(data.sectionheaderformatsdata[1]));
                }
            });
            log.debug('NED Format Edit Section Form AMD init.');
        }
    }
});
/* jshint ignore:end */
