<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Filters;

/**
 * Name: SanitizeUrlencode.php
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
 * SanitizeUrlencode
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class SanitizeUrlencode extends Wfresource\Request
{
    /**
     * SanitizeUrlencode::render()
     *
     * @param             $method
     * @param             $key
     * @param array       $options
     * @return bool|mixed
     */
    public function doRender($method, $key, $options = [])
    {
        $options = $this->checkOption($options);
        if (\is_int($method)) {
            $ret = \filter_input($method, $key, \FILTER_SANITIZE_ENCODED, $options);
        } else {
            $method = \is_array($method) ? $method[$key] : $method;
            $ret    = \filter_var($method, \FILTER_SANITIZE_ENCODED, $options);
        }
        if (false === $ret) {
            return false;
        }

        return $ret;
    }

    /**
     * SanitizeUrlencode::checkOption()
     *
     * @param mixed $options
     */
    public function checkOption($options = []): array
    {
        return $options = ['options' => $options];
    }
}
