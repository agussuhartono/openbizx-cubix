<?php
/**
 * Openbiz Framework
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package   openbiz.bin
 * @copyright Copyright (c) 2005-2011, Rocky Swen
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @link      http://www.phpopenbiz.org/
 * @version   $Id$
 */

use Openbiz\Openbiz;
use Openbiz\ClassLoader;
include_once("ClassLoader.php");


spl_autoload_register(['\Openbiz\ClassLoader', 'autoload']);
// loadCoreClassMap
$coreClassMap = include( __DIR__ . DIRECTORY_SEPARATOR . 'autoload_classmap.php' );
ClassLoader::registerClassMap($coreClassMap);

// loadVendorAutoLoadClassMap
$othersClassMap = include( realpath(__DIR__ . '/../../autoload_classmap_selected.php') );
ClassLoader::registerClassMap($othersClassMap);

//include_once("sysclass_inc.php");

if (isset($_SERVER['SERVER_NAME'])) {
    define('CLI', 0);
    define('nl', "<br/>");
} else {
    define('CLI', 1);
    define('nl', "\n");
}

register_shutdown_function("bizsystem_shutdown");

function bizsystem_shutdown()
{
    if (isset(Openbiz::$app)) {
        Openbiz::$app->getSessionContext()->saveSessionObjects();
    }
}

/* * **************************************************************************
  openbiz core path
 * ************************************************************************** */
//define('OPENBIZ_PATH', 'absolute_dir/Openbiz');
if (!defined('OPENBIZ_PATH')) {
    define('OPENBIZ_PATH', dirname(dirname(__FILE__)));
}
if (!defined('OPENBIZ_BIN')) {
    define('OPENBIZ_BIN', OPENBIZ_PATH . "/bin/");
}
if (!defined('OPENBIZ_META')) {
    define('OPENBIZ_META', OPENBIZ_PATH . "/metadata/");
}

/* * **************************************************************************
  third party library path
 * ************************************************************************** */
// Smarty package
if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', realpath(OPENBIZ_PATH . "/../Smarty/libs").'/');
}

//echo 'SMARTY_DIR ' .SMARTY_DIR;
//exit;
/* * **************************************************************************
  application services
 * ************************************************************************** */
if (!defined('AUTH_SERVICE')) {
    define('AUTH_SERVICE', "service.authService");
}
if (!defined('ACCESS_SERVICE')) {
    define('ACCESS_SERVICE', "service.accessService");
}
if (!defined('ACL_SERVICE')) {
    define('ACL_SERVICE', "service.aclService");
}
if (!defined('PROFILE_SERVICE')) {
    define('PROFILE_SERVICE', "service.profileService");
}
if (!defined('LOG_SERVICE')) {
    define('LOG_SERVICE', "service.logService");
}
if (!defined('EXCEL_SERVICE')) {
    define('EXCEL_SERVICE', "service.excelService");
}
if (!defined('OPENBIZ_PDF_SERVICE')) {
    define('OPENBIZ_PDF_SERVICE', "service.pdfService");
}
if (!defined('IO_SERVICE')) {
    define('IO_SERVICE', "service.ioService");
}
if (!defined('EMAIL_SERVICE')) {
    define('EMAIL_SERVICE', "service.emailService");
}
if (!defined('DOTRIGGER_SERVICE')) {
    define('DOTRIGGER_SERVICE', "service.doTriggerService");
}
if (!defined('GENID_SERVICE')) {
    define('GENID_SERVICE', "service.genIdService");
}
if (!defined('VALIDATE_SERVICE')) {
    define('VALIDATE_SERVICE', "service.validateService");
}
if (!defined('QUERY_SERVICE')) {
    define('QUERY_SERVICE', "service.queryService");
}
if (!defined('SECURITY_SERVICE')) {
    define('SECURITY_SERVICE', "service.securityService");
}
if (!defined('OPENBIZ_EVENTLOG_SERVICE')) {
    define('OPENBIZ_EVENTLOG_SERVICE', "service.eventlogService");
}
if (!defined('CACHE_SERVICE')) {
    define('CACHE_SERVICE', "service.cacheService");
}
if (!defined('CRYPT_SERVICE')) {
    define('CRYPT_SERVICE', "service.cryptService");
}
if (!defined('LOCALEINFO_SERVICE')) {
    define('LOCALEINFO_SERVICE', "service.localeInfoService");
}

/* whether print debug infomation or not */
if (!defined('OPENBIZ_DEBUG')) {
    define("OPENBIZ_DEBUG", 1);
}
if (!defined('PROFILING')) {
    define("PROFILING", 1);
}

/* check whether user logged in */
//if(!defined('CHECKUSER')) define("CHECKUSER", "N");
/* session timeout seconds */
if (!defined('OPENBIZ_TIMEOUT'))
    define("OPENBIZ_TIMEOUT", -1);  // -1 means never timeout.



//include system message file
include_once(OPENBIZ_PATH . "/messages/system.msg");

// defined \Zend framework library home as ZEND_FRWK_HOME
define('ZEND_FRWK_HOME', OPENBIZ_PATH . "/../");
// add zend framework to include path
//set_include_path(get_include_path() . PATH_SEPARATOR . ZEND_FRWK_HOME);


/* Popup Suffix for Modal or Popup Windows */
define('Popup_Suffix', "_popupx_");


/* global variables */




// error handling 
error_reporting(E_ALL ^ (E_NOTICE | E_STRICT));

// if use user defined error handling function, all errors are reported to the function
//$default_error_handler = set_error_handler("userErrorHandler");
set_error_handler(array('\Openbiz\Core\ErrorHandler','errorHandler'));
//ErrorHandler::ErrorHandler($errno, $errmsg, $filename, $linenum, $vars);
//$default_exception_handler = set_exception_handler('userExceptionHandler');
set_exception_handler(array('\Openbiz\Core\ErrorHandler','exceptionHandler'));
//ErrorHandler::ExceptionHandler($exc);

// set DOCUMENT_ROOT
setDocumentRoot();

/**
 * Search for the php file required to load the class
 *
 * @package openbiz.bin
 * @param string $className
 * @return void
 * */
function __autoload_openbiz($className)
{
    ClassLoader::autoload($className);
}

/*
 * Set DOCUMENT_ROOT in case the server doesn't have DOCUMENT_ROOT setting (e.g. IIS). 
 * Reference from http://fyneworks.blogspot.com/2007/08/php-documentroot-in-iis-windows-servers.html
 */
function setDocumentRoot()
{
    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
        };
    };
    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
        };
    };
}

/**
 *  formatted output given variable by using print_r() function
 *
 *  @author Garbin
 *  @param  any
 *  @return void
 */
function dump($arr, $debug = false)
{
    if ($debug)
        $debug_fun = 'debug_print_backtrace();';
    echo '<pre>';
    array_walk(func_get_args(), create_function('&$item, $key', 'print_r($item);' . $debug_fun . ''));
    echo '</pre>';
    exit();
}

/**
 *  formatted output given variable by using var_dump() function
 *
 *  @author Garbin
 *  @param  any
 *  @return void
 */
function vdump($arr, $debug = false)
{
    if ($debug)
        $debug_fun = 'debug_print_backtrace();';
    echo '<pre>';
    array_walk(func_get_args(), create_function('&$item, $key', 'var_dump($item);' . $debug_fun . ''));
    echo '</pre>';
    exit();
}