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
CSR::define('CSR_MVC_CONTROLLERS_DIR', CSR_MVC_APP_DIR . 'controllers' . DS);
/**
 * Define CSR views directory
 */
CSR::define('CSR_MVC_VIEWS_DIR', CSR_MVC_APP_DIR . 'views' . DS);
/**
 * Define CSR models directory
 */
CSR::define('CSR_MVC_MODELS_DIR', CSR_MVC_APP_DIR . 'models' . DS);
/**
 * Define CSR_MVC errors directory
 */
CSR::define('CSR_MVC_ERRORS_DIR', CSR_MVC_ROOT_DIR . 'libs' . DS . 'errors' . DS);
/**
 * Define CSR_MVC debug help files directory
 */
CSR::define('CSR_MVC_DEBUG_HELP_DIR', CSR_MVC_ERRORS_DIR . 'debug' . DS);
