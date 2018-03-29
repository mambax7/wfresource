<?php

use Xmf\Request;

defined('XOOPS_ROOT_PATH') || die('You do not have permission to access this file!');

/**
 * wfp_Callback
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2007
 * @access    public
 */
class wfp_Callback extends wfp_ObjectHandler
{
    public $_callback;
    public $_obj;
    public $_id;
    public $_notifyType;
    public $value  = [];
    public $groups = [];

    /**
     * wfp_Callback::wfp_Callback()
     */
    public function __construct()
    {
    }

    /**
     * wfp_Callback::getSingleton()
     * @return mixed
     */
    public static function getSingleton()
    {
        static $instance;
        if (null === $instance) {
            $class    = __CLASS__;
            $instance = new $class();
        }

        return $instance;
    }

    /**
     * wfp_Callback::setCallback()
     *
     */
    public function setCallback()
    {
        $this->_callback = func_get_arg(0);
        $this->_id       = wfp_Request::doRequest($_REQUEST, $this->_callback->keyName, 0, 'int');
        $this->url       = isset($_SERVER['HTTP_REFERER']) ? urldecode($_SERVER['HTTP_REFERER']) : xoops_getenv('PHP_SELF');
    }

    /**
     * wfp_Callback::setMenu()
     *
     */
    public function setMenu()
    {
        $this->_menuid = (int)func_get_arg(0);
    }

    /**
     * wfp_Callback::setSubHeader()
     *
     */
    public function setSubHeader()
    {
        $this->_menuid = (int)func_get_arg(0);
    }

    /**
     * wfp_Callback::setRedirect()
     *
     */
    public function setRedirect()
    {
        $this->_redirect = &func_get_arg(0);
    }

    /**
     * wfp_Callback::setId()
     *
     */
    public function setId()
    {
        $this->_id = (int)func_get_arg(0);
    }

    /**
     * wfp_Callback::setNotificationType()
     *
     * @param string $type
     */
    public function setNotificationType($type = '')
    {
        $this->_notifyType = $type;
    }

    /**
     * wfp_Callback::getId()
     * @return int
     */
    public function getId()
    {
        $ret = ($this->_id > 0) ? $this->_id : 0;

        return $ret;
    }

    /**
     * wfp_Callback::help()
     * @return bool
     */
    public function help()
    {
        xoops_cp_header();
        $GLOBALS['menuHandler']->render($this->_menuid);
        wfp_showHelp();

        return true;
    }

    /**
     * wfp_Callback::about()
     * @return bool
     */
    public function about()
    {
        xoops_cp_header();
        $GLOBALS['menuHandler']->render($this->_menuid);
        wfp_showAbout();

        return true;
    }

    /**
     * wfp_Callback::edit()
     * @return bool
     */
    public function edit()
    {
        xooslaFormLoader();

        $_function = ($this->getId() > 0) ? 'get' : 'create';
        if ($this->getId() > 0) {
            //            $_obj = &call_user_func(array(&$this->_callback, $_function), $this->getId());
            $callArray = [&$this->_callback, $_function];
            $_obj      = call_user_func($callArray, $this->getId());
        } else {
            //            $_obj = &call_user_func(array(&$this->_callback, $_function));
            $callArray = [&$this->_callback, $_function];
            $_obj      = call_user_func($callArray);
        }
        xoops_cp_header();
        $GLOBALS['menuHandler']->render($this->_menuid);
        if (is_object($_obj)) {
            $_obj->formEdit($this->_callback->obj_class);

            return true;
        }

        return false;
    }

