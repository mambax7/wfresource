<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeHtmlspecialchars.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use XoopsModules\Wfresource;

/**
 * SanitizeHtmlspecialchars
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeHtmlspecialchars extends Wfresource\Request
{
    /**
     * SanitizeHtmlspecialchars::render()
     *
     * @param $method
     * @param $key
     * @return bool|mixed
     */
    public function doRender($method, $key)
    {
        if (!empty($method) && \is_int($method)) {
            if (\defined('FILTER_SANITIZE_ADD_SLASHES')) {
                $ret = \filter_input($method, $key, \FILTER_SANITIZE_ADD_SLASHES);
            } else {
                $ret = \filter_input($method, $key, \FILTER_SANITIZE_MAGIC_QUOTES);
            }
        } else {
            $method = (\is_array($method) && isset($method[$key])) ? $method[$key] : $method;
            if (\defined('FILTER_SANITIZE_ADD_SLASHES')) {
                return \filter_var($variable, \FILTER_SANITIZE_ADD_SLASHES);
            }

            return \filter_var($variable, \FILTER_SANITIZE_MAGIC_QUOTES);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }
}
