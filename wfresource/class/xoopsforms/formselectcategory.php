<?php
/**
 * Name: formselectcategory.php
 * Description:
 *
 * @package   : Xoosla Modules
 * @Module    :
 * @subpackage:
 * @since     : v1.0.0
 * @author    John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license   : GNU/LGPL, see docs/license.php
 * @version   : $Id: formselectcategory.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';

/**
 * A select field with a choice of available users
 *
 * @package    kernel
 * @subpackage form
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
     * @param bool   $include_anon Include user "anonymous"?
     * @param mixed  $value        Pre-selected value (or array of them).
     * @param int    $size         Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple     Allow multiple selections?
     */
    public function __construct($caption, $name, $value = null, $size = 1, $multiple = false)
    {
        $this->XoopsFormSelect($caption, $name, $value, $size, $multiple);
        $category_handler = &wfp_gethandler('category', 'wfsection', 'wfs_');
        $categorys        = $category_handler->getList();
        $this->addOptionArray($categorys);
    }
}
