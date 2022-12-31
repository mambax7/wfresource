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
// URL: http:www.Xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * Broken
 *
 * @author    John
 * @copyright Copyright (c) 2008
 */
class Broken extends Wfresource\WfpObject
{
    private $broken_id;
    private $broken_fid;
    private $broken_mid;
    private $broken_uid;
    private $broken_ip;
    private $broken_date;
    private $broken_confirmed;
    private $broken_acknowledged;

    /**
     * Broken::Broken()
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('broken_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_fid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_mid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_uid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_ip', \XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('broken_date', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_confirmed', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('broken_acknowledged', \XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Broken::getUser()
     *
     * @param mixed $value
     */
    public function getUser($value = null): string
    {
        $uid = $this->getVar('broken_uid');
        $ret = 'Unknown Author';
        if (\is_array($value) && isset($value[$uid])) {
            $ret = $value[$uid];
        }

        return $ret;
    }

    /**
     * @param null|array $value
     */
    public function getFiles($value = null): string
    {
        $file = $this->getVar('broken_fid');
        $ret  = 'Unknown File';
        if (\is_array($value) && isset($value[$file])) {
            $ret = $value[$file];
        }

        return $ret;
    }
}
