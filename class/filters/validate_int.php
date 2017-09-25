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
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * xo_Filters_Validate_Int
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class xo_Filters_Validate_Int extends wfp_Request
{
    /**
     * xo_Filters_Validate_String::render()
     *
     * @param  null $int
     * @return bool
     */
    public function doRender($int = null)
    {
        $valid_int = filter_var($value, FILTER_VALIDATE_INT);
        if (false !== $valid_int) {
            return true;
        }

        return false;
    }
}
