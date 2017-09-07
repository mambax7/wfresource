<?php
// $Id: class.votes.php 8181 2011-11-07 01:14:53Z beckmi $
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
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');
wfp_getObjectHandler();

/**
 * wfp_Votes
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_Votes extends wfp_Object
{
    /**
     * wfp_Votes::wfp_Votes()
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('vote_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_mid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_aid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_aname', XOBJ_DTYPE_TXTBOX, null, false, 250);
        $this->initVar('vote_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('vote_uname', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('vote_rating', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_ipaddress', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('vote_date', XOBJ_DTYPE_LTIME, time(), false);
    }

    /**
     * wfp_Votes::getUser()
     * @return mixed|string
     */
    public function getUser()
    {
        if ($this->getVar('vote_uid') > 0) {
            return $this->getVar('vote_uname');
        } else {
            return 'Anon';
        }
    }
}

/**
 * wfp_VotesHandler
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_VotesHandler extends wfp_ObjectHandler
{
    public $vote_mid;
    public $vote_aid;
    public $vote_uid;
    public $vote_rating;
    public $vote_ipaddress;
    /**
     * days to wait to allow new vote
     */
    public $anonwaitdays;

    /**
     * Constructor
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'wfp_votes', 'wfp_Votes', 'vote_id', 'vote_mid');
    }

    /**
     * wfp_VotesHandler::getObj()
     * @return bool
     */
    public function getObj()
    {
        $obj = false;
        if (func_num_args() === 2) {
            $args     = func_get_args();
            $criteria = new CriteriaCompo();
            if ($GLOBALS['xoopsModule']->getVar('mid')) {
                $criteria->add(new Criteria('vote_mid', $GLOBALS['xoopsModule']->getVar('mid')));
            }
            $obj['count'] = $this->getCount($criteria);
            if (!empty($args[0])) {
                $criteria->setSort($args[0]['sort']);
                $criteria->setOrder($args[0]['order']);
                $criteria->setStart($args[0]['start']);
                $criteria->setLimit($args[0]['limit']);
            }
            $obj['list'] = $this->getObjects($criteria, $args[1]);
        }

        return $obj;
    }

    /**
     * wfp_VotesHandler::xo_AddVoteData()
     *
     * @param mixed $rating
     * @param mixed $aid
     */
    public function xo_AddVoteData(&$rating, &$aid)
    {
        global $xoopsUser;

        $this->anonwaitdays = 1;
        $this->vote_mid     = 0;
        if (isset($GLOBALS['xoopsModule']) && $GLOBALS['xoopsModule']->getVar('mid') > 0) {
            $this->vote_mid = $GLOBALS['xoopsModule']->getVar('mid');
        }
        $this->vote_aid = 0;
        if (isset($_REQUEST['page_type']) && $_REQUEST['page_type'] === 'content') {
            $this->vote_aid = (int)$_REQUEST['id'];
        }
        $this->vote_ipaddress = getip();
        $this->vote_aid       = $ratinguser = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
        $this->vote_rating    = (int)$rating;
    }

    public function xo_GetVoteData()
    {
    }

    public function xo_getRateAuthor()
    {
    }

    /**
     * wfp_VotesHandler::xo_getRatingUser()
     * @return array
     */
    public function xo_getRatingUser()
    {
        $yesterday = (time() - (86400 * $this->anonwaitdays));

        $sql    = 'SELECT COUNT(*) ' . "\n WHERE vote_aid=" . $this->vote_aid . "\n AND ( vote_uid=" . $this->vote_uid . " OR ( vote_uid=0 AND vote_ipaddress='" . $this->vote_ipaddress . "')" . "\n AND vote_date >" . (int)$yesterday;
        $result = $_this->db->query($sql);
        $ret    = [];
        if ($result) {
            $ret = $_this->db->fetchObject($result);
        }

        return $ret;
    }

    /**
     * wfp_VotesHandler::getModule()
     * @return array
     */
    public function getModule()
    {
        global $moduleHandler;
        static $_cachedModule_list;
        if (!empty($_cachedModule_list)) {
            $_module =& $_cachedModule_list;

            return $_module;
        } else {
            $module_list        =& $moduleHandler->getList();
            $_cachedModule_list =& $module_list;

            return $module_list;
        }
    }

    /**
     * wfc_PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count)
    {
        global $list_array, $nav;
        /**
         */

        $onchange = 'onchange=\'location="admin.votes.php?%s="+this.options[this.selectedIndex].value\'';
        $ret      = '<div style="padding-bottom: 16px;">';
        // $ret .= '<form><div style="text-align: left; margin-bottom: 12px;"><input type="button" name="button" onclick=\'location="admin.votes.php?op=edit"\' value="' . _MD_WFP_CREATENEW . '"></div></form>';
        $ret .= '<div>
            <span style="float: right;">' . _AM_WFP_DISPLAYAMOUNT_BOX . wfp_getSelection($list_array, $nav['limit'], 'limit', 1, 0, false, false, sprintf($onchange, 'limit'), 0, false) . '</span>
            </div>';
        $ret .= '</div><br clear="all">';
        echo $ret;
    }
}
