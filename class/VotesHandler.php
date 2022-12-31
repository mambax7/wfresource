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

use Criteria;
use CriteriaCompo;
use XoopsDatabase;
use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * VotesHandler
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class VotesHandler extends Wfresource\WfpObjectHandler
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
     * @param \XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'wfp_votes', Votes::class, 'vote_id', 'vote_mid');
    }

    /**
     * VotesHandler::getObj()
     * @return bool
     */
    public function getObj(...$args)
    {
        $obj = false;
        if (2 === \func_num_args()) {
//            $args     = \func_get_args();
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
     * VotesHandler::xo_AddVoteData()
     *
     * @param mixed $rating
     * @param mixed $aid
     */
    public function xo_AddVoteData($rating, &$aid): void
    {
        global $xoopsUser;

        $this->anonwaitdays = 1;
        $this->vote_mid     = 0;
        if (isset($GLOBALS['xoopsModule']) && $GLOBALS['xoopsModule']->getVar('mid') > 0) {
            $this->vote_mid = $GLOBALS['xoopsModule']->getVar('mid');
        }
        $this->vote_aid = 0;
        if (\Xmf\Request::hasVar('page_type', 'REQUEST') && 'content' === $_REQUEST['page_type']) {
            $this->vote_aid = \Xmf\Request::getInt('id', 0, 'REQUEST');
        }
        $this->vote_ipaddress = getip();
        $this->vote_aid       = $ratinguser = \is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
        $this->vote_rating    = (int)$rating;
    }

    public function xo_GetVoteData(): void
    {
    }

    public function xo_getRateAuthor(): void
    {
    }

    /**
     * VotesHandler::xo_getRatingUser()
     */
    public function xo_getRatingUser(): array
    {
        $yesterday = (\time() - (86400 * $this->anonwaitdays));

        $sql    = 'SELECT COUNT(*) ' . "\n WHERE vote_aid=" . $this->vote_aid . "\n AND ( vote_uid=" . $this->vote_uid . " OR ( vote_uid=0 AND vote_ipaddress='" . $this->vote_ipaddress . "')" . "\n AND vote_date >" . $yesterday;
        $result = $_this->db->query($sql);
        $ret    = [];
        if ($result) {
            $ret = $_this->db->fetchObject($result);
        }

        return $ret;
    }

    /**
     * VotesHandler::getModule()
     */
    public function getModule(): array
    {
        global $moduleHandler;
        static $_cachedModule_list;
        if (!empty($_cachedModule_list)) {
            $_module = &$_cachedModule_list;

            return $_module;
        }
        $module_list        = $moduleHandler->getList();
        $_cachedModule_list = &$module_list;

        return $module_list;
    }

    /**
     * PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count): void
    {
        global $list_array, $nav;

        $onchange = 'onchange=\'location="admin.votes.php?%s="+this.options[this.selectedIndex].value\'';
        $ret      = '<div style="padding-bottom: 16px;">';
        // $ret .= '<form><div style="text-align: left; margin-bottom: 12px;"><input type="button" name="button" onclick=\'location="admin.votes.php?op=edit"\' value="' . _MD_WFP_CREATENEW . '"></div></form>';
        $ret .= '<div>
            <span style="float: right;">' . \_AM_WFP_DISPLAYAMOUNT_BOX . Utility::getSelection($list_array, $nav['limit'], 'limit', 1, 0, false, false, \sprintf($onchange, 'limit'), 0, false) . '</span>
            </div>';
        $ret .= '</div><br clear="all">';
        echo $ret;
    }
}
