<?php

declare(strict_types=1);

namespace XoopsModules\Wfresource;

use function md5;

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
 */

\define('DS', \DIRECTORY_SEPARATOR);
\define('XO_ROOT_PATH', \dirname(__DIR__, 3));

/**
 * xo_Loader
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class xo_Loader
{
    private static array $instance = [];
    private static array $config = [];
    private static array $paths    = [];
    private static array $urls      = [];
    private static array $handlers  = [];
    private static array $languages = [];
    private static array $services  = [];

    /**
     * xo_Loader::__construct()
     */
    private function __construct()
    {
    }

    /**
     * xo_Loader::loadObjectHandlers()
     */
    public function loadObjectHandlers(): void
    {
    }

    /**
     * xo_Loader::loadHandler()
     *
     * @param string      $var
     * @param string|null $module
     * @param string|null $class
     * @param null        $options
     * @param null        $args
     * @return bool
     */
    public static function loadHandler(string $var, string $module = null, string $class = null, $options = null, $args = null): ?bool
    {
        $module ??= 'system';
        $class  ??= 'xoops_';

        $path     = ('system' === $module) ? 'kernel' : 'modules/' . $module . '/class';
        $filename = $class . $var . '.php';
        echo $path . DS . $filename;

        $result = self::loadInclude($path . DS . $filename);
        if ($result) {
            if (false === mb_stripos(mb_strtolower($var), $class)) {
                $var = $class . \ucfirst($var) . 'Handler';
            }
            $md5var = md5($var . $options);
            if (!isset(self::$services[$md5var])) {
                self::$services[$md5var] = self::getService($var, $options, $args);
            }
            $ret = &self::$services[$md5var];

            return $ret;
        }

        return false;
        //        unset($ret);
    }

    /**
     * xo_Loader::loadClass()
     *
     * @param      $var
     * @param null $options
     * @param null $args
     */
    public static function loadClass($var, $options = null, $args = null): void
    {
    }

    /**
     * xo_Loader::loadService()
     *
     * @param      $var
     * @param null $options
     * @param null $args
     */
    public static function loadService($var, $options = null, $args = null): void
    {
    }

    /**
     * xo_Loader::getService()
     *
     * @param mixed $var
     * @param mixed $options
     * @param mixed $args
     * @return bool|self
     */
    private static function getService($var, $options = null, $args = null)
    {
        $ret = false;
        if (!isset(self::$instance[$var])) {
            if (\class_exists($var)) {
                self::$instance[$var] = new $var();
            }
            // xo_Errors::raiseError( array( 'item' => $var, 'class' => __CLASS__, 'file' => __FILE__, 'line' => __LINE__ ) );
        }

        if (isset(self::$instance[$var]) && \is_object(self::$instance[$var])) {
            $ret = self::_addOptions(self::$instance[$var], $options);
            if ($ret) {
                return $ret;
            }
            if (\method_exists(self::$instance[$var], 'xoRun')) {
                if (!isset(self::$xorun[$var])) {
                    self::$instance[$var]->xoRun($options);
                    self::$xorun[$var] = true;
                }
            }
            // self::loadHelper( $var );
            $ret = &self::$instance[$var];

            return $ret;
        }

        return false;
//        unset($ret);
    }

    /**
     * Set several properties of an object
     * @param self       $instance
     * @param array|null $options
     */
    private static function _addOptions(&$instance, array $options = null): void
    {
        if ($instance) {
            if (\is_object($options)) {
                $options = \get_object_vars($options);
            }
            if (null !== $options) {
                foreach ($options as $key => $val) {
                    if (\is_callable([&$instance, $method = 'set' . \ucfirst($key)])) {
                        $instance->$method($val);
                    } elseif (\is_callable([&$instance, $method = $key])) {
                        $instance->$method($val);
                    } else {
                        $instance->$key = $val;
                    }
                }
            }
        }
//        unset($instance);
    }

    /**
     * xo_Loader::getHelper()
     *
     * @param $var
     */
    private static function loadHelper($var): void
    {
        if (!isset(self::$services[$var])) {
            self::$services[$var] = self::getHelper($var);
        }
        $ret = self::$services[$var];
        if ($ret) {
            require_once $ret;
        }
    }

    /**
     * xo_Loader::loadHelper()
     *
     * @param $var
     * @return bool|string
     */
    private static function getHelper($var)
    {
        $ret = \mb_strtolower($var . '-helper');
        $ret = self::path(XOOSLA_SERVICE_PATH . DS . \mb_strtolower($var) . DS . $ret);

        return $ret;
        //        unset($ret);
    }

    /**
     * xo_Loader::loadLanguage()
     *
     * @param       $var
     * @param null  $module
     * @param null  $language
     * @return bool
     */
    public static function loadLanguage($var, $module = null, $language = null)
    {
        if (!empty($var)) {
            $language  = $language ?? $GLOBALS['xoopsConfig']['language'];
            $buildPath = XOOPS_ROOT_PATH;
            if (null !== $module) {
                $buildPath = DS . 'modules' . DS . $module;
            }
            $buildPath = DS . 'language' . DS . $var . '.php';
            if (self::path($buildPath)) {
                return self::path($buildPath);
            }
        }
        \trigger_error('Path does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::loadInclude()
     *
     * @param mixed $var
     * @param null  $type
     * @return bool
     */
    public static function loadInclude($var, $type = null): bool
    {
        $var = self::path($var);
        if (false !== $var) {
            switch ($type) {
                case 'include':
                    require $var;
                    break;
                case 'require':
                default:
                    require_once $var;
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
     * @return bool
     */
    public static function path($var, $isVirtual = null, $verbose = null)
    {
        $isVirtual ??= false;
        $verbose   ??= true;

        $ret = false;
        if (\preg_match('/^[\.]{1,2}$/', $var)) {
            return false;
        }
        $md5Name = md5($var);
        /**
         * Return a physical path
         */
        if (false !== $isVirtual) {
            if (isset(self::$urls[$md5Name])) {
                return self::$urls[$md5Name];
            }
            $var                  = \str_replace('\\', '/', $var);
            self::$urls[$md5Name] = (false === mb_stripos($var, XOOPS_URL)) ? XOOPS_URL . '/' . $var : $var;

            return self::$urls[$md5Name];
        } else {
            if (isset(self::$paths[$md5Name])) {
                return self::$paths[$md5Name];
            }
            $fileName = self::buildpath($var);
            if (false !== $fileName) {
                if (true === mb_stristr($fileName, XOOPS_ROOT_PATH)) {
                    $filename = \str_replace(XOOPS_ROOT_PATH, '', $filename);
                }
                self::$paths[$md5Name] = \XO_ROOT_PATH . DS . $fileName;
                if (true === $verbose) {
                    echo self::$paths[$md5Name];
                }
                /**
                 * do check to see if file actually exists
                 */
                if (self::fileExists(self::$paths[$md5Name])) {
                    return self::$paths[$md5Name];
                }
            }
        }
        \trigger_error('file not found' . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::buildpath()
     * @return false|string
     */
    private static function buildpath(...$args)
    {
        if (1 === \func_num_args()) {
            $var = \func_get_arg(0);
            $ext = 'php';
            if (true === mb_stristr($var, XOOPS_ROOT_PATH . DS)) {
                $var = \str_replace(XOOPS_ROOT_PATH . DS, '', $var);
            }
            $fileName = \preg_replace('/[\/\\\]/U', '.', $var);

            if (true === mb_stristr($fileName, '.php')) {
                $fileName = \str_replace('.php', '', $fileName);
            } elseif ($pos = (true === mb_stristr($fileName, '.php'))) {
                $fileName = mb_substr($fileName, $pos + 4);
            }
            $parts    = \explode('.', $fileName);
            $fileName = \str_replace('.', DS, $fileName) . '.' . $ext;
            /**
             * hopefully stop root access to root
             */
            if (\preg_match('/^[\.]{1,2}$/', $fileName)) {
                \trigger_error('Direct root access forbidden in ' . __FILE__ . ' ' . __LINE__);

                return false;
            }

            return $fileName;
        }

        return false;
    }

    /**
     * xo_Loader::fileExists()
     * @param mixed ...$args
     * @return bool
     */
    public static function fileExists(...$args): bool
    {
        if (1 === \func_num_args()) {
            $var = \func_get_arg(0);
            if (false === mb_stripos($var, \XO_ROOT_PATH)) {
                $var = \XO_ROOT_PATH . DS . $var;
            }
            if (\is_dir($var) && !\is_dir($var)) {
                return true;
            }
        }
        \trigger_error('File does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::dirExists()
     * @param mixed ...$args
     * @return bool
     */
    public function dirExists(...$args): bool
    {
        if (1 === \func_num_args()) {
            $var = \func_get_arg(0);
            if (false === mb_stripos($var, \XO_ROOT_PATH)) {
                $var = \XO_ROOT_PATH . DS . $var;
            }
            if (\is_dir($var) && \is_dir($var)) {
                return true;
            }
        }
        \trigger_error('Directory does not exist: ' . $var . ' ' . __FILE__ . ' ' . __LINE__);

        return false;
    }

    /**
     * xo_Loader::loadConfig()
     * @return bool|string|int
     */
    public static function getConfig(...$args)
    {
        if (1 === \func_num_args()) {
            $var = \func_get_arg(0);
            echo $var;

            if (isset($GLOBALS['xoopsConfig'][$var])) {
                return $GLOBALS['xoopsConfig'][$var];
            }
        }

        return false;
    }

    /**
     * xo_Loader::loadConfig()
     * @return bool|string|int
     */
    public static function loadConfig(...$var)
    {
        if (1 === \func_num_args()) {
//            $var    = \func_get_arg(0);
            $config = self::loadHandler('config');

            return $config->getConfigsByCat($var);
        }

        return false;
    }

    /**
     * xo_Loader::setConfig()
     * @return bool|string|int
     */
    public static function setConfig(...$args)
    {
        if (2 === \func_num_args()) {
            $var      = \func_get_arg(0);
            $newValue = \func_get_arg(1);
            if (isset(self::$config[$var])) {
                self::$config[$var] = \strip_tags($var);
            }
        }

        return false;
    }

    /**
     * xo_Loader::fileExtension()
     *
     * @param mixed $filename
     * @return mixed|string
     */
    public function fileExtension($filename)
    {
        $path_info = \pathinfo($filename);

        return $path_info['extension'];
    }
}
