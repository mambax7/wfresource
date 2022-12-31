<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

// ------------------------------------------------------------------------ //
// wfp_ - PHP Content Management System                                 //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * Xoops Mimetype Class
 *
 * @author        John Neill AKA Catzwolf
 * @copyright (c) 2006 Xoops
 */
class Mimetype extends Wfresource\WfpObject
{
    private $mime_id;
    private $mime_ext;
    private $mime_name;
    private $mime_types;
    private $mime_images;
    private $mime_safe;
    private $mime_category;
    private $mime_display;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('mime_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('mime_ext', \XOBJ_DTYPE_TXTBOX, null, true, 10);
        $this->initVar('mime_name', \XOBJ_DTYPE_TXTBOX, null, true, 60);
        $this->initVar('mime_types', \XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('mime_images', \XOBJ_DTYPE_TXTBOX, null, true, 120);
        $this->initVar('mime_safe', \XOBJ_DTYPE_INT, 0, true);
        $this->initVar('mime_category', \XOBJ_DTYPE_TXTBOX, 'unknown', true, 10);
        $this->initVar('mime_display', \XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return (-1 === $this->getVar('mime_id'));
    }

    /**
     * @param $Handler
     * @return mixed
     */
    public function mimeCategory($Handler)
    {
        $haystack = &$Handler->mimeCategory();
        $needle   = $this->getVar('mime_category');

        return $haystack[$needle] ?? $haystack['unknown'];
    }

    public function mimeSafe(): string
    {
        $ret = $this->getVar('mime_safe') ? Utility::showImage('safe') : Utility::showImage('unsafe');

        return $ret;
    }
}
