<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateBool.php
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
 * ValidateBool
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateBool extends Wfresource\Request
{
    /**
     * ValidateBool::doRender()
     *
     * @param null $bool
     * @return bool
     */
    public function doRender($bool = null)
    {
        $valid_bool = \filter_var($bool, \FILTER_VALIDATE_BOOLEAN);
        if (false !== $valid_bool) {
            return true;
        }

        return false;
    }
}
