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
 * Request
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Request
{
    public static $method;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Wfresource\Filter::doRequest()
     *
     * @param                  $method
     * @param mixed            $key
     * @param mixed            $default
     * @param mixed            $type
     * @param array            $options
     * @param string           $module
     * @return bool|mixed|null
     */
    public static function doRequest($method, $key, $default = null, $type = null, $options = [], $module = '')
    {
        if (ctype_alpha($type)) {
            $filter = Filter::getFilter('Sanitize' . \ucfirst($type), $module);
            if (null !== $filter && \is_object($filter)) {
                $ret = $filter->doRender($method, $key, $options);

                return (false === $ret) ? $default : $ret;
            }
        }
        unset($filter);

        return false;
    }

    /**
     * Wfresource\Request::doValidate()
     *
     * @param         $value
     * @param         $type
     * @param string  $module
     * @param null    $flags
     * @return bool
     */
    public static function doValidate($value, $type, $module = '', $flags = null)
    {
        if (ctype_alpha($type)) {
            $filter = Filter::getFilter('Validate' . \ucfirst($type), $module);
            if (null !== $filter && \is_object($filter)) {
                if (false !== ($ret = $filter->doRender($value, $flags))) {
                    return (false === $ret) ? $default : $ret;
                }
            }
        }
        unset($filter);

        return false;
    }

    /**
     * Wfresource\Request::inArray()
     *
     * @param $method
     * @param $key
     * @return bool
     */
    public static function inArray($method, $key): ?bool
    {
        if (empty($method) || empty($key)) {
            return \filter_has_var($method, $key);
        }
    }
}
