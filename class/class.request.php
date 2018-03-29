<?php
/**
 * Name: class.filter.php
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
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * wfp_Filter
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_Filter
{
    protected static $instance;
    protected static $handlers;
    private static $name;

    /**
     * xo_Xoosla::getIntance()
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * wfp_Filter::getFilter()
     *
     * @param  mixed $name
     * @param  null  $module
     * @return bool
     */
    public static function getFilter($name, $module = null)
    {
        static $handlers;
        self::$name = $name;
        /**
         */
        if (!isset($handlers[self::$name])) {
            // $ret = self::getModule( $module );
            // if ($ret !== false) {
            $ret = self::getCore();
            // }
            if (true !== $ret) {
                $className = 'xo_Filters_' . self::$name;
                if (class_exists($className) && is_callable(__CLASS__, $className)) {
                    $handler = new $className(__CLASS__);
                    if (!is_object($handler)) {
                        trigger_error('value is null, sort it or else sucker');

                        return null;
                    }
                    $handlers[self::$name] = $handler;
                }
            } else {
                trigger_error('Error: Filter <b>' . $name . '</b> could not be load due to an error. Please check the filter name and try again.<br>File: ' . __FILE__ . ' line: ' . __LINE__);
            }
        }
        unset($name);
        if (!isset($handlers[self::$name])) {
            return false;
            trigger_error('Filter name ' . self::$name . 'does not exist');
        }

        return $handlers[self::$name];
    }

    /**
     * wfp_Filter::getCore()
     * @return bool
     */
    public static function getCore()
    {
        if (file_exists($file = __DIR__ . DS . 'filters' . DS . strtolower(self::$name) . '.php')) {
            require_once strtolower($file);
        }
        unset($file);

        return false;
    }

    /**
     * wfp_Filter::getModule()
     *
     * @param  null $module
     * @return bool
     */
    public function getModule($module = null)
    {
        $module = (null !== $module) ? $module : $GLOBALS['xoopsModule'];
        if (file_exists($file = XOOPS_ROOT_PATH . '/modules' . $module . '/filters/' . strtolower(self::$name) . '.php')) {
            require_once $file;
        } else {
            trigger_error($file);
        }
        unset($file);

        return false;
    }

    /**
     * wfp_Filter::getUser()
     *
     */
    public function getUser()
    {
    }

    /**
     * wfp_Filter::filterValidate()
     *
     * @param       $value
     * @param  int  $filterid
     * @return bool
     */
    public function filterValidate($value, $filterid = 0)
    {
        return filter_var($value, (int)$filterid) ? true : false;
    }
}

/**
 * wfp_Request
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_Request
{
    public static $method;

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct()
    {
    }

    /**
     * wfp_Filter::doRequest()
     *
     * @param                  $method
     * @param  mixed           $key
     * @param  mixed           $default
     * @param  mixed           $type
     * @param  array           $options
     * @param  string          $module
     * @return bool|mixed|null
     */
    public static function doRequest($method, $key, $default = null, $type = null, $options = [], $module = '')
    {
        if (ctype_alpha($type)) {
            $filter = wfp_Filter::getFilter('Sanitize_' . ucfirst($type), $module);
            if (!empty($filter) && is_object($filter)) {
                $ret = $filter->doRender($method, $key, $options);

                return (false === $ret) ? $default : $ret;
            }
        }
        unset($filter);

        return false;
    }

    /**
     * wfp_Request::doValidate()
     *
     * @param         $value
     * @param         $type
     * @param  string $module
     * @param  null   $flags
     * @return bool
     */
    public static function doValidate($value, $type, $module = '', $flags = null)
    {
        if (ctype_alpha($type)) {
            $filter = wfp_Filter::getFilter('Validate_' . ucfirst($type), $module);
            if (!empty($filter) && is_object($filter)) {
                if (false !== ($ret = $filter->doRender($value, $flags))) {
                    return (false === $ret) ? $default : $ret;
                }
            }
        }
        unset($filter);

        return false;
    }

    /**
     * wfp_Request::inArray()
     *
     * @param $method
     * @param $key
     * @return bool
     */
    public static function inArray($method, $key)
    {
        if (empty($method) || empty($key)) {
            return filter_has_var($method, $key);
        }
    }
}
