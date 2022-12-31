<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeString.php
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
 * SanitizeString
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeString extends Wfresource\Request
{
    /**
     * SanitizeString::render()
     *
     * @param $method
     * @param $key
     * @return bool|mixed
     */
    public function doRender($method, $key)
    {
        if (!empty($method) && \is_int($method)) {
            $ret = \filter_input($method, $key, \FILTER_SANITIZE_STRIPPED);
        } else {
            $method = (\is_array($method) && isset($method[$key])) ? $method[$key] : $method;
            $ret    = \filter_var($method, \FILTER_SANITIZE_STRIPPED);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }
}
