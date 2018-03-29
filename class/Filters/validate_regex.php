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
 * xo_Filters_Validate_String
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class xo_Filters_Validate_Regex extends wfp_Request
{
    /**
     * xo_Filters_Validate_String::doRender()
     *
     * @param  null $reg
     * @return bool
     */
    public function doRender($reg = null)
    {
        $valid_reg = @filter_var($reg, FILTER_VALIDATE_REGEXP);
        if (false !== $valid_reg) {
            return true;
        }

        return false;
    }
}
