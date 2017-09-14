Version Information
===================
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