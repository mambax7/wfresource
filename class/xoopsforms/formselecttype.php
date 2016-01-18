<?php
// $Id: formselecttype.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                      			//
// Copyright (c) 2007 Xoops                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.xoops.com 												//
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
/**
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH . "/class/xoopsform/formselect.php";

/**
 * A select field with countries
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectType extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param mixed  $value   Pre-selected value (or array of them).
     *                        Legal are all 2-letter country codes (in capitals).
     * @param int    $size    Number or rows. "1" makes a drop-down-list
     * @param string $handler Handler to use to get the list
     * @param string $module  Dirname of module - defaults to current module
     */
    public function __construct($caption, $name, $value = 'news', $size = 1)
    {
        $_menus = array('news' => 'News', 'review' => 'Review', 'article' => 'Article', 'preview' => 'Preview', 'blog' => 'Blog', 'static' => 'Static', 'faq' => 'FAQ', 'link' => 'Links', 'other' => 'Other');
        // $_menus = array( 'news' => 'News', 'article' => 'Article', 'blog' => 'Blog', 'faq' => 'FAQ', 'other' => 'Other');
        $multiple = 0;
        parent::__construct($caption, $name, $value, $size, $multiple);
        $this->addOptionArray($_menus);
    }
}
