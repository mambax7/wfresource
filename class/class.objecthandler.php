<?php
/**
 * Name: class.objecthandler.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

wfp_loadLangauge('errors', 'wfresource');

/**
 * wfp_ObjectHandler
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_ObjectHandler extends XoopsObjectHandler
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
    public $groupName;
    public $tableName;
    public $isAdmin;
    public $doPermissions;
    public $_errors = array();

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

        static $db;
        if (null === $db) {
            $db = XoopsDatabaseFactory::getDatabaseConnection();
        }

        parent::__construct($db);
        $this->db_table  = $db->prefix($db_table);
        $this->obj_class = $obj_class;
        // **//
        $this->identifierName = ($identifier_name !== false) ? $identifier_name : '';
        $this->groupName      = ($group_name !== false) ? $group_name : '';
        $this->user_groups    = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);
        $this->doPermissions  = ($this->groupName !== '' && !in_array(1, $this->user_groups)) ? 1 : 0;
        $this->ckeyName       = $this->doPermissions ? 'c.' . $key_name : $key_name;
        $this->keyName        = $key_name;
        $this->tkeyName       = null;
    }

    /**
     * @param $db
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
    public function setPermission($value = true)
    {
        $this->doPermissions = $value;
    }

    /**
     * @param $value
     */
    public function setTempKeyName($value)
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

    public function unsetTempKeyName()
    {
        unset($this->tkey_name);
    }

    /**
     * @param  bool|true $isNew
     * @return bool|XoopsObject
     */
    public function create($isNew = true)
    {
        $obj = new $this->obj_class();
        if (!is_object($obj)) {
            trigger_error(get_class($this->obj_class) . ' is not an object', E_USER_ERROR);

            return false;
        } else {
            if ($isNew === true) {
                $obj->setNew();
            }

            return $obj;
        }
    }

    /**
     * wfp_ObjectHandler::get()
     *
     * @param  string $id
     * @param  mixed  $as_object
     * @param  string $keyName
     * @return bool|void
     */
    public function get($id = '', $as_object = true, $keyName = '')
    {
        $id       = (int)$id;
        $ret      = false;
        $criteria = new CriteriaCompo();
        if (is_array($this->keyName)) {
            $arrayCount = count($this->keyName);
            for ($i = 0; $i < $arrayCount; ++$i) {
                $criteria->add(new Criteria($this->keyName[$i], $id[$i]));
            }
        } else {
            if ($id > 0) {
                $criteria = new Criteria($this->ckeyName, $id);
            } else {
                $criteria = new Criteria("$keyName", 1);
            }
        }
        $criteria->setLimit(1);
        $obj_array = $this->getObjects($criteria, false, $as_object);
        if (!is_array($obj_array) || count($obj_array) !== 1) {
            $this->setErrors(_MD_WFP_ERROR_GET_ITEM);

            return false;
        }

        return $obj_array[0];
    }

    /**
     * wfp_ObjectHandler::getObjects()
     *
     * @param  mixed $criteria
     * @param  mixed $id_as_key
     * @param  mixed $as_object
     * @param  mixed $return_error
     * @return array|bool
     */
    public function getObjects($criteria = null, $id_as_key = false, $as_object = true, $return_error = false)
    {
        $ret   = array();
        $limit = $start = 0;
        if ($this->doPermissions) {
            $sql = 'SELECT DISTINCT c.* FROM ' . $this->db_table . ' c  LEFT JOIN ' . $this->db->prefix('group_permission') . " l   ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( "
                   . implode(',', $this->user_groups) . ' )   )';
        } else {
            $sql = 'SELECT * FROM ' . $this->db_table;
        }
        if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
            if ($this->doPermissions) {
                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if ($criteria->getSort() !== '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            trigger_error($this->db->errno() . ' ' . $this->db->error(), E_USER_WARNING);
            $this->setErrors($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        } else {
            $result =& $this->convertResultSet($result, $id_as_key, $as_object);

            return $result;
        }
    }

    /**
     * wfp_ObjectHandler::convertResultSet()
     *
     * @param  mixed $result
     * @param  mixed $id_as_key
     * @param  mixed $as_object
     * @return array
     */
    public function &convertResultSet($result, $id_as_key = false, $as_object = true)
    {
        $ret = array();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $obj = $this->create(false);
            if (!$obj) {
                trigger_error(_MD_WFP_ERROR_CREATE_NEW_OBJECT, E_USER_WARNING);

                return false;
            }
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                if ($as_object) {
                    $ret[] = &$obj;
                } else {
                    $row  = array();
                    $vars = $obj->getVars();
                    foreach (array_keys($vars) as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[] = $row;
                }
            } else {
                if ($as_object) {
                    $ret[$myrow[$this->keyName]] = &$obj;
                } else {
                    $row  = array();
                    $vars = $obj->getVars();
                    foreach (array_keys($vars) as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[$myrow[$this->keyName]] = $row;
                }
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * wfp_ObjectHandler::getList()
     *
     * @param  mixed  $criteria
     * @param  string $querie
     * @param  mixed  $show
     * @param  mixed  $doCriteria
     * @return array|bool
     */
    public function getList($criteria = null, $querie = '*', $show = null, $doCriteria = true)
    {
        $ret   = array();
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
            $sql = 'SELECT DISTINCT c.* FROM ' . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix('group_permission') . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . implode(',',
                                                                                                                                                                                                                                                    $this->user_groups)
                   . ' ))';
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
        if ($doCriteria !== false) {
            if ($criteria === null) {
                $criteria = new CriteriaCompo();
            }
            if ($criteria->getSort() === '') {
                $criteria->setSort($this->identifierName);
            }
            if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
                if ($this->doPermissions) {
                    $sql .= ' AND ' . $criteria->render();
                } else {
                    $sql .= ' ' . $criteria->renderWhere();
                }
                if ($criteria->getSort() !== '') {
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
                $ret[$myrow[$this->tkey_name]] = empty($this->identifierName) ? '' : htmlspecialchars($myrow[$this->identifierName], ENT_QUOTES);
            } else {
                $ret[$myrow[$this->keyName]] = empty($this->identifierName) ? '' : htmlspecialchars($myrow[$this->identifierName], ENT_QUOTES);
            }
        }
        $this->unsetTempKeyName();

        return $ret;
    }

    /**
     * wfp_ObjectHandler::getCount()
     *
     * @param  mixed  $criteria
     * @param  string $querie
     * @return bool
     */
    public function getCount($criteria = null, $querie = '*')
    {
        if ($this->doPermissions) {
            $sql = "SELECT ${querie} FROM " . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix('group_permission') . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . implode(',',
                                                                                                                                                                                                                                                 $this->user_groups)
                   . ' ) )';
        } else {
            $sql = "SELECT ${querie} FROM " . $this->db_table;
        }
        if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
            if ($this->doPermissions) {
                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
        }
        if (!$result = $this->db->query($sql)) {
            trigger_error($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        }
        if ($querie !== '*') {
            return $this->db->fetchArray($result);
        } else {
            $count = $this->db->getRowsNum($result);
        }

        return $count;
    }

    /**
     * @return string
     */
    public function isId()
    {
        echo 'hello';
        $a = '212121';

        return $a;
    }

    /**
     * wfp_ObjectHandler::insert()
     *
     * @param  XoopsObject $obj
     * @param  mixed       $checkObject
     * @param  mixed       $andclause
     * @param  mixed       $force
     * @return bool|void
     */
    public function insert(
        XoopsObject $obj,
        $force = true,
        $checkObject = true,
        $andclause = null
    ) // insert(&$obj, $checkObject = true, $andclause = null, $force = false)
    {
        if ($checkObject === true) {
            if (!is_object($obj) || !is_a($obj, $this->obj_class)) {
                $this->setErrors(_MD_WFP_ISNOTOBEJECT);

                return false;
            }
            if (!$obj->isDirty()) {
                $this->setErrors(_MD_WFP_ISNOTOBEJECTDIRTY . '<br>' . $obj->getErrors());

                return false;
            }
        }
        if (!$obj->cleanVars()) {
            $this->setErrors($obj->getErrors());

            return false;
        }
        if ($obj->isNew()) {
            $obj->cleanVars[$this->keyName] = '';
            foreach ($obj->cleanVars as $k => $v) {
                $cleanvars[$k] = ($obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT) ? (int)$v : $this->db->quoteString($v);
            }
            $sql = 'INSERT INTO ' . $this->db_table . ' (`' . implode('`, `', array_keys($cleanvars)) . '`) VALUES (' . implode(',', array_values($cleanvars)) . ')';
        } else {
            $sql = 'UPDATE ' . $this->db_table . ' SET';
            foreach ($obj->cleanVars as $k => $v) {
                if (null !== $notfirst) {
                    $sql .= ', ';
                }
                if ($obj->vars[$k]['data_type'] === XOBJ_DTYPE_INT) {
                    $sql .= ' ' . $k . ' = ' . (int)$v;
                } else {
                    $sql .= ' ' . $k . ' = ' . $this->db->quoteString($v);
                }
                $notfirst = true;
            }
            $sql .= ' WHERE ' . $this->keyName . " = '" . $obj->getVar($this->keyName) . "'";
            if ($andclause) {
                $sql .= $andclause;
            }
        }
        if ($force !== false) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        // echo $sql.'<br>';
        if (!$result) {
            trigger_error($this->db->errno() . ' ' . $this->db->error(), E_USER_ERROR);
            $this->setErrors($GLOBALS['xoopsDB']->errno() . ' ' . $GLOBALS['xoopsDB']->error() . ' ' . __FILE__ . ' ' . __LINE__);

            return false;
        }
        if ($obj->isNew() && !is_array($this->keyName)) {
            $obj->assignVar($this->keyName, $this->db->getInsertId());
        }

        return true;
    }

    /**
     * wfp_ObjectHandler::updateAll()
     *
     * @param  mixed   $fieldname
     * @param  integer $fieldvalue
     * @param  mixed   $criteria
     * @param  mixed   $force
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue = 0, $criteria = null, $force = true)
    {
        if (is_array($fieldname) && $fieldvalue === 0) {
            $set_clause = '';
            foreach ($fieldname as $key => $value) {
                if (null !== $notfirst) {
                    $set_clause .= ', ';
                }
                $set_clause .= is_numeric($key) ? ' ' . $key . ' = ' . $value : ' ' . $key . ' = ' . $this->db->quoteString($value);
                $notfirst = true;
            }
        } else {
            $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        }
        $sql = 'UPDATE ' . $this->db_table . ' SET ' . $set_clause;

        if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ($force !== false) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(sprintf(_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * wfp_ObjectHandler::delete()
     *
     * @param  int|XoopsObject $obj
     * @param  mixed       $force
     * @return bool|void
     */
    public function delete(XoopsObject $obj, $force = false)
    {
        if (!is_object($obj) || !is_a($obj, $this->obj_class)) {
            $this->setErrors(sprintf(_MD_WFP_ERROR_DELETE, basename(__FILE__), __LINE__));

            return false;
        }
        if (is_array($this->keyName)) {
            $clause     = array();
            $arrayCount = count($this->keyName);
            for ($i = 0; $i < $arrayCount; ++$i) {
                $clause[] = $this->keyName[$i] . ' = ' . $obj->getVar($this->keyName[$i]);
            }
            $whereclause = implode(' AND ', $clause);
        } else {
            $whereclause = $this->keyName . ' = ' . $obj->getVar($this->keyName);
        }
        $sql = 'DELETE FROM ' . $this->db_table . ' WHERE ' . $whereclause;
        if ($force !== false) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(sprintf(_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * wfp_ObjectHandler::updateCounter()
     *
     * @param  mixed $fieldname
     * @param  mixed $criteria
     * @param  mixed $force
     * @return bool
     */
    public function updateCounter($fieldname, $criteria = null, $force = true)
    {
        $set_clause = $fieldname . '=' . $fieldname . '+1';
        $sql        = 'UPDATE ' . $this->db_table . ' SET ' . $set_clause;
        if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ($force !== false) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $this->setErrors(sprintf(_MD_WFP_ERROR, $GLOBALS['xoopsDB']->errno(), $GLOBALS['xoopsDB']->error(), __FILE__, __LINE__));

            return false;
        }

        return true;
    }

    /**
     * wfp_ObjectHandler::getaDate()
     *
     * @param  string $exp_value
     * @param  string $exp_time
     * @param  mixed  $useMonth
     * @return array
     */
    public function getaDate($exp_value = '', $exp_time = '', $useMonth = 0)
    {
        $_date_arr = array();
        $_date     = $exp_value ?: time();
        $d         = date('j', $_date);
        $m         = date('m', $_date);
        $y         = date('Y', $_date);
        if ($useMonth > 0) {
            /**
             * We use +1 for the the previous month and not the next here,
             * if the day var is set to 0 ( You would have thought a neg value would have been correct here but nope!
             * Bloody strange way of doing it if you ask me! :-/ )
             */
            $_date_arr['begin'] = mktime(0, 0, 0, $m, 1, $y);
            $_date_arr['end']   = mktime(0, 0, 0, $m + 1, 0, $y);
        } else {
            /**
             * 86400 = 1 day, while 86399 = 23 hours and 59 mins and 59 secs
             */
            $_date_arr['begin'] = mktime(0, 0, 0, $m, $d, $y);
            $_date_arr['end']   = mktime(23, 59, 59, $m, $d, $y);
        }

        return $_date_arr;
    }

    /**
     * wfc_RefersHandler::showHtmlCalendar()
     * @return null|string|void
     */
    public function showHtmlCalendar()
    {
        if (func_num_args() !== 2) {
            return null;
        }
        $display = func_get_arg(0);
        $date    = func_get_arg(1);

        $jstime = formatTimestamp('F j Y, H:i:s', time());
        $value  = ($date === '') ? '' : strftime('%Y-%m-%d %I:%M', $date);
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/calendar/calendar.php';
        $calendar = new DHTML_Calendar(XOOPS_URL . '/modules/wfresource/class/calendar/', 'en', 'calendar-system', false);
        $calendar->load_files();

        return $calendar->make_input_field(array(
                                               'firstDay'   => 1,
                                               'showsTime'  => true,
                                               'showOthers' => true,
                                               'ifFormat'   => '%Y-%m-%d %I:%M',
                                               'timeFormat' => '12'
                                           ), // field attributes go here
                                           array('style' => '', 'name' => 'date', 'value' => $value), $display);
    }

    /**
     * wfp_ObjectHandler::displayCalendar()
     * @return string
     */
    public function displayCalendar()
    {
        $ret = '';
        if (func_num_args() !== 2) {
            return $ret;
        }
        $nav     = func_get_arg(0);
        $display = func_get_arg(1);

        $op       = wfp_Request::doRequest($_REQUEST, 'op', 'default', 'textbox');
        $onchange = 'onchange=\'location="' . basename($_SERVER['SCRIPT_FILENAME']) . '?op=' . $op . '&amp;%s="+this.options[this.selectedIndex].value\'';
        $ret .= '<form id="calender" method="post">';
        $ret .= '<div id="wrapper" style="padding-bottom: 8px;">';
        $ret .= '<div style="float: left;">' . $this->showHtmlCalendar(false, $nav['date']);
        $ret .= '<input type="text" name="search" id="search" size="20" maxlength="255" value="' . wfp_stripslashes($nav['search']) . '"/>&nbsp;';
        $ret .= wfp_getSelection(wfp_ListAndOr(), $nav['andor'], 'andor', 1, 0, false, false, '', 0, false) . '&nbsp;';
        $ret .= '<input align="left" type="submit" class="formbutton" value="' . _AM_WFP_SEARCH . '" name="selsubmit" /></div>';
        $ret .= '<div style="float: right;">';
        if ($display) {
            $ret .= _AM_WFC_DISPLAYPUBLISHED . wfp_getSelection(wfp_ListPages(), $nav['active'], 'active', 1, 0, false, false, sprintf($onchange, 'active'), 0, false) . '&nbsp;';
        }
        $ret .= _AM_WFC_DISPLAYAMOUNT_BOX . wfp_getSelection(wfp_ListArray(), $nav['limit'], 'limit', 1, 0, false, false, sprintf($onchange, 'limit'), 0, false);
        $ret .= '</div>';
        $ret .= '</div><br clear="all" />';
        $ret .= '</form>';
        echo $ret;
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * add an error
     *
     * @param $err_str
     * @internal param string $value error to add
     * @access   public
     */
    public function setErrors($err_str)
    {
        if (is_array($err_str)) {
            foreach ($err_str as $error) {
                $this->_errors[] = trim($error);
            }
        } else {
            $this->_errors[] = trim($err_str);
        }
    }

    /**
     * wfp_ObjectHandler::getHtmlErrors()
     *
     * @param  bool $return
     * @param  int  $menu
     * @return string
     */
    public function getHtmlErrors($return = false, $menu = 0)
    {
        global $menuHandler;

        $ret = '';
        if (0 !== count($this->_errors)) {
            foreach ($this->_errors as $error) {
                $ret .= $error . '<br>';
            }
        }
        if ($return === false) {
            xoops_cp_header();
            $menuHandler->addSubHeader(_MD_WFP_ERRORS);
            $menuHandler->render($menu);
            echo $ret;
            xoosla_cp_footer();
            exit();
        } else {
            return $ret;
        }
    }
}
