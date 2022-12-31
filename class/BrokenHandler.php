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
 * BrokenHandler
 *
 * @author    John
 * @copyright Copyright (c) 2008
 */
class BrokenHandler extends Wfresource\WfpObjectHandler
{
    /**
     * BrokenHandler::BrokenHandler()
     *
     * @param mixed $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'wfp_broken', Broken::class, 'broken_id', 'broken_fid');
    }

    /**
     * BrokenHandler::getObj()
     *
     * @param array $nav
     * @param mixed $value
     * @return bool
     */
    public function &getObj(...$args)
    {
        $obj = false;
        if (2 == \func_num_args()) {
//            $args     = \func_get_args();
            $criteria = new \CriteriaCompo();
            if (!empty($args[0]['pulldate'])) {
                $addon_date = $this->getaDate($args[0]['broken_date']);
                if ($addon_date['begin'] && $addon_date['end']) {
                    $criteria->add(new \Criteria('broken_date', $addon_date['begin'], '>='));
                    $criteria->add(new \Criteria('broken_date', $addon_date['end'], '<='));
                }
            }
            if (isset($args[0]['broken_uid']) and $args[0]['broken_uid'] > 0) {
                $criteria->add(new \Criteria('broken_uid', $args[0]['broken_uid'], '='));
            }
            if ($GLOBALS['xoopsModule']->getVar('mid')) {
                $criteria->add(new \Criteria('broken_mid', $GLOBALS['xoopsModule']->getVar('mid')));
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
     * BrokenHandler::getaDate()
     *
     * @param string $exp_value
     * @param string $exp_time
     * @param mixed  $useMonth
     * @return array
     */
    public function getaDate($exp_value = '', $exp_time = '', $useMonth = 0)
    {
        $_date_arr = [];
        $_date     = $exp_value ?: \time();
        $d         = \date('j', $_date);
        $m         = \date('m', $_date);
        $y         = \date('Y', $_date);
        if ($useMonth > 0) {
            /**
             * We use +1 for the the previous month and not the next here,
             * if the day var is set to 0 ( You would have thought a neg value would have been correct here but nope!
             * Bloody strange way of doing it if you ask me! :-/ )
             */
            $_date_arr['begin'] = \mktime(0, 0, 0, $m, 1, $y);
            $_date_arr['end']   = \mktime(0, 0, 0, $m + 1, 0, $y);
        } else {
            /**
             * 86400 = 1 day, while 86399 = 23 hours and 59 mins and 59 secs
             */
            $_date_arr['begin'] = \mktime(0, 0, 0, $m, $d, $y);
            $_date_arr['end']   = \mktime(23, 59, 59, $m, $d, $y);
        }

        return $_date_arr;
    }

    /**
     * @return mixed
     */
    private function &getUserInfo()
    {
        $sql    = \sprintf(
            '
         SELECT DISTINCT
             users.uid, users.uname
         FROM
             %s AS users, %s AS broken
         WHERE
             users.uid = broken.broken_uid
             AND broken.broken_mid = %u
         ORDER
             BY users.uname',
            $this->db->prefix('users'),
            $this->db->prefix('wfp_broken'),
            $GLOBALS['xoopsModule']->getVar('mid')
        );
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['uid']] = $myrow['uname'];
        }
        $this->db->freeRecordSet($result);

        return $ret;
    }

    /**
     * @return mixed
     */
    private function &getFileInfo()
    {
        $sql    = \sprintf(
            '
         SELECT DISTINCT
             files.file_id, files.file_displayname
         FROM
             %s AS files, %s AS broken
         WHERE
             files.file_id = broken.broken_fid
         ORDER
             BY files.file_displayname',
            $this->db->prefix('wfp_files'),
            $this->db->prefix('wfp_broken')
        );
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['file_id']] = $myrow['file_displayname'];
        }
        $this->db->freeRecordSet($result);

        return $ret;
    }

    /**
     * @return mixed
     */
    public function &getUserList()
    {
        static $user_array;
        if (null === $user_array) {
            $data       = &$this->getUserInfo();
            $user_array = &$data;
        }

        return $user_array;
    }

    /**
     * @return mixed
     */
    public function &getFilesList()
    {
        static $file_array;
        if (null === $file_array) {
            $data       = &$this->getFileInfo();
            $file_array = &$data;
        }

        return $file_array;
    }

    /**
     * @return null|string|void
     */
    public function showHtmlCalendar(...$args)
    {
        if (2 !== \func_num_args()) {
            return null;
        }
        $display = \func_get_arg(0);
        $date    = \func_get_arg(1);

        $jstime = \formatTimestamp('F j Y, H:i:s', \time());
        $value  = ('' === $date) ? '' : \strftime('%Y-%m-%d %I:%M', $date);
        //        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/calendar/calendar.php';
        $calendar = new DHTML_Calendar(XOOPS_URL . '/modules/wfresource/class/calendar/', 'en', 'calendar-system', false);
        $calendar->load_files();

        return $calendar->make_input_field(
            [
                'firstDay'   => 1,
                'showsTime'  => true,
                'showOthers' => true,
                'ifFormat'   => '%Y-%m-%d %I:%M',
                'timeFormat' => '12',
            ], // field attributes go here
            ['style' => '', 'name' => 'date1', 'value' => $value],
            $display
        );
    }

    /**
     * PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count): void
    {
        /**
         * bad bad bad!! Need to change this
         */ global $list_array, $nav, $broken_authors;

        $onchange = 'onchange=\'location="admin.broken.php?%s="+this.options[this.selectedIndex].value\'';
        $ret      = '<div style="padding-bottom: 16px;">';
        $ret      .= '<form><div style="text-align: left; margin-bottom: 12px;"><input type="button" name="button" onclick=\'location="admin.broken.php?op=edit"\' value="' . _MD_WFP_CREATENEW . '"></div></form>';
        $ret      .= '<div>
            <span style="float: left;">' . Utility::getSelection($broken_authors, $nav['broken_uid'], 'broken_uid', 1, 1, false, false, \sprintf($onchange, 'broken_uid'), 0, false) . '</span>
            <span style="float: right;">' . \_AM_WFP_DISPLAYAMOUNT_BOX . Utility::getSelection($list_array, $nav['limit'], 'limit', 1, 0, false, false, \sprintf($onchange, 'limit'), 0, false) . '</span>
            </div>';
        $ret      .= '</div><br clear="all">';
        echo $ret;
    }
}
