<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeInt.php
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
 * SanitizeInt
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeInt extends Wfresource\Request
{
    /**
     * SanitizeInt::render()
     *
     * @param             $method
     * @param             $key
     * @param array       $options
     * @return bool|mixed
     */
    public function doRender($method, $key, $options = [])
    {
        $this->checkOption($options);
        if (!empty($method) && \is_int($method)) {
            $ret = \filter_input($method, $key, \FILTER_SANITIZE_NUMBER_INT, $options);
        } else {
            $method = (\is_array($method) && isset($method[$key])) ? $method[$key] : $method;
            $ret    = \filter_var($method, \FILTER_SANITIZE_NUMBER_INT, $options);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }

    /**
     * xo_Filters_Validate_Int::checkOption()
     *
     * @param mixed $options
     */
    public function checkOption($options): void
    {
        if (\is_array($options) && (2 === \count($options))) {
            $options = ['options' => $options];
            if (!\array_key_exists('min_range', $options) && !\array_key_exists('max_range', $options)) {
                // trigger_error( "Value must be 1 or below" );
            }
        }
    }
}
