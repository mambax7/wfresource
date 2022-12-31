<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeUrl.php
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
 * SanitizeUrl
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeUrl extends Wfresource\Request
{
    /**
     * xo_Filters_Validate_String::doRender()
     *
     * @param mixed $method
     * @param mixed $key
     * @return bool|mixed
     */
    public function doRender($method, $key)
    {
        if (\is_int($method)) {
            $ret = \filter_input($method, $key, \FILTER_SANITIZE_URL);
        } else {
            $method = \is_array($method) ? $method[$key] : $method;
            $ret    = \filter_var($method, \FILTER_SANITIZE_URL);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }
}
