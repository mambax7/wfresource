<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.objecthandler.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use Criteria;
use CriteriaCompo;
use XoopsDatabaseFactory;
use XoopsModules\Wfresource\Calendar\DHTML_Calendar;


Utility::loadLanguage('errors', 'wfresource');

/**
 * WfpObjectHandler
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class WfpObjectHandler extends \XoopsObjectHandler
{
    /**
     * *#@+
     * Information about the class, the handler is managing
     *
     * @var string
     */
    public $db_table;
    public $obj_class;
    public $key_name;
    public $tkey_name;
    public $ckey_name;
    public $identifier_name;
    public $identifierName;
    public $user_groups;
    public $ckeyName;
    public $keyName;
    public $tkeyName;
    public $groupName;
    public $tableName;
    public $isAdmin;
    public $doPermissions;
    public $_errors = [];

    /**
     * @param            $db
     * @param string     $db_table
     * @param string     $obj_class
     * @param string     $key_name
     * @param bool|false $identifier_name
     * @param bool|false $group_name
     */
    public function __construct(
        $db,
        $db_table = '',
        $obj_class = '',
        $key_name = '',
        $identifier_name = false,
        $group_name = false
    ) {
        global $xoopsUserIsAdmin, $xoopsUser;

//        static $db;
        if (null === $db) {
            $db = XoopsDatabaseFactory::getDatabaseConnection();
        }

        parent::__construct($db);
        $this->db_table  = $db->prefix($db_table);
        $this->obj_class = $obj_class;
        // **//
        $this->identifierName = ($identifier_name) ?: '';
        $this->groupName      = ($group_name) ?: '';
        $this->user_groups    = \is_object($xoopsUser) ? $xoopsUser->getGroups() : [0 => XOOPS_GROUP_ANONYMOUS];
        $this->doPermissions  = ('' !== $this->groupName && !\in_array('1', $this->user_groups, true)) ? 1 : 0;
        $this->ckeyName       = $this->doPermissions ? 'c.' . $key_name : $key_name;
        $this->keyName        = $key_name;
        $this->tkeyName       = null;
    }

    /**
     * @param \XoopsDatabase|null $db
     * @return mixed
     */
    public function getInstance($db)
    {
        static $instance;
        if (null === $instance) {
            $_class   = $this->obj_class . 'Handler';
            $instance = new $_class($db);
        }

        return $instance;
    }

    /**
     * @param bool|true $value
     */
    public function setPermission($value = true): void
    {
        $this->doPermissions = $value;
    }

    /**
     * @param $value
     */
    public function setTempKeyName($value): void
    {
        $this->tkey_name = $value;
    }

    /**
     * @return mixed
     */
    public function getTempKeyName()
    {
        return $this->tkey_name;
    }

    public function unsetTempKeyName(): void
    {
        unset($this->tkey_name);
    }

    /**
     * @param bool|true $isNew
     * @return bool|\XoopsObject
     */
    public function create($isNew = true)
    {
        $obj = new $this->obj_class();
        if (!\is_object($obj)) {
            \trigger_error(\get_class($this->obj_class) . ' is not an object', \E_USER_ERROR);

            return false;
        }
        if (true === $isNew) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * WfpObjectHandler::get()
     *
     * @param string|int $id
     * @param mixed      $as_object
     * @param string     $keyName
     * @return bool|void
     */
    public function get($id = '', $as_object = true, $keyName = '')
    {
        $id       = (int)$id;
        $ret      = false;
        $criteria = new CriteriaCompo();
        if (\is_array($this->keyName)) {
            $arrayCount = \count($this->keyName);
            for ($i = 0; $i < $arrayCount; ++$i) {
                $criteria->add(new Criteria($this->keyName[$i], $id[$i]));
            }
        } else {
            if ($id > 0) {
                $criteria = new Criteria($this->ckeyName, $id);
            } else {
                $criteria = new Criteria((string)$keyName, 1);
            }
        }
        $criteria->setLimit(1);
        $obj_array = $this->getObjects($criteria, false, $as_object);
        if (!\is_array($obj_array) || 1 > \count($obj_array)) {
            $this->setErrors(\_MD_WFP_ERROR_GET_ITEM);

            return false;
        }

        return $obj_array[0];
    }

    /**
     * WfpObjectHandler::getObjects()
     *
     * @param mixed $criteria
     * @param mixed $id_as_key
     * @param mixed $as_object
     * @param mixed $return_error
     * @return array|bool
     */
    public function getObjects($criteria = null, $id_as_key = false, $as_object = true, $return_error = false)
    {
        $ret   = [];
        $limit = $start = 0;
        if ($this->doPermissions) {
            $sql = 'SELECT DISTINCT c.* FROM ' . $this->db_table . ' c  LEFT JOIN ' . $this->db->prefix('group_permission') . " l   ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . \implode(',', $this->user_groups) . ' )   )';
        } else {
            $sql = 'SELECT * FROM ' . $this->db_table;
        }
         if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            if ($this->doPermissions) {
                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if ('' !== $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            \trigger_error($this->db->errno() . ' ' . $this->db->error(), \E_USER_WARNING);
            $this->setErrors($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        }
        $result = $this->convertResultSet($result, $id_as_key, $as_object);

        return $result;
    }

    /**
     * WfpObjectHandler::convertResultSet()
     *
     * @param mixed $result
     * @param mixed $id_as_key
     * @param mixed $as_object
     * @return array
     */
    public function convertResultSet($result, $id_as_key = false, $as_object = true)
    {
        $ret = [];
        if ($this->db->isResultSet($result)) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $obj = $this->create(false);
                if (!$obj) {
                    \trigger_error(\_MD_WFP_ERROR_CREATE_NEW_OBJECT, \E_USER_WARNING);

                    return false;
                }
                $obj->assignVars($myrow);
                if (!$id_as_key) {
                    if ($as_object) {
                        $ret[] = &$obj;
                    } else {
                        $row  = [];
                        $vars = $obj->getVars();
                        foreach (\array_keys($vars) as $i) {
                            $row[$i] = $obj->getVar($i);
                        }
                        $ret[] = $row;
                    }
                } else {
                    if ($as_object) {
                        $ret[$myrow[$this->keyName]] = &$obj;
                    } else {
                        $row  = [];
                        $vars = $obj->getVars();
                        foreach (\array_keys($vars) as $i) {
                            $row[$i] = $obj->getVar($i);
                        }
                        $ret[$myrow[$this->keyName]] = $row;
                    }
                }
                unset($obj);
            }
        }

        return $ret;
    }

    /**
     * WfpObjectHandler::getList()
     *
     * @param mixed  $criteria
     * @param string $querie
     * @param mixed  $show
     * @param mixed  $doCriteria
     * @return array|bool
     */
    public function getList($criteria = null, $querie = '*', $show = null, $doCriteria = true)
    {
        $ret   = [];
        $limit = $start = 0;
        if ($this->doPermissions) {
            if ($querie) {
                $query = $querie;
            } else {
                $query = $this->ckeyName;
                if (!empty($this->identifierName)) {
                    $query .= ', c.' . $this->identifierName;
                }
            }
            $sql = 'SELECT DISTINCT c.* FROM ' . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix('group_permission') . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . \implode(',', $this->user_groups) . ' ))';
        } else {
            if ($querie) {
                $query = $querie;
            } else {
                $query = $this->ckeyName;
                if (!empty($this->identifierName)) {
                    $query .= ', ' . $this->identifierName;
                }
            }
            $sql = 'SELECT ' . $query . ' FROM ' . $this->db_table;
        }
        if (false !== $doCriteria) {
            if (null === $criteria) {
                $criteria = new CriteriaCompo();
            }
            if ('' === $criteria->getSort()) {
                $criteria->setSort($this->identifierName);
            }
            if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
                if ($this->doPermissions) {
//                    $sql .= ' AND ' . $criteria->render();
                } else {
                    $sql .= ' ' . $criteria->renderWhere();
                }
                if ('' !== $criteria->getSort()) {
                    $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
        }
        if (!$result = $this->db->query($sql, $limit, $start)) {
            $this->setErrors($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($this->getTempKeyName()) {
                $ret[$myrow[$this->tkey_name]] = empty($this->identifierName) ? '' : \htmlspecialchars($myrow[$this->identifierName], \ENT_QUOTES);
            } else {
                $ret[$myrow[$this->keyName]] = empty($this->identifierName) ? '' : \htmlspecialchars($myrow[$this->identifierName], \ENT_QUOTES);
            }
        }
        $this->unsetTempKeyName();

        return $ret;
    }

    /**
     * WfpObjectHandler::getCount()
     *
     * @param mixed  $criteria
     * @param string $querie
     * @return bool|array|int
     */
    public function getCount($criteria = null, $querie = '*')
    {
        if ($this->doPermissions) {
            $sql = "SELECT {$querie} FROM " . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix('group_permission') . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . \implode(',', $this->user_groups) . ' ) )';
        } else {
            $sql = "SELECT {$querie} FROM " . $this->db_table;
        }
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            if ($this->doPermissions) {
//                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
        }
        $result = $this->db->query($sql);

        if (!$this->db->isResultSet($result)) {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $this->db->error(), \E_USER_ERROR);
        }


//        if (!$result = $this->db->query($sql)) {
//            \trigger_error($this->db->errno() . ' ' . $this->db->error() . ' ' . __FILE__ . ' ' . __LINE__);
//
//            return false;
//        }
        if ('*' !== $querie) {
            return $this->db->fetchArray($result);
        }
        $count = $this->db->getRowsNum($result);

        return $count;
    }

    public function isId(): string
    {
        echo 'hello';
        $a = '212121';

        return $a;
    }

    /**
     * WfpObjectHandler::insert()
     *
     * @param mixed $checkObject
     * @param mixed $andclause
     * @param mixed $force
     * @return bool
     */
    public function insert(
        \XoopsObject $object,
        $force = true,
        $checkObject = true,
        $andclause = null
    ) { // insert(&$object, $checkObject = true, $andclause = null, $force = false)
        if (true === $checkObject) {
            if (!\is_object($object) || !\is_a($object, $this->obj_class)) {
                $this->setErrors(\_MD_WFP_ISNOTOBEJECT);

                return false;
            }
            if (!$object->isDirty()) {
                $this->setErrors(\_MD_WFP_ISNOTOBEJECTDIRTY . '<br>' . $object->getErrors());

                return false;
            }
        }
        if (!$object->cleanVars()) {
            $this->setErrors($object->getErrors());

            return false;
        }
        if ($object->isNew()) {
            $object->cleanVars[$this->keyName] = '';
            foreach ($object->cleanVars as $k => $v) {
                $cleanvars[$k] = (\XOBJ_DTYPE_INT == $object->vars[$k]['data_type']) ? (int)$v : $this->db->quoteString($v);
            }
            $sql = 'INSERT INTO ' . $this->db_table . ' (`' . \implode('`, `', \array_keys($cleanvars)) . '`) VALUES (' . \implode(',', \array_values($cleanvars)) . ')';
        } else {
            $sql = 'UPDATE ' . $this->db_table . ' SET';
            foreach ($object->cleanVars as $k => $v) {
                if (null !== $notfirst) {
                    $sql .= ', ';
                }
                if (\XOBJ_DTYPE_INT === $object->vars[$k]['data_type']) {
                    $sql .= ' ' . $k . ' = ' . (int)$v;
                } else {
                    $sql .= ' ' . $k . ' = ' . $this->db->quoteString($v);
                }
                $notfirst = true;
            }
            $sql .= ' WHERE ' . $this->keyName . " = '" . $object->getVar($this->keyName) . "'";
            if ($andclause) {
                $sql .= $andclause;
            }
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        // echo $sql.'<br>';
        if (!$result) {
            \trigger_error($this->db->errno() . ' ' . $this->db->error(), \E_USER_ERROR);
            $this->setErrors($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        }
        if ($object->isNew() && !\is_array($this->keyName)) {
            $object->assignVar($this->keyName, $this->db->getInsertId());
        }

        return true;
    }

    /**
     * WfpObjectHandler::updateAll()
     *
     * @param array|string|int $fieldname
     * @param int              $fieldvalue
     * @param mixed            $criteria
     * @param mixed            $force
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue = 0, $criteria = null, $force = true)
    {
        if (\is_array($fieldname) && 0 === $fieldvalue) {
            $set_clause = '';
            foreach ($fieldname as $key => $value) {
                if (null !== $notfirst) {
                    $set_clause .= ', ';
                }
                $set_clause .= \is_numeric($key) ? ' ' . $key . ' = ' . $value : ' ' . $key . ' = ' . $this->db->quoteString($value);
                $notfirst   = true;
            }
        } else {
            $set_clause = \is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        }
        $sql = 'UPDATE ' . $this->db_table . ' SET ' . $set_clause;

        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(\sprintf(\_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * WfpObjectHandler::delete()
     *
     * @param int|\XoopsObject $object
     * @param mixed            $force
     * @return bool
     */
    public function delete(\XoopsObject $object, $force = false)
    {
        if (!\is_object($object) || !\is_a($object, $this->obj_class)) {
            $this->setErrors(\sprintf(\_MD_WFP_ERROR_DELETE, \basename(__FILE__), __LINE__));

            return false;
        }
        if (\is_array($this->keyName)) {
            $clause     = [];
            $arrayCount = \count($this->keyName);
            for ($i = 0; $i < $arrayCount; ++$i) {
                $clause[] = $this->keyName[$i] . ' = ' . $object->getVar($this->keyName[$i]);
            }
            $whereclause = \implode(' AND ', $clause);
        } else {
            $whereclause = $this->keyName . ' = ' . $object->getVar($this->keyName);
        }
        $sql = 'DELETE FROM ' . $this->db_table . ' WHERE ' . $whereclause;
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(\sprintf(\_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * WfpObjectHandler::updateCounter()
     *
     * @param mixed $fieldname
     * @param mixed $criteria
     * @param mixed $force
     * @return bool
     */
    public function updateCounter($fieldname, $criteria = null, $force = true)
    {
        $set_clause = $fieldname . '=' . $fieldname . '+1';
        $sql        = 'UPDATE ' . $this->db_table . ' SET ' . $set_clause;
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(\sprintf(\_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * WfpObjectHandler::getaDate()
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
     * RefersHandler::showHtmlCalendar()
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
        //        $value  = ('' === $date) ? '' : \strftime('%Y-%m-%d %I:%M', $date);

        $format  = 'Y-m-d I:M';
        $dateObj = new \DateTime();
        $dateObj->setTimestamp((int)$date);
        $value = ('' === $date) ? '' : $dateObj->format($format);

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
            ['style' => '', 'name' => 'date', 'value' => $value],
            $display
        );
    }

    /**
     * WfpObjectHandler::displayCalendar()
     * @return string
     */
    public function displayCalendar(...$args)
    {
        $ret = '';
        if (2 !== \func_num_args()) {
            return $ret;
        }
        $nav     = \func_get_arg(0);
        $display = \func_get_arg(1);

        $op       = Request::doRequest($_REQUEST, 'op', 'default', 'textbox');
        $onchange = 'onchange=\'location="' . \basename($_SERVER['SCRIPT_FILENAME']) . '?op=' . $op . '&amp;%s="+this.options[this.selectedIndex].value\'';
        $ret      .= '<form id="calendar" method="post">';
        $ret      .= '<div id="wrapper" style="padding-bottom: 8px;">';
        $ret      .= '<div style="float: left;">' . $this->showHtmlCalendar(false, $nav['date']);
        $ret      .= '<input type="text" name="search" id="search" size="20" maxlength="255" value="' . Utility::stripslashes($nav['search']) . '">&nbsp;';
        $ret      .= Utility::getSelection(Utility::listAndOr(), $nav['andor'], 'andor', 1, 0, false, false, '', 0, false) . '&nbsp;';
        $ret      .= '<input align="left" type="submit" class="formbutton" value="' . \_AM_WFP_SEARCH . '" name="selsubmit"></div>';
        $ret      .= '<div style="float: right;">';
        if ($display) {
            $ret .= \_AM_WFC_DISPLAYPUBLISHED . Utility::getSelection(Utility::listPages(), $nav['active'], 'active', 1, 0, false, false, \sprintf($onchange, 'active'), 0, false) . '&nbsp;';
        }
        $ret .= \_AM_WFC_DISPLAYAMOUNT_BOX . Utility::getSelection(Utility::listArray(), $nav['limit'], 'limit', 1, 0, false, false, \sprintf($onchange, 'limit'), 0, false);
        $ret .= '</div>';
        $ret .= '</div><br clear="all">';
        $ret .= '</form>';
        echo $ret;
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    /**
     * add an error
     *
     * @param $err_str
     * @internal param string $value error to add
     */
    public function setErrors($err_str): void
    {
        if (\is_array($err_str)) {
            foreach ($err_str as $error) {
                $this->_errors[] = \trim($error);
            }
        } else {
            $this->_errors[] = \trim($err_str);
        }
    }

    /**
     * WfpObjectHandler::getHtmlErrors()
     *
     * @param bool $return
     * @param int  $menu
     */
    public function getHtmlErrors($return = false, $menu = 0): string
    {
        global $menuHandler;

        $ret = '';
        if (0 !== \count($this->_errors)) {
            foreach ($this->_errors as $error) {
                $ret .= $error . '<br>';
            }
        }
        if (!$return) {
            \xoops_cp_header();
            $menuHandler->addSubHeader(\_MD_WFP_ERRORS);
            $menuHandler->render($menu);
            echo $ret;
            \xoosla_cp_footer();
            exit();
        }

        return $ret;
    }
}
