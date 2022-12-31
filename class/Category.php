<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

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

//wfp_getObjectHandler();

/**
 * Category
 *
 * @author    John
 * @copyright Copyright (c) 2006
 */
class Category extends Wfresource\WfpObject
{
    private $category_id;
    private $category_pid;
    private $category_mid;
    private $category_title;
    private $category_description;
    private $category_image;
    private $category_weight;
    private $category_display;
    private $category_published;
    private $category_imageside;
    private $category_type;
    private $category_header;
    private $category_footer;
    private $category_body;
    private $category_folders;
    private $category_metatitle;
    private $category_meta;
    private $category_keywords;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('category_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_pid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_mid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_title', \XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('category_description', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_image', \XOBJ_DTYPE_TXTBOX, '', false, 150);
        $this->initVar('category_weight', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('category_display', \XOBJ_DTYPE_INT, 1, false);
        $this->initVar('category_published', \XOBJ_DTYPE_INT, \time(), false);
        $this->initVar('category_imageside', \XOBJ_DTYPE_OTHER, 'left', false);
        $this->initVar('category_type', \XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('category_header', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_footer', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_body', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_folders', \XOBJ_DTYPE_ARRAY, null, false, null);
        $this->initVar('category_metatitle', \XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('category_meta', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_keywords', \XOBJ_DTYPE_TXTAREA, null, false, null, 1);
    }
}
