<?php
/**
 * Name: class.loader.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 * @version    : $Id: xo_loader.php 10055 2012-08-11 12:46:10Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

define('DS', DIRECTORY_SEPARATOR);
define('XO_ROOT_PATH', dirname(dirname(dirname(__DIR__))));

/**
 * xo_Loader
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @version   $Id: xo_loader.php 10055 2012-08-11 12:46:10Z beckmi $
 * @access    public
 */
class xo_Loader
{
    private static   $instance  = array();
    private static   $config    = array();
    private static   $paths     = array();
    private static   $urls      = array();
    protected static $handlers  = array();
    protected static $languages = array();
    protected static $services  = array();

    /**
     * xo_Loader::__construct()
     */
    private function __construct()
    {
    }

    /**
     * xo_Loader::loadObjectHandlers()
     *
     * @return
     */
    public function loadObjectHandlers()
    {
    }

    /**
     * xo_Loader::loadHandler()
     *
     * @return
     */
    public static function loadHandler($var, $module = 'system', $class = 'xoops_', $options = null, $args = null)
    {
        $path = ($module == 'system') ? 'kernel' : 'modules' . DS . $module . DS . 'class';

        $filename = $class . $var . '.php';

        echo $path . DS . $filename;

        $result = self::loadInclude($path . DS . $filename);
        if ($result) {
            if (!stristr(strtolower($var), $class)) {
                $var = $class . ucfirst($var) . 'Handler';
            }
            $md5var = md5($var . $options);
            if (!isset(self::$services[$md5var])) {
                self::$services[$md5var] = &self::getService($var, $options, $args);
            }
            $ret = &self::$services[$md5var];

            return $ret;
        }

        return false;
        unset($ret);
    }

    /**
     * xo_Loader::loadClass()
     *
     * @return
     */
    public static function loadClass($var, $options = null, $args = null)
    {
    }

    /**
     * xo_Loader::loadService()
     *
     * @return
     */
    public static function loadService($var, $options = null, $args = null)
    {
    }

    /**
     * xo_Loader::getService()
     *
     * @param mixed $var
     * @param mixed $options
     * @param mixed $args
     * @return
     */
    private static function &getService($var, $options = null, $args = null)
    {
        /**
         */
        if (!isset(self::$instance[$var])) {
            if (class_exists($var)) {
                self::$instance[$var] = new $var();
            } else {
                // xo_Errors::raiseError( array( 'item' => $var, 'class' => __CLASS__, 'file' => __FILE__, 'line' => __LINE__ ) );
            }
        }
        /**
         */
        if (isset(self::$instance[$var]) && is_object(self::$instance[$var])) {
            $ret = self::_addOptions(self::$instance[$var], $options);
            if ($ret) {
                return $ret;
            }
            if (method_exists(self::$instance[$var], 'xoRun')) {
                if (!isset(self::$xorun[$var])) {
                    self::$instance[$var]->xoRun($options);
                    self::$xorun[$var] = true;
                }
            }
            // self::loadHelper( $var );
            $ret = &self::$instance[$var];

            return $ret;
        } else {
            $ret = false;

            return $ret;
        }
        unset($ret);
    }

    /**
     * Set several properties of an object
     */
    private static function _addOptions(&$instance, $options = null)
    {
        if ($instance) {
            if (isset($options) && is_object($options)) {
                $options = get_object_vars($options);
            }
            if (!is_null($options)) {
                foreach ($options as $key => $val) {
                    if (is_callable(array(&$instance, $method = 'set' . ucfirst($key)))) {
                        $instance->$method($val);
                    } elseif (is_callable(array(&$instance, $method = $key))) {
                        $instance->$method($val);
                    } else {
                        $instance->$key = $val;
                    }
                }
            }
        }
        unset($instance);
    }

    /**
     * xo_Loader::getHelper()
     *
     * @return
     */
    private static function loadHelper($var)
    {
        if (!isset(self::$services[$var])) {
            self::$services[$var] = self::getHelper($var);
        }
        $ret = self::$services[$var];
        if ($ret) {
            include_once $ret;
        }
    }

    /**
     * xo_Loader::loadHelper()
     *
     * @return
     */
    private static function &getHelper($var)
    {
        $ret = strtolower($var . '-helper');
        $ret = &self::path(XOOSLA_SERVICE_PATH . DS . strtolower($var) . DS . $ret);

        return $ret;
        unset($ret);
    }

