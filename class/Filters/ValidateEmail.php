<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: ValidateEmail.php
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
 * ValidateEmail
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class ValidateEmail extends Wfresource\Request
{
    /**
     * ValidateEmail::doRender()
     *
     * @param null $email
     * @return bool|mixed
     */
    public function doRender($email = null)
    {
        $valid_email = \filter_var($email, \FILTER_VALIDATE_EMAIL);
        if (false === $valid_email) {
            return false;
        }

        return $valid_email;
    }
}
