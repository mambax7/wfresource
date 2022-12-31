<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

use XoopsModules\Wfresource\Request as wfcRequest;

/**
 * WfpCallback
 *
 * @author    John
 * @copyright Copyright (c) 2007
 */
class WfpCallback extends WfpObjectHandler
{
    public $_callback;
    public $_obj;
    public $_id;
    public $_notifyType;
    public $value  = [];
    public $groups = [];
    public $url;
    public $_menuid;

    /**
     * WfpCallback::__construct()
     */
    public function __construct()
    {
    }

    /**
     * WfpCallback::getSingleton()
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
     * WfpCallback::setCallback()
     */
    public function setCallback(...$args): void
    {
        $this->_callback = \func_get_arg(0);
        $this->_id       = \Xmf\Request::getInt($this->_callback->keyName, 0);
        $this->url       = \Xmf\Request::hasVar('HTTP_REFERER', 'SERVER') ? \urldecode(\Xmf\Request::getString('HTTP_REFERER', '', 'SERVER')) : \xoops_getenv('SCRIPT_NAME');
    }

    /**
     * WfpCallback::setMenu()
     */
    public function setMenu(): void
    {
        $this->_menuid = (int)\func_get_arg(0);
    }

    /**
     * WfpCallback::setSubHeader()
     */
    public function setSubHeader(): void
    {
        $this->_menuid = (int)\func_get_arg(0);
    }

    /**
     * WfpCallback::setRedirect()
     */
    public function setRedirect(): void
    {
        $this->_redirect = \func_get_arg(0);
    }

    /**
     * WfpCallback::setId()
     */
    public function setId(): void
    {
        $this->_id = (int)\func_get_arg(0);
    }

    /**
     * WfpCallback::setNotificationType()
     *
     * @param string $type
     */
    public function setNotificationType($type = ''): void
    {
        $this->_notifyType = $type;
    }

    /**
     * WfpCallback::getId()
     */
    public function getId(): int
    {
        $ret = ($this->_id > 0) ? $this->_id : 0;

        return $ret;
    }

    /**
     * WfpCallback::help()
     * @return bool
     */
    public function help()
    {
        \xoops_cp_header();
        $menuHandler = new MenuHandler();
        $menuHandler->render($this->_menuid);
        Utility::showHelp();

        return true;
    }

    /**
     * WfpCallback::about()
     * @return bool
     */
    public function about()
    {
        \xoops_cp_header();
        $menuHandler = new MenuHandler();
        $menuHandler->render($this->_menuid);
        Utility::showAbout();

        return true;
    }

    /**
     * WfpCallback::edit()
     * @return bool
     */
    public function edit(...$args): bool
    {
        //        xooslaFormLoader();

        $_function = ($this->getId() > 0) ? 'get' : 'create';
        if ($this->getId() > 0) {
            //            $_obj = &call_user_func(array(&$this->_callback, $_function), $this->getId());
            $callArray = [&$this->_callback, $_function];
            $_obj      = \call_user_func($callArray, $this->getId());
        } else {
            //            $_obj = &call_user_func(array(&$this->_callback, $_function));
            $callArray = [&$this->_callback, $_function];
            $_obj      = \call_user_func($callArray);
        }
        //mb        \xoops_cp_header();
        $menuHandler = new MenuHandler();

        if (null === $menuHandler->_obj) {
            $menuHandler->_obj = $this->_obj;
        }
        $menuHandler->render($this->_menuid);

        if (\is_object($_obj)) {
            $_obj->formEdit($this->_callback->obj_class);

            return true;
        }

        return false;
    }