    /**
     * xo_Loader::loadLanguage()
     *
     * @return
     */
    public static function loadLanguage($var, $module = null, $language = null)
    {
        if (!empty($var)) {
            $language  = (!is_null($language)) ? $language : $GLOBALS['xoopsConfig']['language'];
            $buildPath = XOOPS_ROOT_PATH;
            if ($module != null) {
                $buildPath = DS . 'modules' . DS . $module;
            }
            $buildPath = DS . 'language' . DS . $var . '.php';
            if (self::path($buildPath)) {
                return self::path($buildPath);
            }
        }
        trigger_error('Path does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::loadInclude()
     *
     * @return
     */
    public static function loadInclude($var, $type = null)
    {
        $var = &self::path($var);
        if ($var != false) {
            switch ($type) {
                case 'include':
                    include $var;
                    break;
                case 'include_once':
                default;
                    include_once $var;
                    break;
            }
        }

        return false;
    }

    /**
     * xo_Loader::path()
     *
     * @param mixed $var
     * @param mixed $isVirtual
     * @param mixed $verbose
     * @return
     */
    public static function path($var, $isVirtual = false, $verbose = true)
    {
        $ret = false;
        if (preg_match("/^[\.]{1,2}$/", $var)) {
            return false;
        }
        $md5Name = md5($var);
        /**
         * Return a physical path
         */
        if ($isVirtual == false) {
            if (isset(self::$paths[$md5Name])) {
                return self::$paths[$md5Name];
            }
            $fileName = self::buildpath($var);
            if ($fileName != false) {
                if ((stristr($fileName, XOOPS_ROOT_PATH) == true)) {
                    $filename = str_replace(XOOPS_ROOT_PATH, '', $filename);
                }
                self::$paths[$md5Name] = XO_ROOT_PATH . DS . $fileName;
                if ($verbose == true) {
                    echo self::$paths[$md5Name];
                }
                /**
                 * do check to see if file actually exists
                 */
                if (self::fileExists(self::$paths[$md5Name])) {
                    return self::$paths[$md5Name];
                }
            }
        } else {
            if (isset(self::$urls[$md5Name])) {
                return self::$urls[$md5Name];
            }
            $var                  = str_replace('\\', '/', $var);
            self::$urls[$md5Name] = (stristr($var, XOOPS_URL) === false) ? XOOPS_URL . '/' . $var : $var;

            return self::$urls[$md5Name];
        }
        trigger_error('file not found');

        return false;
    }

    /**
     * xo_Loader::buildpath()
     *
     * @param mixed  $var
     * @param string $ext
     * @return
     */
    private static function buildpath()
    {
        if (func_num_args() == 1) {
            $var = func_get_arg(0);
            $ext = 'php';
            if (stristr($var, XOOPS_ROOT_PATH . DS) == true) {
                $var = str_replace(XOOPS_ROOT_PATH . DS, '', $var);
            }
            $fileName = preg_replace('/[\/\\\]/U', '.', $var);

            if (stristr($fileName, '.php') == true) {
                $fileName = str_replace('.php', '', $fileName);
            } elseif ($pos = stristr($fileName, '.php') == true) {
                $fileName = substr($fileName, $pos + 4);
            }
            $parts    = explode('.', $fileName);
            $fileName = implode(DS, $parts) . '.' . $ext;
            /**
             * hopefully stop root access to root
             */
            if (preg_match('/^[\.]{1,2}$/', $fileName)) {
                trigger_error('Direct root access forbidden in ' . __FILE__ . ' ' . __LINE__);

                return false;
            }

            return $fileName;
        }

        return false;
    }

    /**
     * xo_Loader::fileExists()
     *
     * @return
     */
    public function fileExists()
    {
        if (func_num_args() == 1) {
            $var = func_get_arg(0);
            if (stristr($var, XO_ROOT_PATH) === false) {
                $var = XO_ROOT_PATH . DS . $var;
            }
            if (file_exists($var) && !is_dir($var)) {
                return true;
            }
        }
        trigger_error('File does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::dirExists()
     *
     * @return
     */
    public function dirExists()
    {
        if (func_num_args() == 1) {
            $var = func_get_arg(0);
            if (stristr($var, XO_ROOT_PATH) === false) {
                $var = XO_ROOT_PATH . DS . $var;
            }
            if (file_exists($var) && is_dir($var)) {
                return true;
            }
        }
        trigger_error('Directory does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::loadConfig()
     *
     * @return
     */
    public static function getConfig()
    {
        if (func_num_args() == 1) {
            $var = func_get_arg(0);
            echo $var;

            if (isset($GLOBALS['xoopsConfig'][$var])) {
                return $GLOBALS['xoopsConfig'][$var];
            }
        }

        return false;
    }

    /**
     * xo_Loader::loadConfig()
     *
     * @return
     */
    public static function loadConfig()
    {
        if (func_num_args() == 1) {
            $var    = func_get_arg(0);
            $config = &xo_Loader::loadHandler('config');

            return $config->getConfigsByCat($var);
        }

        return false;
    }

    /**
     * xo_Loader::setConfig()
     *
     * @return
     */
    public static function setConfig()
    {
        if (func_num_args() == 2) {
            $var      = func_get_arg(0);
            $newValue = func_get_arg(1);
            if (isset(self::$config[$var])) {
                self::$config[$var] = strip_tags($var);
            }
        }

        return false;
    }

    /**
     * xo_Loader::fileExtension()
     *
     * @param mixed $filename
     * @return
     */
    public function fileExtension($filename)
    {
        $path_info = pathinfo($filename);

        return $path_info['extension'];
    }
}
