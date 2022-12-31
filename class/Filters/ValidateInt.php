<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateInt.php
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
 * ValidateInt
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateInt extends Wfresource\Request
{
    /**
     * ValidateInt::render()
     *
     * @param null $int
     * @return bool
     */
    public function doRender($int = null)
    {
        $valid_int = \filter_var($value, \FILTER_VALIDATE_INT);
        if (false !== $valid_int) {
            return true;
        }

        return false;
    }
}
