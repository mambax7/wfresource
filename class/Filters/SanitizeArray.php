<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeArray.php
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
 * SanitizeArray
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeArray extends Wfresource\Request
{
    /**
     * SanitizeArray::render()
     *
     * @param         $method
     * @param         $key
     * @param array   $options
     */
    public function doRender($method, $key, $options = []): string
    {
        $ret = $method[$key] ?? '';
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
     * @param mixed $options
     */
    public function checkOption($options = []): array
    {
        return $options = ['options' => $options];
    }
}
