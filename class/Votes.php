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

use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * Votes
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Votes extends Wfresource\WfpObject
{
    private $vote_id;
    private $vote_mid;
    private $vote_aid;
    private $vote_aname;
    private $vote_uid;
    private $vote_uname;
    private $vote_rating;
    private $vote_ipaddress;
    private $vote_date;

    /**
     * Votes::Votes()
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('vote_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_mid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_aid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_aname', \XOBJ_DTYPE_TXTBOX, null, false, 250);
        $this->initVar('vote_uid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_uname', \XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('vote_rating', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_ipaddress', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('vote_date', \XOBJ_DTYPE_LTIME, \time(), false);
    }

    /**
     * Votes::getUser()
     * @return mixed|string
     */
    public function getUser()
    {
        if ($this->getVar('vote_uid') > 0) {
            return $this->getVar('vote_uname');
        }

        return 'Anon';
    }
}
