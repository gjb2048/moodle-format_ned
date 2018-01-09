Version Information
===================
Version 3.3.1.3.1
  1. Stage 7 continued.
  2. No white space above section title: https://www.screencast.com/t/bBysNCTVhA4u.
  3. Fix null data being written to the database in the 'format_ned' table when '/course/editsection.php' has no parameters.

Version 3.3.1.3
  1. Stage 7.
  2. Added 'format/ned:formatupdate' capability.

Version 3.3.1.2
  1. Stage 5.
  2. Added experimental 'Relocate activity description' setting.
  3. Fix indentation of activity description when 'Activity tracking background' is 'Show'.
  4. Fix position of completion icon when 'Relocate activity description' is in an 'Above' state and when 'Activity tracking background' is 'Show'.

Version 3.3.1.1
  1. Add border width to colour presets.

Version 3.3.1.0
  1. Beta version.
  2. Added FontAwesome filter (https://moodle.org/plugins/filter_fontawesome) support on Framed sections + Formatted header.

Version 3.3.0.6
  1. Change NED Edit icon to green and replace cog at the top of the course.
  2. Increase height of the preformatted framed header to 38px.
  3. Ensure framed section edit menu text is the same as the colour preset text.
  4. Move 'Edit section' outside of the 'Edit' menu in a section and transform into a cog with a white background.
  5. Fix indenting of 'Edit menu'.
  6. Fix editing activity / resource drag and drop not working, caused by:
        .editing ul.ned-framedsections .section .activity .mod-indent-outer {
            position: relative;
        }
     added in commit https://github.com/fernandooliveira/moodle-format_ned/commit/8795cfa700ac115d735509617a8ffe82b92bc82c#diff-4e7bb40584b55a44f4657b188986bff4R240.
  7. Change widths of columns from all 33.3% for Framed sections + Formatted header to 25% for left and right and 50% for middle
     and add ellipsis text overflow.

Version 3.3.0.5
  1. Draft 4 - parts 'a' to 'c' section name and location setting hiding functionality which is additionally a
     proof of concept for elements of part 'd' where the labels can change.

Version 3.3.0.4
  1. Implemented database end of colour presets.  Notes:
     'Manage colour presets' now goes to the list of presets and thus saving need for:
        a) Additional JavaScript.
        b) The user accidentially changing the colour scheme for the course.
        c) Making the list easier to find.
     Optimised colourpreset.php to use one database query instead of two for the count
     of records which additionally would have been wrong in the previous version for the
     paging bar because the count was based on all records and not the actual number when
     filtered.  Removed strange 'WHERE 0 = 0' SQL statement addition.
     Optimised course fetching in colour preset code to use core cached API instead of
     accessing the database directly.
     Restoring an old course will now check that a colour preset still exists, otherwise
     reset to default.

  TODO:
    Should the colourpreset.php and colourpreset_edit.php tables be responsive grids instead?  Currently seem
    to be fine with tables but could convert if required, however in tests this can be a little tricky with
    having to support both BS 2.3.2(Clean) and 4(Boost).

Version 3.3.0.3
  1. Implemented Framed sections.
  2. Fixed issue with navigation block / flat navigation links when using 'Section - Specify default section' or
     'Section - Section that contains the earliest "Not attempted" activity'.
  3. Fixed cosmetic issues with Framed sections.  Note:  The frame around the section will change when editing to
     accomodate the elements within the sides.
  4. Implemented 'Edit mode' -> 'Add section below'.
  5. Changed framed sections left, right and bottom border width from 20 to 5 pixels when not editing.
  6. Created colour presets user interface.  Note: Removed use of JavaScript on 'Manage colour presets' button, thus
     reducing the code, solved by styling a link as a button.  Consequence can be more complicated CSS to structure
     the layout, especially in Boost - constrained by way forms are rendered and removal of CSS3 'calc()' function
     by core minifier.

Version 3.3.0.2
  1. Added progress tooltip options.
  2. Added section delivery method settings.
  3. Implemented section delivery method 'Section - Specify default section' such that when navigating to the main course
     page the specified section will be shown.
  4. Implemented section delivery method 'Section - Section that contains the earliest "Not attempted" activity' such that
     when navigating to the main course page the calculated section will be shown if there is one, otherwise the main course
     page will be shown.  By "Not attempted" this means 'Incomplete' as there is no 'Attempted but not finished / in progress'
     state available in the Moodle API.  But there is an 'Not viewed' state to consider.

Version 3.3.0.1
  1. Clone of topics format.
  2. Added NED format completion icons.  Note: Using own extended 'core_course_render' with overridden method
     'course_section_cm_completion', thus if any theme extends the core course renderer then any changes will
     not be called for any method called directly or indirectly by the format renderer $this->courseformat attribute
     as effectivly they are two different objects.
  3. Added 'nediconsleft' option.
  4. Added 'sectioncontentjustification' option.  Note: Uses 'Flexbox' so might not be the solution if old browsers used.
  5. Added activity tracking background colours.
  6. Added all 'showsection0' options, being 'hide', 'show' and 'show only section 0'.
  7. Added 'Edit course settings' to the course administration menu.
  8. Disable 'sectioncontentjustification' setting.
  9. Remove 'Your progress' and add down arrow.
 10. Change 'Edit course settings' to 'Edit Ned Format settings'.
 11. Added view "Jump to" menu and "forward/back" navigation settings.
 12. Fix completion icons overlay on activities / resources.
 13. Fix progress help icon position.
 14. Fix activity background width and space between.
 15. Added "Jump to" menu and "forward/back" navigation "Nobody" option.
 16. Fix unsuccessful completion background and 'draft / waiting for grade' states.
 17. Fix saved, submitted and waiting for grade icons.
 18. Fix 'Edit NED Format settings' appearing for all formats.
 19. Fix completion background colour for complete fixes.