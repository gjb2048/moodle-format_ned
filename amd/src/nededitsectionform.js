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
            var navigationDefaultString = data.defaultstring;
            var navigationNameSelect = $('#id_navigationname');
            var navigationNameBlockValue = $('#sectionnamenavblockvalue');

            var checkSelect = function(us) {
                var chosen = us.find(':selected').val();
                log.debug('NED Format Edit Section Form AMD checkSelect chosen: ' + chosen);

                // Change the section name labels.
                leftLabel.html(sectionheaderformatsdata[chosen]['leftcolumn']['value']);
                middleLabel.html(sectionheaderformatsdata[chosen]['middlecolumn']['value']);
                rightLabel.html(sectionheaderformatsdata[chosen]['rightcolumn']['value']);

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

                // Change the navigation name values.
                navigationNameSelect.empty();
                navigationNameSelect.append($("<option></option>")
                    .attr("value", 0)
                    .text(navigationDefaultString));
                $.each(sectionheaderformatsdata[chosen]['navigationname'], function(key, value) {
                    navigationNameSelect.append($("<option></option>")
                        .attr("value", key)
                        .text(value));
                });
                // Goes back to 'default' so update the text.
                navigationNameBlockValue.text(data.sectionnamenavblockvaluedata[0]);
            };

            navigationNameSelect.on('change', function (e) {
                // Change the navigation name block value.
                var chosen = $(this).find(':selected').val();
                navigationNameBlockValue.text(data.sectionnamenavblockvaluedata[chosen]);
            });

            this.on('change', function (e) {
                checkSelect($(this));
            });
        }
    }($));

    return {
        init: function(data) {
            $(document).ready(function($) {
                $('select#id_sectionheaderformat').sectionHeaderFormat(data);
                if (window.JSON && window.JSON.stringify) {
                    log.debug('NED Format Edit Section Form AMD data: ' + JSON.stringify(data));
                }
            });
            log.debug('NED Format Edit Section Form AMD init.');
        }
    }
});
/* jshint ignore:end */
