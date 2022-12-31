<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateRegex.php
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
 * ValidateRegex
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateRegex extends Wfresource\Request
{
    /**
     * ValidateRegex::doRender()
     *
     * @param null $reg
     * @return bool
     */
    public function doRender($reg = null)
    {
        $valid_reg = @\filter_var($reg, \FILTER_VALIDATE_REGEXP);
        if (false !== $valid_reg) {
            return true;
        }

        return false;
    }
}
