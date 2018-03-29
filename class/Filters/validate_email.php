<?php
/**
 * Name: validate_string.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * xo_Filters_Validate_String
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class xo_Filters_Validate_Email extends wfp_Request
{
    /**
     * xo_Filters_Validate_String::doRender()
     *
     * @param  null $email
     * @return bool|mixed
     */
    public function doRender($email = null)
    {
        $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (false === $valid_email) {
            return false;
        }

        return $valid_email;
    }
}
