<?php
/*===================================================================*/
/* Define shortcuts                                                  */
/*===================================================================*/
/**
 * Define DIRECTORY_SEPARATOR shortcut
 */
CSR::define('DS', DIRECTORY_SEPARATOR);


/*===================================================================*/
/* Define options                                                    */
/*===================================================================*/
/**
 * Define CSR mode (true | false)
 * <p>if true then CSR running develop mode.</p>
 */
CSR::define('CSR_DEVELOP_MODE', true);


/*===================================================================*/
/* Define pathes                                                     */
/*===================================================================*/
/**
 * Define CSR root directory from document root
 */
CSR::define('ROOT_PATH'  , strcmp(dirname($_SERVER['SCRIPT_NAME']), '/') !== 0 ? dirname($_SERVER['SCRIPT_NAME']) . '/' : '/');
/**
 * Define CSR image directory
 */
CSR::define('IMAGES_PATH', ROOT_PATH . 'images/');
/**
 * Define CSR css directory
 */
CSR::define('CSS_PATH', ROOT_PATH . 'css/');
/**
 * Define CSR external javascript directory
 */
CSR::define('JS_PATH', ROOT_PATH . 'js/');


/*===================================================================*/
/* Define directories                                                */
/*===================================================================*/
/**
 * Define CSR install directory
 */
CSR::define('CSR_INSTALL_DIR', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
/**
 * Define CSR core files directory
 */
CSR::define('CSR_CORE_DIR', CSR_INSTALL_DIR . 'core' . DS);
/**
 * Define CSR applciations directory
 */
CSR::define('CSR_APP_DIR', CSR_INSTALL_DIR . 'applications' . DS);
/**
 * Define CSR modules directory
 */
CSR::define('CSR_MODULES_DIR', CSR_INSTALL_DIR . 'modules' . DS);
/**
 * Define CSR Plugins directory
 */
CSR::define('CSR_PLUGINS_DIR', CSR_INSTALL_DIR . 'plugins' . DS);
/**
 * Define CSR error documents directory
 */
CSR::define('CSR_ERRORS_DIR', CSR_CORE_DIR . 'errors' . DS);
/**
 * Define CSR debug help files directory
 */
CSR::define('CSR_DEBUG_HELP_DIR', CSR_ERRORS_DIR . 'debug' . DS);


/*===================================================================*/
/* Define events                                                     */
/*===================================================================*/
/**
 * Define application start event
 */
CSR::define('CSR_EVENT_APPLICATION_START', 'event_application_start');
/**
 * Define application end event
 */
CSR::define('CSR_EVENT_APPLICATION_END', 'event_application_end');
/**
 * Define before routing event
 */
CSR::define('CSR_EVENT_BEFORE_ROUTING', 'event_before_routing');
/**
 * Define after routing event
 */
CSR::define('CSR_EVENT_AFTER_ROUTING', 'event_after_routing');
/**
 * Define before routing event
 */
CSR::define('CSR_EVENT_BEFORE_EXEC_TARGET_FUNCTION', 'event_before_exec_target_function');
/**
 * Define after routing event
 */
CSR::define('CSR_EVENT_AFTER_EXEC_TARGET_FUNCTION', 'event_after_exec_target_function');
/**
 * Define dispatch error event
 */
CSR::define('CSR_EVENT_DISPATCH_ERROR', 'event_dispatch_error');