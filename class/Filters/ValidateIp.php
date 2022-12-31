<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateIp.php
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
 * ValidateIp
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateIp extends Wfresource\Request
{
    /**
     * ValidateIp::doRender()
     *
     * @param mixed $ipaddress
     * @param mixed $flags      valid flags are FILTER_FLAG_IPV6, FILTER_FLAG_IPV4 FILTER_FLAG_NO_PRIV_RANGE FILTER_FLAG_NO_RES_RANGE
     *                          Flags can be an array
     * @return bool
     */
    public function doRender($ipaddress = null, $flags = null)
    {
        if (\is_array($flags)) {
            $flags = \explode('|', $flags);
        }
        $valid_url = \filter_var($url, \FILTER_VALIDATE_IP, (string)$flags);
        if (false !== $valid_url) {
            return true;
        }

        return false;
    }
}
