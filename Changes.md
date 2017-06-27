Version Information
===================
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
 11. Added view "Jump to" menu and 'forward/back' navigation settings.
 12. Fix completion icons overlay on activities / resources.
 13. Fix progress help icon position.