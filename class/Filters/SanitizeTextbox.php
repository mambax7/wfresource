<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeTextbox.php
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
 * SanitizeTextbox
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeTextbox extends Wfresource\Request
{
    /**
     * xo_Filters_Validate_String::render()
     *
     * @param $method
     * @param $key
     * @return bool|mixed
     */
    public function doRender($method, $key)
    {
        if (!empty($method) && \is_int($method)) {
            $ret = \filter_input($method, $key, \FILTER_UNSAFE_RAW, \FILTER_FLAG_STRIP_LOW | \FILTER_FLAG_STRIP_HIGH | \FILTER_FLAG_NO_ENCODE_QUOTES);
        } else {
            $method = (\is_array($method) && isset($method[$key])) ? $method[$key] : $method;
            $ret    = \filter_var($method, \FILTER_UNSAFE_RAW, \FILTER_FLAG_STRIP_LOW | \FILTER_FLAG_STRIP_HIGH | \FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }
}