    /**
     * WfpCallback::save()
     *
     * @param $options
     * @return bool
     */
    public function save($options)
    {
        if (!isset($options['noreturn'])) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                \redirect_header($this->url, 0, $GLOBALS['xoopsSecurity']->getErrors(true));
            }
        }

        $_obj = \call_user_func([$this->_callback, ($this->getId() > 0) ? 'get' : 'create'], ($this->getId() > 0) ? $this->getId() : true);
        $_obj->setVars($this->value);

        if ($this->_callback->insert($_obj, false)) {
            $this->groups = (0 !== \count($this->groups)
                             && \is_array($this->groups)) ? $this->groups : ['0' => '1'];
            if (\is_array($this->groups)) {
                foreach ($this->groups as $groups) {
                    Utility::savePerms($this->_callback, $groups, $_obj->getVar($this->_callback->keyName));
                }
            }
            $this->notifications($_obj);
            $this->tags($_obj);
            if (isset($options['noreturn'])) {
                return true;
            }
            // echo $this->url;
            \redirect_header($this->url, 1, ($_obj->isNew() ? \_AM_WFP_DBCTREATED : \_AM_WFP_DBUPDATED));
        }

        return false;
    }

    /**
     * WfpCallback::deleteById()
     * delete an object from the database by id.
     * @param int  $id id of the object to delete
     * @param bool $force
     * @return bool|void
     */
    public function deleteById($id, $force = false)
    {
        $wfc_cid = \Xmf\Request::getInt($this->_callback->keyName, 0);
        $_obj    = $this->_callback->get($this->_id);
        if (\is_object($_obj)) {
            $ok = \Xmf\Request::getInt('ok', 0, 'REQUEST');
            switch ($ok) {
                case 0:
                default:
                    xoops_confirm(
                        [
                            'op'                      => 'delete',
                            $this->_callback->keyName => $this->_id,
                            'ok'                      => 1,
                            'url'                     => $this->url,
                        ],
                        $this->url,
                        \sprintf(\_AM_WFP_DYRWTDICONFIRM, $_obj->getVar($this->_callback->identifierName))
                    );

                    return true;
                    break;
                case 1:
                    if (!$GLOBALS['xoopsSecurity']->check()) {
                        \redirect_header($this->url, 1, \_AM_WFP_DBERROR);
                    }
                    if ($this->_callback->delete($_obj)) {
                        $url = \Xmf\Request::getUrl('url', '');
                        Utility::deletePerms($this->_callback, $_obj->getVar($this->_callback->keyName));
                        \xoops_comment_delete($GLOBALS['xoopsModule']->getVar('mid'), $_obj->getVar($this->_callback->keyName));
                        \redirect_header($url, 1, \_AM_WFP_DBUPDATEDDELETED);
                    } else {
                        return false;
                    }
                    break;
            } // switch
        }
    }

    /**
     * WfpCallback::duplicate()
     * @return bool
     */
    public function duplicate(...$args): ?bool
    {
        // if ( !$GLOBALS['xoopsSecurity']->check() ) {
        // redirect_header( $this->url, 0, $GLOBALS['xoopsSecurity']->getErrors( true ) );
        // }
        $optionArray = '';
        if (\func_num_args()) {
            $optionArray = \func_get_arg(0);
        }
        $_obj = $this->_callback->get($this->_id);
        if (!\is_object($_obj)) {
            return false;
        }
        $_obj->setNew();
        $oldID = $_obj->getVar($this->_callback->keyName);
        if ($this->_callback->insert($_obj, true, null, true)) {
            Utility::clonePerms($this->_callback, $oldID, $_obj->getVar($this->_callback->keyName));
            \redirect_header($this->url, 1, \_AM_WFP_DBITEMDUPLICATED);
        } else {
            return false;
        }
    }

    /**
     * WfpCallback::deleteall()
     */
    public function deleteall(...$args): void
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            \redirect_header(\xoops_getenv('SCRIPT_NAME'), 1, \_AM_WFP_DBERROR);
        }

        $array_keys = [];
        if (\func_num_args() > 0) {
            $array_keys = \func_get_arg(0);
        }
        $checkbox = \Xmf\Request::getArray('checkbox', []);
        if ((is_countable($checkbox) ? \count($checkbox) : 0) > 0) {
            foreach (\array_keys($checkbox) as $id) {
                $_obj = $this->_callback->get($id);
                if ($_obj) {
                    /**
                     * This is a check to prevent core or selected items from deletion
                     * for an example of how this is done look at the system block positions module
                     */
                    $array_keys = (!\is_array($array_keys)) ? [] : $array_keys;
                    $do_delete  = true;
                    if ($array_keys && \is_array($array_keys)) {
                        foreach ($array_keys as $k => $v) {
                            if ($_obj->getVar($k) == $v) {
                                $do_delete = false;
                                break;
                            }
                        }
                    }
                    if ($do_delete) {
                        if ($this->_callback->delete($_obj, false)) {
                            if (!empty($this->Handler->groupName)) {
                                Utility::deletePerms($this->Handler, $_obj->getVar($this->Handler->keyName));
                            }
                        } else {
                            \trigger_error($_obj, \E_USER_WARNING);
                        }
                    }
                }
            }
        }
        \redirect_header($this->url, 1, (\is_array($checkbox) && \count($checkbox) > 0 ? \_AM_WFP_DBITEMSDELETED : \_AM_WFP_DBNOTUPDATED));
    }

    /**
     * WfpCallback::cloneall()
     */
    public function duplicateAll(...$args): void
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            \redirect_header(\xoops_getenv('SCRIPT_NAME'), 1, \_AM_WFP_DBERROR);
        }

        $array_keys = [];
        if (\func_num_args() > 0) {
            $array_keys = \func_get_arg(0);
        }
        $checkbox = \Xmf\Request::getArray('checkbox', []);
        if ((is_countable($checkbox) ? \count($checkbox) : 0) > 0) {
            foreach (\array_keys($checkbox) as $id) {
                $_obj = $this->_callback->get($id);
                if ($_obj) {
                    $_obj->setNew();
                    if ($array_keys && \is_array($array_keys)) {
                        foreach ($array_keys as $k => $v) {
                            $_obj->setVar($k, $v);
                        }
                    }
                    $oldID = $_obj->getVar($this->_callback->keyName);
                    if ($this->_callback->insert($_obj, false)) {
                        if (!empty($this->Handler->groupName)) {
                            Utility::clonePerms($this->Handler, $oldID, $_obj->getVar($this->Handler->keyName));
                        }
                    }
                }
            }
        }
        \redirect_header($this->url, 1, (\is_array($checkbox) && \count($checkbox) > 0 ? \_AM_WFP_DBITEMSDUPLICATED : \_AM_WFP_DBNOTUPDATED));
    }

    /**
     * WfpCallback::updateall()
     *
     * @param mixed $fieldname
     * @param int   $fieldvalue
     * @param null  $criteria
     * @param bool  $force
     */
    public function updateall($fieldname, $fieldvalue = 0, $criteria = null, $force = true): void
    {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            \redirect_header(\xoops_getenv('SCRIPT_NAME'), 1, \_AM_WFP_DBERROR);
        }

        $array_keys = $fieldname;
        $checkbox   = \Xmf\Request::getArray('checkbox', []);
        if ((is_countable($checkbox) ? \count($checkbox) : 0) > 0) {
            foreach (\array_keys((array)$checkbox) as $id) {
                $_obj       = $this->_callback->get($id);
                $arrayCount = \count($array_keys);
                for ($i = 0; $i < $arrayCount; ++$i) {
                    if (isset($_REQUEST[$array_keys[$i]])) {
                        $temp_array = &$_REQUEST[$array_keys[$i]];
                        $_obj->setVar($array_keys[$i], $temp_array[$id]);
                    }
                } // for
                if ($this->_callback->insert($_obj, false)) {
                    if (isset($_REQUEST[$this->_callback->groupName][$_obj->getVar($this->_callback->keyName)])) {
                        $groups = $_REQUEST[$this->_callback->groupName][$_obj->getVar($this->_callback->keyName)];
                        Utility::savePerms($this->_callback, $groups, $_obj->getVar($this->_callback->keyName));
                    }
                }
            }
        }
        \redirect_header(
            $this->url,
            1,
            ((is_countable($checkbox) ? \count($checkbox) : 0) > 0) ? \_AM_WFP_DBSELECTEDITEMSUPTATED : \_AM_WFP_DBNOTUPDATED
        );
    }

    /**
     * WfpCallback::notifications()
     *
     * @param mixed $_obj
     */
    public function notifications(&$_obj): void
    {
        if (isset($GLOBALS['xoopsModuleConfig']['notification_enabled'])
            && $GLOBALS['xoopsModuleConfig']['notification_enabled'] > 0) {
            if (\method_exists($this->_callback, 'upDateNotification')) {
                if (!empty($this->_notifyType)) {
                    $this->_callback->upDateNotification($_obj, $this->_notifyType);
                }
            }
        }
    }

    /**
     * WfpCallback::tags()
     *
     * @param mixed $_obj
     */
    public function tags(&$_obj): void
    {
        if (Utility::module_installed('tag')) {
            if (\method_exists($this->_callback, 'upTagHandler')) {
                $this->_callback->upTagHandler($_obj);
            }
        }
    }

    /**
     * WfpCallback::doBasics()
     */
    public function setBasics(): void
    {
        $_REQUEST['dohtml']   = \Xmf\Request::getInt('dohtml', 0);
        $_REQUEST['dobr']     = \Xmf\Request::getInt('dobr', 0);
        $_REQUEST['doxcode']  = \Xmf\Request::getInt('doxcode', 0);
        $_REQUEST['dosmiley'] = \Xmf\Request::getInt('dosmiley', 0);
        $_REQUEST['doimage']  = \Xmf\Request::getInt('doimage', 0);
    }

    /**
     * WfpCallback::setValue()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValue($key, $value): void
    {
        $key               = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        $this->value[$key] = $value;
    }

    /**
     * WfpCallback::setValue()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValueTime($key, $value): void
    {
        $key = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        if (\is_numeric($value)) {
            $value = $value;
        } elseif (\is_string($value) && !empty($value)) {
            $value = \strtotime($value);
        } else {
            $value = '';
        }
        $this->value[$key] = $value;
    }

    /**
     * WfpCallback::setValueGroups()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValueGroups($key, $value): void
    {
        $key                = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        $this->groups[$key] = !empty($value) ? $value : '';
    }

    /**
     * WfpCallback::setValueArray()
     *
     * @param mixed $array
     */
    public function setValueArray($array): void
    {
        foreach ($array as $key => $value) {
            $key = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
            $this->setValue($key, $value);
        }
    }

    /**
     * WfpCallback::getValue()
     *
     * @param mixed $key
     * @return mixed
     */
    public function getValue($key)
    {
        $key = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

        return $this->value[$key];
    }

    /**
     * WfpCallback::setImage()
     *
     * @param mixed  $key
     * @param mixed  $name
     * @param string $width
     * @param string $height
     */
    public function setImage($key, $name, $width = '', $height = ''): void
    {
        $key = \preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

        $cleani = \explode('|', (string)$name);
        $image  = \is_array($cleani) ? $cleani[0] : $name;
        $width  = (!empty($width) || $width >= 0) ? $width : $cleani[1];
        $height = (!empty($height) || $height >= 0) ? $height : $cleani[2];
        if (\is_array($cleani)) {
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
     * PageHandler::htmlImport()
     *
     * @param string $file
     * @param bool   $doimport
     * @return array|string|null |null
     */
    public function htmlImport($file = '', $doimport = false)
    {
        $file = \preg_replace('/^\W+|\W+$/', '', $file);
        /**
         * Do import
         */
        $ret = null;
        if ($doimport && !empty($file)) {
            $clean = new Clean(); //wfp_getClass('clean');
            $ret   = $clean->importHtml($file, Utility::getModuleOption('htmluploaddir'));
        }

        return $ret;
    }

    /**
     * PageHandler::htmlClean()
     *
     * @param mixed $text
     * @param int   $options
     * @return mixed|string
     */
    public function htmlClean($text = '', $options = 0)
    {
        /**
         * Do import
         */
        if ($options >= 0 && $options < 5) {
            $clean = new Clean(); //wfp_getClass('clean');
            $text  = $clean->cleanUpHTML($text, $options);
        }

        return $text;
    }
}
