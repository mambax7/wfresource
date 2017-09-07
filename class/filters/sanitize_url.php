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
class xo_Filters_Sanitize_Url extends wfp_Request
{
    /**
     * xo_Filters_Validate_String::doRender()
     *
     * @param  mixed $method
     * @param  mixed $key
     * @return bool|mixed
     */
    public function doRender($method, $key)
    {
        if (is_int($method)) {
            $ret = filter_input($method, $key, FILTER_SANITIZE_URL);
        } else {
            $method = is_array($method) ? $method[$key] : $method;
            $ret    = filter_var($method, FILTER_SANITIZE_URL);
        }
        if ($ret === false) {
            return false;
        }

        return $ret;
    }
}