    /**
     * wfp_Callback::save()
     *
     * @param $options
     * @return bool
     */
    public function save($options)
    {
        if (!isset($options['noreturn'])) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($this->url, 0, $GLOBALS['xoopsSecurity']->getErrors(true));
            }
        }

        $_obj = call_user_func([$this->_callback, ($this->getId() > 0) ? 'get' : 'create'], ($this->getId() > 0) ? $this->getId() : true);
        $_obj->setVars($this->value);

        if ($this->_callback->insert($_obj, false)) {
            $this->groups = (0 !== count($this->groups)
                             && is_array($this->groups)) ? (array)$this->groups : ['0' => '1'];
            if (is_array($this->groups)) {
                foreach ($this->groups as $groups) {
                    wfp_savePerms($this->_callback, $groups, $_obj->getVar($this->_callback->keyName));
                }
            }
            $this->notifications($_obj);
            $this->tags($_obj);
            if (isset($options['noreturn'])) {
                return true;
            } else {
                // echo $this->url;
                redirect_header($this->url, 1, ($_obj->isNew() ? _AM_WFP_DBCTREATED : _AM_WFP_DBUPDATED));
            }
        }

        return false;
    }

    /**
     * wfp_Callback::deleteByID()
     * delete an object from the database by id.
     * @param  XoopsObject $obj id of the object to delete
     * @param  bool        $force
     * @return bool|void
     */
    public function deleteByID(\XoopsObject $obj, $force = false)
    {
        $wfc_cid = wfp_Request::doRequest($_REQUEST, $this->_callback->keyName, 0, 'int');
        $_obj    = $this->_callback->get($this->_id);
        if (is_object($_obj)) {
            $ok = wfp_Request::doRequest($_REQUEST, 'ok', 0, 'int');
            switch ($ok) {
                case 0:
                default:
                    xoops_cp_header();
                    $GLOBALS['menuHandler']->render($this->_menuid);
                    wfp_confirm([
                                    'op'                      => 'delete',
                                    $this->_callback->keyName => $this->_id,
                                    'ok'                      => 1,
                                    'url'                     => $this->url
                                ], $this->url, sprintf(_AM_WFP_DYRWTDICONFIRM, $_obj->getVar($this->_callback->identifierName)));

                    return true;
                    break;
                case 1:
                    if (!$GLOBALS['xoopsSecurity']->check()) {
                        redirect_header($this->url, 1, _AM_WFP_DBERROR);
                    }
                    if ($this->_callback->delete($_obj)) {
                        $url = wfp_Request::doRequest($_REQUEST, 'url', '', 'textbox');
                        wfp_deletePerms($this->_callback, $_obj->getVar($this->_callback->keyName));
                        xoops_comment_delete($GLOBALS['xoopsModule']->getVar('mid'), $_obj->getVar($this->_callback->keyName));
                        redirect_header($url, 1, _AM_WFP_DBUPDATEDDELETED);
                    } else {
                        return false;
                    }
                    break;
            } // switch
        }
    }

    /**
     * wfp_Callback::duplicate()
     * @return bool
     */
    public function duplicate()
    {
        // if ( !$GLOBALS['xoopsSecurity']->check() ) {
        // redirect_header( $this->url, 0, $GLOBALS['xoopsSecurity']->getErrors( true ) );
        // }
        $optionArray = '';
        if (func_num_args()) {
            $optionArray = func_get_arg(0);
        }
        $_obj = $this->_callback->get($this->_id);
        if (!is_object($_obj)) {
            return false;
        } else {
            $_obj->setNew();
            $oldID = $_obj->getVar($this->_callback->keyName);
            if ($this->_callback->insert($_obj, true, null, true)) {
                wfp_clonePerms($this->_callback, $oldID, $_obj->getVar($this->_callback->keyName));
                redirect_header($this->url, 1, _AM_WFP_DBITEMDUPLICATED);
            } else {
                return false;
            }
        }
    }

    /**
     * wfp_Callback::deleteall()
     *
     */
    public function deleteall()
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(xoops_getenv('PHP_SELF'), 1, _AM_WFP_DBERROR);
        }

        $array_keys = [];
        if (func_num_args() > 0) {
            $array_keys = &func_get_arg(0);
        }
        $checkbox = wfp_Request::doRequest($_REQUEST, 'checkbox', false, 'array');
        if (is_array($checkbox) && count($checkbox) > 0) {
            foreach (array_keys($checkbox) as $id) {
                $_obj = $this->_callback->get($id);
                if ($_obj) {
                    /**
                     * This is a check to prevent core or selected items from deletion
                     * for an example of how this is done look at the system block positions module
                     */
                    $array_keys = (!is_array($array_keys)) ? [] : $array_keys;
                    $do_delete  = true;
                    if (is_array($array_keys) && count($array_keys) > 0) {
                        foreach ($array_keys as $k => $v) {
                            if ($_obj->getVar($k) == $v) {
                                $do_delete = false;
                            }
                        }
                    }
                    if (true === $do_delete) {
                        if ($this->_callback->delete($_obj, false)) {
                            if (!empty($this->Handler->groupName)) {
                                wfp_deletePerms($this->Handler, $_obj->getVar($this->Handler->keyName));
                            }
                        } else {
                            trigger_error($_obj, E_USER_WARNING);
                        }
                    }
                }
            }
        }
        redirect_header($this->url, 1, (is_array($checkbox) && count($checkbox) > 0 ? _AM_WFP_DBITEMSDELETED : _AM_WFP_DBNOTUPDATED));
    }

    /**
     * wfp_Callback::cloneall()
     *
     */
    public function duplicateAll()
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(xoops_getenv('PHP_SELF'), 1, _AM_WFP_DBERROR);
        }

        $array_keys = [];
        if (func_num_args() > 0) {
            $array_keys = &func_get_arg(0);
        }
        $checkbox = wfp_Request::doRequest($_REQUEST, 'checkbox', false, 'array');
        if (is_array($checkbox) && count($checkbox) > 0) {
            foreach (array_keys($checkbox) as $id) {
                $_obj = $this->_callback->get($id);
                if ($_obj) {
                    $_obj->setNew();
                    if (is_array($array_keys) && count($array_keys) > 0) {
                        foreach ($array_keys as $k => $v) {
                            $_obj->setVar($k, $v);
                        }
                    }
                    $oldID = $_obj->getVar($this->_callback->keyName);
                    if ($this->_callback->insert($_obj, false)) {
                        if (!empty($this->Handler->groupName)) {
                            wfp_clonePerms($this->Handler, $oldID, $_obj->getVar($this->Handler->keyName));
                        }
                    }
                }
            }
        }
        redirect_header($this->url, 1, (is_array($checkbox) && count($checkbox) > 0 ? _AM_WFP_DBITEMSDUPLICATED : _AM_WFP_DBNOTUPDATED));
    }

    /**
     * wfp_Callback::updateall()
     *
     * @param  mixed $fieldname
     * @param  int   $fieldvalue
     * @param  null  $criteria
     * @param  bool  $force
     * @return bool|void
     */
    public function updateall($fieldname, $fieldvalue = 0, $criteria = null, $force = true)
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(xoops_getenv('PHP_SELF'), 1, _AM_WFP_DBERROR);
        }

        $array_keys = &func_get_arg(0);
        $checkbox   = wfp_Request::doRequest($_REQUEST, 'checkbox', false, 'array');
        // $checkbox = ( isset( $_REQUEST['checkbox'] ) ) ? $_REQUEST['checkbox']: '';
        // $checkbox = &wfp_cleanRequestVars( $_REQUEST, 'checkbox', null, XOBJ_DTYPE_OTHER );
        if (is_array($checkbox) && count($checkbox) > 0) {
            foreach (array_keys((array)$checkbox) as $id) {
                $_obj       = $this->_callback->get($id);
                $arrayCount = count($array_keys);
                for ($i = 0; $i < $arrayCount; ++$i) {
                    if (isset($_REQUEST[$array_keys[$i]])) {
                        $temp_array =& $_REQUEST[$array_keys[$i]];
                        $_obj->setVar($array_keys[$i], $temp_array[$id]);
                    }
                } // for
                if ($this->_callback->insert($_obj, false)) {
                    if (isset($_REQUEST[$this->_callback->groupName][$_obj->getVar($this->_callback->keyName)])) {
                        $groups = $_REQUEST[$this->_callback->groupName][$_obj->getVar($this->_callback->keyName)];
                        wfp_savePerms($this->_callback, $groups, $_obj->getVar($this->_callback->keyName));
                    }
                }
            }
        }
        redirect_header($this->url, 1, (is_array($checkbox)
                                        && count($checkbox) > 0) ? _AM_WFP_DBSELECTEDITEMSUPTATED : _AM_WFP_DBNOTUPDATED);
    }

    /**
     * wfp_Callback::notifications()
     *
     * @param mixed $_obj
     */
    public function notifications(&$_obj)
    {
        if (isset($GLOBALS['xoopsModuleConfig']['notification_enabled'])
            && $GLOBALS['xoopsModuleConfig']['notification_enabled'] > 0) {
            if (method_exists($this->_callback, 'upDateNotification')) {
                if (!empty($this->_notifyType)) {
                    $this->_callback->upDateNotification($_obj, $this->_notifyType);
                }
            }
        }
    }

    /**
     * wfp_Callback::tags()
     *
     * @param mixed $_obj
     */
    public function tags(&$_obj)
    {
        if (wfp_module_installed('tag')) {
            if (method_exists($this->_callback, 'upTagHandler')) {
                $this->_callback->upTagHandler($_obj);
            }
        }
    }

    /**
     * wfp_Callback::doBasics()
     *
     */
    public function setBasics()
    {
        $_REQUEST['dohtml']   = wfp_Request::doRequest($_REQUEST, 'dohtml', 0, 'int');
        $_REQUEST['dobr']     = wfp_Request::doRequest($_REQUEST, 'dobr', 0, 'int');
        $_REQUEST['doxcode']  = wfp_Request::doRequest($_REQUEST, 'doxcode', 0, 'int');
        $_REQUEST['dosmiley'] = wfp_Request::doRequest($_REQUEST, 'dosmiley', 0, 'int');
        $_REQUEST['doimage']  = wfp_Request::doRequest($_REQUEST, 'doimage', 0, 'int');
    }

    /**
     * wfp_Callback::setValue()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValue($key, $value)
    {
        $key               = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        $this->value[$key] = $value;
    }

    /**
     * wfp_Callback::setValue()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValueTime($key, $value)
    {
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        if (is_numeric($value)) {
            $value = $value;
        } elseif (is_string($value) && !empty($value)) {
            $value = strtotime($value);
        } else {
            $value = '';
        }
        $this->value[$key] = $value;
    }

    /**
     * wfp_Callback::setValueGroups()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValueGroups($key, $value)
    {
        $key                = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        $this->groups[$key] = (!empty($value)) ? $value : '';
    }

    /**
     * wfp_Callback::setValueArray()
     *
     * @param mixed $array
     */
    public function setValueArray($array)
    {
        foreach ($array as $key => $value) {
            $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
            $this->setValue($key, $value);
        }
    }

    /**
     * wfp_Callback::getValue()
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getValue($key)
    {
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

        return $this->value[$key];
    }

    /**
     * wfp_Callback::setImage()
     *
     * @param mixed  $key
     * @param mixed  $name
     * @param string $width
     * @param string $height
     */
    public function setImage($key, $name, $width = '', $height = '')
    {
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

        $cleani = explode('|', $name);
        $image  = is_array($cleani) ? $cleani[0] : $name;
        $width  = (!empty($width) || $width >= 0) ? $width : $cleani[1];
        $height = (!empty($height) || $height >= 0) ? $height : $cleani[2];
        if (is_array($cleani)) {
            if (!empty($image)) {
                $this->value[$key] = "$image|$width|$height";
            } else {
                $this->value[$key] = '';
            }
        } else {
            $this->value[$key] = $name;
        }
    }

    /**
     * wfc_PageHandler::htmlImport()
     *
     * @param  string $file
     * @param  bool   $doimport
     * @return null
     */
    public function htmlImport($file = '', $doimport = false)
    {
        $file = preg_replace('/^\W+|\W+$/', '', $file);
        /**
         * Do import
         */
        $ret = null;
        if (true === $doimport && !empty($file)) {
            $clean = wfp_getClass('clean');
            $ret   = $clean->importHtml($file, wfp_getModuleOption('htmluploaddir'));
        }

        return $ret;
    }

    /**
     * wfc_PageHandler::htmlClean()
     *
     * @param  mixed   $text
     * @param  integer $options
     * @return mixed|string
     */
    public function htmlClean($text = '', $options = 0)
    {
        /**
         * Do import
         */
        if ($options >= 0 && $options < 5) {
            $clean = wfp_getClass('clean');
            $text  = $clean->cleanUpHTML($text, $options);
        }

        return $text;
    }
}
