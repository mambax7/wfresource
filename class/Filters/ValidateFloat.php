<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateFloat.php
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
 * ValidateFloat
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateFloat extends Wfresource\Request
{
    /**
     * ValidateFloat::doRender()
     *
     * @param null $float
     * @return bool
     */
    public function doRender($float = null)
    {
        $valid_float = \filter_var($float, \FILTER_VALIDATE_FLOAT);
        if (false !== $valid_float) {
            return true;
        }

        return false;
    }
}
