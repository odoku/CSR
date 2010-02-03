<?php
/*===================================================================*/
/* Define settings                                                   */
/*===================================================================*/
/**
 * Define CSR_MVC target regexp prefix
 */
CSR::define('CSR_MVC_PREFIX', '');
/**
 * Define CSR_MVC target regexp
 */
CSR::define('CSR_MVC_TARGET_REGEXP', sprintf('/^%s\w+\/\w+$/', CSR_MVC_PREFIX));
/**
 * Define default layout file name
 */
CSR::define('CSR_LAYOUT_FILE_NAME', '_layout.html');


/*===================================================================*/
/* Define directories                                                */
/*===================================================================*/
/**
 * Define CSR_MVC root directory
 */
CSR::define('CSR_MVC_ROOT_DIR', CSR_PLUGINS_DIR . 'mvc' . DS);
/**
 * Define CSR_MVC application directory
 */
CSR::define('CSR_MVC_APP_DIR', CSR_MVC_ROOT_DIR . 'applications' . DS);
/**
 * Define CSR controllers directory
 */
CSR::define('CSR_CONTROLLERS_DIR', CSR_MVC_APP_DIR . 'controllers' . DS);
/**
 * Define CSR views directory
 */
CSR::define('CSR_VIEWS_DIR', CSR_MVC_APP_DIR . 'views' . DS);
/**
 * Define CSR models directory
 */
CSR::define('CSR_MODELS_DIR', CSR_MVC_APP_DIR . 'models' . DS);
/**
 * Define CSR_MVC errors directory
 */
CSR::define('CSR_MVC_ERRORS_DIR', CSR_MVC_ROOT_DIR . 'libs' . DS . 'errors' . DS);
/**
 * Define CSR_MVC debug help files directory
 */
CSR::define('CSR_MVC_DEBUG_HELP_DIR', CSR_MVC_ERRORS_DIR . 'debug' . DS);


/*===================================================================*/
/* Define events                                                     */
/*===================================================================*/
/**
 * Define before action event
 */
CSR::define('CSR_EVENT_BEFORE_ACTION', 'event_before_action');
/**
 * Define after action event
 */
CSR::define('CSR_EVENT_AFTER_ACTION', 'event_after_action');
/**
 * Define before render event
 */
CSR::define('CSR_EVENT_BEFORE_RENDER', 'event_before_render');
/**
 * Define after render event
 */
CSR::define('CSR_EVENT_AFTER_RENDER', 'event_after_render');
/**
 * Define before output event
 */
CSR::define('CSR_EVENT_BEFORE_OUTPUT', 'event_before_output');
/**
 * Define after output event
 */
CSR::define('CSR_EVENT_AFTER_OUTPUT', 'event_after_output');