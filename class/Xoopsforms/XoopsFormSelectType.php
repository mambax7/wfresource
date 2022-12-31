<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

/**
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * Parent
 */

//require_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';

/**
 * A select field with countries
 *
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectType extends \XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param mixed  $value   Pre-selected value (or array of them).
     *                        Legal are all 2-letter country codes (in capitals).
     * @param int    $size    Number or rows. "1" makes a drop-down-list
     */
    public function __construct($caption, $name, $value = 'news', $size = 1)
    {
        $_menus = [
            'news'    => 'News',
            'review'  => 'Review',
            'article' => 'Article',
            'preview' => 'Preview',
            'blog'    => 'Blog',
            'static'  => 'Static',
            'faq'     => 'FAQ',
            'link'    => 'Links',
            'other'   => 'Other',
        ];
        // $_menus = array( 'news' => 'News', 'article' => 'Article', 'blog' => 'Blog', 'faq' => 'FAQ', 'other' => 'Other');
        $multiple = 0;
        parent::__construct($caption, $name, $value, $size, $multiple);
        $this->addOptionArray($_menus);
    }
}
