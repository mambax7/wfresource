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
class xo_Filters_Sanitize_Array extends wfp_Request
{
    /**
     * xo_Filters_Validate_String::render()
     *
     * @param         $method
     * @param         $key
     * @param  array  $options
     * @return string
     */
    public function doRender($method, $key, $options = [])
    {
        $ret = isset($method[$key]) ? $method[$key] : '';
        #       $options = $this->checkOption( $options );
        #       filter_input_array(INPUT_POST, $args);
        #
        #       if ( is_int( $method ) ) {
        #           $ret = filter_input_array( $method, $key, FILTER_SANITIZE_ENCODED, $options );
        #       } else {
        #           $method = ( is_array( $method ) ) ? $method[$key] : $method;
        #           $ret = filter_var( $method, FILTER_SANITIZE_ENCODED, $options );
        #       }
        #       if ($ret === false) {
        #           return false;
        #       }

        return $ret;
    }

    /**
     * xo_Filters_Validate_String::checkOption()
     *
     * @param  mixed $options
     * @return array
     */
    public function checkOption($options = [])
    {
        return $options = ['options' => $options];
    }
}
