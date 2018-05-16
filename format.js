// Javascript functions for NED course format.

M.course = M.course || {};

M.course.format = M.course.format || {};

/**
 * Get sections config for this format
 *
 * The section structure is:
 * <ul class="ned">
 *  <li class="section">...</li>
 *  <li class="section">...</li>
 *   ...
 * </ul>
 *
 * @return {object} section list configuration
 */
M.course.format.get_config = function() {
    return {
        container_node : 'ul',
        container_class : 'ned',
        section_node : 'li',
        section_class : 'section'
    };
};

/**
 * Swap section
 *
 * @param {YUI} Y YUI3 instance
 * @param {string} node1 node to swap to
 * @param {string} node2 node to swap with
 * @return {NodeList} section list
 */
M.course.format.swap_sections = function(Y, node1, node2) {
    var CSS = {
        COURSECONTENT : 'course-content',
        SECTIONADDMENUS : 'section_add_menus'
    };

    var sectionlist = Y.Node.all('.' + CSS.COURSECONTENT + ' ' + M.course.format.get_section_selector(Y));
    // Swap menus.
    sectionlist.item(node1).one('.' + CSS.SECTIONADDMENUS).swap(sectionlist.item(node2).one('.' + CSS.SECTIONADDMENUS));
};

/**
 * Process sections after ajax response
 *
 * @param {YUI} Y YUI3 instance
 * @param {array} response ajax response
 * @param {string} sectionfrom first affected section
 * @param {string} sectionto last affected section
 * @return void
 */
M.course.format.process_sections = function(Y, sectionlist, response, sectionfrom, sectionto) {
    var CSS = {
        SECTIONNAME : 'sectionname'
    },
    SELECTORS = {
        SECTIONLEFTSIDE : '.left .section-handle .icon'
    };

    var replacecssn = function(i, stringvalue) {
        // From M.util.get_string and for 'compressedsectionsectionname' language string.
        var search = '{$a->sectionno}';
        search = search.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
        search = new RegExp(search, 'g');
        stringvalue = stringvalue.replace(search, i);
        return stringvalue;
    };

    if (response.action == 'move') {
        // If moving up swap around 'sectionfrom' and 'sectionto' so the that loop operates.
        if (sectionfrom > sectionto) {
            var temp = sectionto;
            sectionto = sectionfrom;
            sectionfrom = temp;
        }

        // Update titles and move icons in all affected sections.
        var ele, str, stridx, newstr;

        // As M.str does not contain the string we need, thus get from the markup - populated by renderer.php.
        var datasectiontitle = Y.all('#nedcssn');
        if (datasectiontitle != null) {
            datasectiontitle = datasectiontitle.getData('cssn');
        }

        for (var i = sectionfrom; i <= sectionto; i++) {
            var sectionnameitem = sectionlist.item(i).all('.' + CSS.SECTIONNAME);

            // Update section title.
            var content = null;
            if (sectionnameitem.hasClass('compressedsectionsectionname')) {
                var sectionprefix = '';
                if (datasectiontitle != null) {
                    sectionprefix = replacecssn(i, '' + datasectiontitle);
                }
                content = Y.Node.create('<span>' + sectionprefix + response.sectiontitles[i] + '</span>');
            } else {
                content = Y.Node.create('<span>' + response.sectiontitles[i] + '</span>');
            }
            sectionnameitem.setHTML(content);
            // Update move icon.
            ele = sectionlist.item(i).one(SELECTORS.SECTIONLEFTSIDE);
            str = ele.getAttribute('alt');
            stridx = str.lastIndexOf(' ');
            newstr = str.substr(0, stridx + 1) + i;
            ele.setAttribute('alt', newstr);
            ele.setAttribute('title', newstr); // For FireFox as 'alt' is not refreshed.
        }
    }
};
