Version Information
===================
Version 3.3.0.1
  1. Clone of topics format.
  2. Added NED format completion icons.  Note: Using own extended 'core_course_render' with overridden method
     'course_section_cm_completion', thus if any theme extends the core course renderer then any changes will
     not be called for any method called directly or indirectly by the format renderer $this->courseformat attribute
     as effectivly they are two different objects.
