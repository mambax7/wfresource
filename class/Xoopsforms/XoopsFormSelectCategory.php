<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: formselectcategory.php
 * Description:
 *
 * @Module    :
 * @subpackage:
 * @since     : v1.0.0
 * @author    John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license   : GNU/LGPL, see docs/license.php
 */

//require_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';

/**
 * A select field with a choice of available users
 *
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectCategory extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple Allow multiple selections?
     */
    public function __construct($caption, $name, $value = null, $size = 1, $multiple = false)
    {
        parent::__construct($caption, $name, $value, $size, $multiple);
        $categoryHandler = Utility::getHandler('category', 'wfsection', 'wfs_');
        $categorys       = $categoryHandler->getList();
        $this->addOptionArray($categorys);
    }
}
