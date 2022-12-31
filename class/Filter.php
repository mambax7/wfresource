<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.filter.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * Filter
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Filter
{
    protected static $instance;
    protected static $handlers;
    private static   $name;

    /**
     * xo_Xoosla::getInstance()
     * @return self
     */
    public static function getInstance(): self
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Filter::getFilter()
     *
     * @param mixed $name
     * @param null  $module
     * @return bool
     */
    public static function getFilter($name, $module = null): ?bool
    {
        static $handlers;
        self::$name = $name;

        if (!isset($handlers[self::$name])) {
            // $ret = self::getModule( $module );
            // if ($ret !== false) {
            $ret = self::getCore();
            // }
            if (!$ret) {
                $className = 'xo_Filters_' . self::$name;
                if (\class_exists($className) && \is_callable(__CLASS__, $className)) {
                    $handler = new $className(__CLASS__);
                    if (!\is_object($handler)) {
                        \trigger_error('value is null, sort it or else sucker');

                        return null;
                    }
                    $handlers[self::$name] = $handler;
                }
            } else {
                \trigger_error('Error: Filter <b>' . $name . '</b> could not be load due to an error. Please check the filter name and try again.<br>File: ' . __FILE__ . ' line: ' . __LINE__);
            }
        }
        unset($name);
        if (!isset($handlers[self::$name])) {
            return false;
            \trigger_error('Filter name ' . self::$name . 'does not exist');
        }

        return $handlers[self::$name];
    }

    /**
     * Filter::getCore()
     * @return bool
     */
    public static function getCore()
    {
        if (\file_exists($file = __DIR__ . DS . 'filters' . DS . \mb_strtolower(self::$name) . '.php')) {
            require_once \mb_strtolower($file);
        }
        unset($file);

        return false;
    }

    /**
     * Filter::getModule()
     *
     * @param null $module
     * @return bool
     */
    public function getModule($module = null)
    {
        $module = $module ?? $GLOBALS['xoopsModule'];
        if (\is_file($file = XOOPS_ROOT_PATH . '/modules' . $module . '/filters/' . \mb_strtolower(self::$name) . '.php')) {
            require_once $file;
        } else {
            \trigger_error($file);
        }
        unset($file);

        return false;
    }

    /**
     * Filter::getUser()
     */
    public function getUser(): void
    {
    }

    /**
     * Filter::filterValidate()
     *
     * @param       $value
     * @param int   $filterid
     * @return bool
     */
    public function filterValidate($value, $filterid = 0)
    {
        return \filter_var($value, (int)$filterid) ? true : false;
    }
}
