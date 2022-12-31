<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateUrl.php
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
 * ValidateUrl
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateUrl extends Wfresource\Request
{
    /**
     * ValidateUrl::doRender()
     *
     * @param null $url
     * @return bool
     */
    public function doRender($url = null)
    {
        $valid_url = \filter_var($url, \FILTER_VALIDATE_URL);
        if (false !== $valid_url) {
            return true;
        }

        return false;
    }
}
