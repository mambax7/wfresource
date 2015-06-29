<?php
/**
 * Name: functions.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 * @version    : $Id: functions.php 10055 2012-08-11 12:46:10Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$dirname = basename(dirname(__DIR__));

define('_RESOURCE_DIR', $dirname);
define('_WFP_RESOURCE_PATH', XOOPS_ROOT_PATH . '/modules/' . $dirname);
define('_WFP_RESOURCE_URL', XOOPS_URL . '/modules/' . $dirname);
define('_RESOURCE_CLASS', 'wfp_');
define('_CORE_DIR', 'core');
// define('DS', DIRECTORY_SEPARATOR);

if (!class_exists('XoopsLoad')) {
    require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';
}

if (!class_exists('wfp_Filter')) {
    if (file_exists($hnd_file = _WFP_RESOURCE_PATH . '/class/class.request.php')) {
        require_once $hnd_file;
    }
}

function wfc_Debug()
{
    $path = str_replace(DIRECTORY_SEPARATOR, '/', __FILE__);
    $path = str_replace(XOOPS_ROOT_PATH, '', $path);
    echo 'File: ' . $path . ' Line:' . __LINE__;
}

/**
 * xooslaFormLoader()
 *
 * @return
 */
function xooslaFormLoader()
{
    require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xooslaformloader.php';
}

/**
 * wfp_getObjectHandler()
 *
 * @return
 */
function wfp_getObjectHandler()
{
    if (!class_exists('wfp_Object')) {
        if (file_exists($hnd_file = _WFP_RESOURCE_PATH . '/class/class.object.php')) {
            require_once $hnd_file;
        }
    }

    if (!class_exists('wfp_ObjectHandler')) {
        if (file_exists($hnd_file = _WFP_RESOURCE_PATH . '/class/class.objecthandler.php')) {
            require_once $hnd_file;
        }
    }
}

/**
 * wfp_loadLangauge()
 *
 * @param mixed $filename
 * @param mixed $module
 * @param mixed $language
 * @return
 */
function wfp_loadLangauge($filename, $module = null, $language = null)
{
    if (empty($filename)) {
        return false;
    }

    $language = (!is_null($language)) ? $language : $GLOBALS['xoopsConfig']['language'];
    $module   = (!is_null($module)) ? $module : $GLOBALS['xoopsModule']->getVar('dirname');

    $file = XOOPS_ROOT_PATH . '/modules/' . $module . '/language/' . $language . '/' . $filename . '.php';
    if (file_exists($file)) {
        include $file;
    } else {
        trigger_error('Langauge file: ' . str_replace(XOOPS_ROOT_PATH, '', $file) . ' does not exist', E_USER_WARNING);

        return false;
    }
}

/**
 * wfp_getObjectCallback()
 *
 * @param mixed $_handler
 * @return
 */
function &wfp_getObjectCallback($_handler)
{
    if (!class_exists('wfp_Callback')) {
        if (file_exists($hnd_file = _WFP_RESOURCE_PATH . '/class/class.objectcallback.php')) {
            include_once $hnd_file;
        }
    }
    $_do_callback = &wfp_Callback::getSingleton();
    $_do_callback->setCallback($_handler);

    return $_do_callback;
}

/**
 * wfp_gethandler()
 *
 * @param mixed  $name
 * @param string $dirname
 * @param string $c_prefix
 * @param mixed  $optional
 * @return
 */
function &wfp_gethandler($name, $dirname = 'wfresource', $c_prefix = 'wfp_', $optional = false)
{
    static $handlers;

    $name = strtolower(trim($name));
    if (!isset($handlers[$name])) {
        if (file_exists($hnd_file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/class.' . $name . '.php')) {
            include_once $hnd_file;
        } else {
            trigger_error('file for <b>' . $name . '</b> does not exist<br />file: ' . $hnd_file . '<br />Handler Name: ' . $name, E_USER_ERROR);
        }

        $class = $c_prefix . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $handlers[$name] = new $class($GLOBALS['xoopsDB']);
        }
    }
    if (!isset($handlers[$name]) && !$optional) {
        trigger_error('Class <b>' . $class . '</b> does not exist<br />Handler Name: ' . $name, E_USER_ERROR);
    }
    if (isset($handlers[$name])) {
        return $handlers[$name];
    }
    $inst = false;

    return $inst;
}

/**
 * wfp_getClass()
 *
 * @param mixed  $name
 * @param string $dirname
 * @param string $c_prefix
 * @param mixed  $optional
 * @return
 */
function &wfp_getClass($name, $dirname = 'wfresource', $c_prefix = 'wfp_', $options = '')
{
    static $_class;

    $name = strtolower(trim($name));
    if ($dirname == 'core') {
        $c_prefix = 'Xoops';
        $hnd_file = XOOPS_ROOT_PATH . '/class/' . $name . '.php';
    } else {
        $hnd_file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/class.' . $name . '.php';
    }

    if (file_exists($hnd_file)) {
        include_once $hnd_file;
    }

    $class = $c_prefix . ucfirst($name);
    if (class_exists($class)) {
        $_class[$name] = new $class($options);
    }
    if (!isset($_class[$name])) {
        trigger_error('Class <b>' . $class . '</b> does not exist<br />Class Name: ' . $name, E_USER_ERROR);
    }
    if (isset($_class[$name])) {
        return $_class[$name];
    }
    $inst = false;

    return $inst;
}

/**
 * wfp_ShowPagenav()
 *
 * @param integer $tot_num
 * @param integer $num_dis
 * @param integer $start
 * @param string  $from
 * @param integer $nav_type
 * @param string  $nav_path
 * @param mixed   $returns
 * @return
 */
function wfp_ShowPagenav($tot_num = 0, $num_dis = 10, $start = 0, $from = 'start', $nav_type = 1, $nav_path = '', $returns = false)
{
    $from_result = $start + 1;
    if ($num_dis == 0) {
        $num_dis = $tot_num;
    }
    if ($start + $num_dis < $tot_num) {
        $to_result = $start + $num_dis;
    } else {
        $to_result = $tot_num;
    }
    if ($from_result >= $tot_num) {
        $from_result = 1;
        $start       = 0;
    }
    $records = ($tot_num > 0) ? sprintf(_MD_WFP_RECORDSFOUND, $from_result, $to_result, $tot_num) : _MD_WFP_NORECORDS;

    $navigation = '';
    $page       = ($tot_num > $num_dis) ? _MD_WFP_PAGE : '';
    if ((int)($tot_num) > (int)$num_dis) {
        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav  = new XoopsPageNav((int)($tot_num), (int)($num_dis), (int)($start), $from, $nav_path);
        $nav_type = 1;
        switch ((int)($nav_type)) {
            case 1:
                $navigation = '&nbsp;' . $pagenav->renderNav();
                break;
            case 2:
            default:
                $navigation = '&nbsp;' . $pagenav->renderImageNav();
                break;
            case 3:
                $navigation = '&nbsp;' . $pagenav->renderSelect();
                break;
        } // switch
    }
    $ret = '';
    $ret .= '<div id="nav-wrapper">';
    $ret .= '<div style="float: left;">' . $records . '</div>';
    $ret .= '<div style="float: right;">' . $navigation . '</div>';
    $ret .= '</div>';
    $ret .= '<br clear="all" />';
    if ($returns == false) {
        echo $ret;
    } else {
        return $ret;
    }
}

/**
 * wfp_show_buttons()
 *
 * @param string $butt_align
 * @param string $butt_id
 * @param string $class_id
 * @param array  $button_array
 * @return
 */
function wfp_show_buttons($butt_align = 'right', $butt_id = 'button', $class_id = 'formbutton', $button_array = array())
{
    if (!is_array($button_array)) {
        return false;
    }
    $ret = "<div style='text-align: $butt_align; margin-bottom: 12px;'>\n";
    $ret .= "<form id='{$butt_id}' action='showbuttons'>\n";
    foreach ($button_array as $k => $v) {
        $ret .= "<input type='button' style='cursor: hand;' class='{$class_id}'  name='" . trim($v) . "' onclick=\"location='" . htmlspecialchars(trim($k), ENT_QUOTES) . "'\" value='" . trim($v) . "' />&nbsp;&nbsp;";
    }
    $ret .= "</form>\n";
    $ret .= "</div>\n";
    echo $ret;
}

/**
 * wfp_showImage()
 *
 * @param string $name
 * @param string $title
 * @param string $align
 * @param string $ext
 * @param string $path
 * @param string $size
 * @return
 */
function wfp_showImage($name = '', $title = '', $align = 'middle', $ext = 'png', $path = '', $size = '')
{
    if (empty($path)) {
        $path = 'modules/wfresource/images/icon';
    }
    if (!empty($name)) {
        $fullpath = XOOPS_URL . '/' . $path . '/' . $name . '.' . $ext;
        $ret      = '<img src="' . $fullpath . '" ';
        if (!empty($size)) {
            $ret = '<img src="' . $fullpath . '" ' . $size;
        }
        $ret .= ' title = "' . $title . '"';
        $ret .= ' alt = "' . $title . '"';
        if (!empty($align)) {
            $ret .= ' style="vertical-align: ' . $align . '; border: 0px;"';
        }
        $ret .= ' />';

        return $ret;
    }

    return '';
}

/**
 * wfp_getConstants()
 *
 * @param mixed  $_title
 * @param string $prefix
 * @param string $suffix
 * @return
 */
function wfp_getConstants($_title, $prefix = '', $suffix = '')
{
    $prefix = ($prefix != "" || $_title != 'action') ? trim($prefix) : "";
    $suffix = trim($suffix);

    return constant(strtoupper("$prefix$_title$suffix"));
}

/**
 * wfp_getImage()
 *
 * @param mixed $value
 * @return
 */
function wfp_getImage($value)
{
    if ($value != 'blank.png' || $value != 'blank.gif') {
        $image = explode('|', $value);
        $image = (is_array($image)) ? $image[0] : $value;

        return $image;
    } else {
        return '';
    }
}

/**
 * wfp_getIcons()
 *
 * @param array $_icon_array
 * @param mixed $key
 * @param mixed $value
 * @param mixed $extra
 * @return
 */
function wfp_getIcons($_icon_array = array(), $key, $value = null, $extra = null)
{
    $ret = '';
    if ($value) {
        foreach ($_icon_array as $_op => $_icon) {
            $url = (!is_numeric($_op)) ? $_op . "?{$key}=" . $value : xoops_getenv('PHP_SELF') . "?op={$_icon}&amp;{$key}=" . $value;
            if ($extra != null) {
                $url .= $extra;
            }
            $ret .= "<a href='" . $url . " '>" . wfp_showImage("wfp_$_icon", wfp_getConstants('_wfp_' . $_icon), null, 'png') . "</a>";
        }
    }

    return $ret;
}

/**
 * wfp_getSelection()
 *
 * @param array   $this_array
 * @param integer $selected
 * @param string  $value
 * @param string  $size
 * @param mixed   $emptyselect
 * @param mixed   $multipule
 * @param string  $noselecttext
 * @param string  $extra
 * @param integer $vvalue
 * @param mixed   $echo
 * @return
 */
function wfp_getSelection($this_array = array(), $selected = 0, $value = '', $size = '', $emptyselect = false, $multipule = false, $noselecttext = "------------------", $extra = '', $vvalue = 0, $echo = true)
{
    if ($multipule == true) {
        $ret = "<select size='" . $size . "' name='" . $value . "[]' id='" . $value . "[]' multiple='multiple' $extra>";
    } else {
        $ret = "<select size='" . $size . "' name='" . $value . "' id='" . $value . "' $extra>";
    }
    if ($emptyselect) {
        $ret .= "<option value=''>$noselecttext</option>";
    }
    if (count($this_array)) {
        foreach ($this_array as $key => $content) {
            $opt_selected = "";
            $newKey       = ((int)($vvalue) == 1) ? $content : $key;
            if (is_array($selected) && in_array($newKey, $selected)) {
                $opt_selected .= " selected='selected'";
            } else {
                if ($key == $selected) {
                    $opt_selected = "selected='selected'";
                }
            }
            $content = xoops_substr($content, 0, 24);
            $ret .= "<option value='" . $newKey . "' $opt_selected>" . $content . "</option>";
        }
    }
    $ret .= "</select>";
    if ($echo == true) {
        echo "<div>" . $ret . "</div><br />";
    } else {
        return $ret;
    }
}

/**
 * wfp_ShowLegend()
 *
 * @param mixed $led_array
 * @return
 */
function wfp_ShowLegend($led_array)
{
    $legend = '';
    /**
     * show legend
     */
    if (is_array($led_array)) {
        foreach ($led_array as $key) {
            $legend .= '<div style="padding: 3;">' . wfp_showImage('wfp_' . $key) . ' ' . wfp_getConstants('_wfp_' . $key . '_LEG') . '</div>';
        }
    } else {
        return '';
    }
    echo $legend;
}

/**
 * xoosla_cp_footer()
 *
 * @return
 */
function xoosla_cp_footer()
{
//    echo '<div style="padding-top: 16px; padding-bottom: 10px; text-align: center;">
//        <a href="' . $GLOBALS['xoopsModule']->getInfo('website_url') . '" target="_blank">' . wfp_showImage('xoopsmicrobutton', $_title = '', '', 'gif') . '
//        </a>
//    </div>';
//    global $xoopsModule;
    $pathIcon32      = XOOPS_URL . '/' . $GLOBALS['xoopsModule']->getInfo('icons32');
    echo "<div class='adminfooter'>\n" . "  <div style='text-align: center;'>\n" . "    <a href='http://www.xoops.org' rel='external'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n" . "  </div>\n" . "  " . _AM_MODULEADMIN_ADMIN_FOOTER . "\n" . "</div>";
    xoops_cp_footer();
}

/**
 * wfp_showHelp()
 *
 * @return
 */
function wfp_showHelp()
{
    require _WFP_RESOURCE_PATH . '/class/class.help.php';
    $wpf_Help = new wpf_Help();
    $wpf_Help->display();
}

/**
 * wfp_showAbout()
 *
 * @return
 */
function wfp_showAbout()
{
    require _WFP_RESOURCE_PATH . '/class/class.about.php';
    $wpf_About = new wpf_About();
    $wpf_About->display();
}

/**
 * wfp_confirm()
 *
 * @param mixed  $hiddens
 * @param mixed  $op
 * @param mixed  $msg
 * @param string $submit
 * @param string $cancel
 * @param mixed  $noarray
 * @param mixed  $echo
 * @return
 */
function wfp_confirm($hiddens, $op, $msg, $submit = '', $cancel = '', $noarray = false, $echo = true)
{
    $submit = ($submit != '') ? trim($submit) : _SUBMIT;
    $cancel = ($cancel != '') ? "onclick=\"location='" . htmlspecialchars(trim($cancel), ENT_QUOTES) . "'\"" : "onClick=\"location.href='" . xoops_getenv('HTTP_REFERER') . "';\"";
    $ret    = '
    <form method="post" op="' . $op . '">
    <div class="confirmMsg">' . $msg . '';
    foreach ($hiddens as $name => $value) {
        if (is_array($value) && $noarray == true) {
            foreach ($value as $caption => $newvalue) {
                $ret .= '<input type="radio" name="' . $name . '" value="' . htmlspecialchars($newvalue) . '" /> ' . $caption;
                $ret .= '<br />';
            }
        } else {
            if (is_array($value)) {
                foreach ($value as $new_value) {
                    $ret .= '<input type="hidden" name="' . $name . '[]" value="' . $new_value . '" />';
                }
            } else {
                $ret .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '" />';
            }
        }
    }
    $ret .= "</div>";
    $ret .= "<div class='confirmButtons'>
             <input type='button' class='formbutton' name='confirm_back' $cancel value='Cancel' />
             <input type='submit' class='formbutton' name='confirm_submit' value='$submit' />";
    $ret .= $GLOBALS['xoopsSecurity']->getTokenHTML();
    $ret .= "</div></form>";
    if ($echo) {
        echo $ret;
    } else {
        return $ret;
    }
}

/**
 * wfc_savePerms()
 *
 * @param mixed $h
 * @param mixed $groups
 * @param mixed $id
 * @return
 */
function wfp_savePerms(&$h, &$groups, $id)
{
    $group = wfp_getClass('permissions');
    $group->setPermissions($h->tableName, $h->groupName, '', $GLOBALS['xoopsModule']->getVar('mid'));
    $group->save($groups, $id);
}

/**
 * wfp_clonePerms()
 *
 * @param mixed $h
 * @param mixed $old_id
 * @param mixed $new_id
 * @return
 */
function wfp_clonePerms(&$h, $old_id = null, $new_id = null)
{
    if (is_null($old_id) or is_null($new_id)) {
        return false;
    }
    // set the persmissions
    $group = wfp_getClass('permissions');
    $group->setPermissions($h->tableName, $h->groupName, '', $GLOBALS['xoopsModule']->getVar('mid'));
    // get ID's for current page
    $groups = $group->getAdmin($old_id);
    // save new ID for new page
    $group->save($groups, $new_id);
}

/**
 * wfp_deletePerms()
 *
 * @param mixed $h
 * @param mixed $id
 * @return
 */
function wfp_deletePerms(&$h, $id)
{
    $group = wfp_getClass('permissions');
    $group->setPermissions($h->tableName, $h->groupName, '', $GLOBALS['xoopsModule']->getVar('mid'));
    $group->doDelete($id);
}

if (!function_exists('print_r_html')) {
    /**
     * print_r_html()
     *
     * @param string $value
     * @param mixed  $debug
     * @param mixed  $extra
     * @return
     */
    function print_r_html($value = '', $debug = false, $extra = false)
    {
        echo '<div>' . str_replace(array("\n", " "), array('<br>', '&nbsp;'), print_r($value, true)) . '</div>';
        if ($extra != false) {
            foreach ($_SERVER as $k => $v) {
                if ($k != "HTTP_REFERER") {
                    echo "<div><b>Server:</b> $k value: $v</div>";
                } else {
                    echo "<div><b>Server:</b> $k value: $v</div>";
                    $v = strpos($_SERVER[$k], XOOPS_URL);
                    echo "<div><b>Server:</b> $k value: $v</div>";
                }
            }
        }
    }
}

/**
 * wfp_file_exists()
 *
 * @param mixed $path
 * @param mixed $file
 * @param mixed $require
 * @return
 */
function wfp_file_exists($path, $file, $require = null)
{
    if (empty($path) || empty($file)) {
        return false;
    }
    $fullpath = $path . '/' . $file;
    // Check it
    if (file_exists(XOOPS_ROOT_PATH . '/' . $fullpath)) {
        if (is_null($require)) {
            return $fullpath;
        } else {
            require_once XOOPS_ROOT_PATH . '/' . $fullpath;
        }
    }

    return false;
}

/**
 * wfp_getFileListAsArray()
 *
 * @param mixed  $dirname
 * @param string $prefix
 * @return
 */
function wfp_getFileListAsArray($dirname, $prefix = '')
{
    $filelist = array();
    if (substr($dirname, -1) == '/') {
        $dirname = substr($dirname, 0, -1);
    }
    if (is_dir($dirname) && $handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if (!preg_match("/^[\.]{1,2}$/", $file) && is_file($dirname . '/' . $file)) {
                $file            = $prefix . $file;
                $filelist[$file] = $file;
            }
        }
        closedir($handle);
        asort($filelist);
        reset($filelist);
    }

    return $filelist;
}

/**
 * wfp_doUpload()
 *
 * @param string $value
 * @param mixed  $handler
 * @param mixed  $prefix
 * @param mixed  $uploadfolder
 * @return
 */
function wfp_doUpload($value = 'file', $handler, $prefix = null, $uploadfolder = null)
{
    if (is_null($uploadfolder)) {
        $uploadfolder = XOOPS_ROOT_PATH . '/uploads';
    }

    $array                 = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
    $ConfigUser['maxsize'] = 100000;
    $ConfigUser['width']   = 1000;
    $ConfigUser['height']  = 1000;
    /**
     * Up load file
     */
    require_once _WFP_RESOURCE_PATH . '/class/class.uploader.php';
    foreach ($_FILES as $FILE) {
        $uploader = new wfp_Uploader($uploadfolder, $array, $ConfigUser['maxsize'], $ConfigUser['width'], $ConfigUser['height']);
        if (!is_null($prefix)) {
            $uploader->setPrefix($prefix);
        }
        if (!empty($FILE['name'])) {
            if ($uploader->fetchMedia($FILE)) {
                if ($uploader->upload()) {
                    $_REQUEST['media'][] = array(
                        $value      => $uploader->getSavedFileName(),
                        'mediaType' => $uploader->getMediaType(),
                        'mediaSize' => $uploader->getMediaSize(),
                        'mediaExt'  => $uploader->getMediaExt(),
                        'mediaName' => $uploader->mediaName);

                    return true;
                }
            }
        }
        $errors = $uploader->getErrors();
        $handler->setErrors($errors);
        $_REQUEST['media'][$value][]      = 'error';
        $_REQUEST['media']['mediaType'][] = '';
        $_REQUEST['media']['mediaSize'][] = 0;

        return false;
    }
}

/**
 * wfp_uploader()
 *
 * @param mixed   $allowed_mimetypes
 * @param mixed   $uploadfile
 * @param string  $redirecturl
 * @param integer $num
 * @param string  $uploaddir
 * @param mixed   $redirect
 * @return
 */
function wfp_uploader($allowed_mimetypes, $uploadfile, $redirecturl = "index.php", $num = 0, $uploaddir = "uploads", $redirect = 0)
{
    global $xoopsModuleConfig;
    /**
     */
    include_once XOOPS_ROOT_PATH . '/class/uploader.php';
    $uploader = new XoopsMediaUploader(XOOPS_ROOT_PATH . "/${uploaddir}", $allowed_mimetypes, $xoopsModuleConfig['maxfilesize'], $xoopsModuleConfig['maximgwidth'], $xoopsModuleConfig['maximgheight']);
    /**
     */
    if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
        if (!$uploader->upload()) {
            return $uploader->getErrors();
        }
    } else {
        return $uploader->getErrors();
    }
    redirect_header($redirecturl, 1, _MD_WFP_FILEUPLOAD);
}

/**
 * wfp_getFileExtension()
 *
 * @param string $value
 * @return
 */
function wfp_getFileExtension($value = '')
{
    $filename        = explode('.', basename($value));
    $ret['basename'] = @$filename['0'];
    $ret['ext']      = @$filename['1'];

    return $ret;
}

/**
 * wfp_removeHeaders()
 *
 * @param mixed $buffer
 * @return
 */
function wfp_removeHeaders($buffer)
{
    return (str_replace('<h4></h4><br />', '', $buffer));
}

/**
 * xo_cp_ListArray()
 *
 * @return
 */
function wfp_ListArray()
{
    return array(1 => '1', 2 => '2', 3 => '3', 5 => '5', 10 => '10', 15 => '15', 25 => '25', 50 => '50', 100 => '100', 0 => 'All');
}

/**
 * xo_cp_displayArray()
 *
 * @return
 */
function wfp_displayArray()
{
    return array('2' => _XO_AD_SHOWALL_BOX, '1' => _XO_AD_SHOWVISIBLE_BOX, '0' => _XO_AD_SHOWHIDDEN_BOX);
}

/**
 * xo_cp_displayArray()
 *
 * @return
 */
function wfp_ListPages()
{
    return array('0' => _MD_WFC_SELALL, '1' => _MD_WFC_SELPUBLIHSED, '2' => _MD_WFC_SELUNPUBLISHED, '3' => _MD_WFC_SELEXPIRED, '4' => _MD_WFC_SELOFFLINE);
}

/**
 * andOr()
 *
 * @return
 */
function wfp_ListandOr()
{
    return array("AND" => _SR_ALL, "OR" => _SR_ANY, "exact" => _SR_EXACT);
}

/**
 * wfp_isEditorHTML()
 *
 * @return
 */
function wfp_isEditorHTML()
{
    if (isset($GLOBALS['xoopsModuleConfig']['use_wysiwyg']) && in_array($GLOBALS['xoopsModuleConfig']['use_wysiwyg'], array('tinymce', 'ckeditor', 'koivi', 'inbetween', 'spaw'))) {
        return true;
    }

    return false;
}

/**
 * wfp_tag_module_included()
 *
 * @return
 */
function wfp_tag_module_included()
{
    static $wfp_tag_module_included;
    if (!isset($wfp_tag_module_included)) {
        $modules_handler = xoops_gethandler('module');
        $tag_mod         = $modules_handler->getByDirName('tag');
        if (!$tag_mod) {
            $tag_mod = false;
        } else {
            $wfp_tag_module_included = $tag_mod->getVar('isactive') == 1;
        }
    }

    return $wfp_tag_module_included;
}

/**
 * news_getmoduleoption()
 *
 * @param mixed  $option
 * @param string $repmodule Thanks Instant (Herve and Marco) for the code
 * @return
 */
function wfp_getModuleOption($option, $dirname = 'wfchannel')
{
    static $tbloptions = array();
    if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
        return $tbloptions[$option];
    }

    $ret = false;
    if (isset($GLOBALS['xoopsModuleConfig']) && (is_object($GLOBALS['xoopsModule']) && $GLOBALS['xoopsModule']->getVar('dirname') == $dirname && $GLOBALS['xoopsModule']->getVar('isactive'))) {
        if (isset($GLOBALS['xoopsModuleConfig'][$option])) {
            $ret = $GLOBALS['xoopsModuleConfig'][$option];
        }
    } else {
        $module_handler = &xoops_gethandler('module');
        $module         = &$module_handler->getByDirname($dirname);
        $config_handler = &xoops_gethandler('config');
        if ($module) {
            $moduleConfig = &$config_handler->getConfigsByCat(0, $module->getVar('mid'));
            if (isset($moduleConfig[$option])) {
                $ret = $moduleConfig[$option];
            }
        }
    }
    $tbloptions[$option] = $ret;

    return $ret;
}

/**
 * wfp_addslashes()
 *
 * @param mixed $value
 * @return
 */
function wfp_addslashes($text)
{
    if (!get_magic_quotes_gpc()) {
        return addslashes($text);
    }

    return $text;
}

/**
 * wfp_stripslashes()
 *
 * @param mixed $text
 * @return
 */
function wfp_stripslashes($text)
{
    if (get_magic_quotes_gpc()) {
        return stripslashes($text);
    }

    return $text;
}

/**
 * wfp_tag_installed()
 *
 * @return
 */
function wfp_module_installed($module = '')
{
    static $wfp_module;
    if (!isset($wfp_module[$module])) {
        $modules_handler = xoops_gethandler('module');
        $tag_mod         = $modules_handler->getByDirName('tag');
        if ($tag_mod && $tag_mod->getVar('isactive')) {
            $wfp_module[$module] = $tag_mod = true;
        } else {
            false;
        }
    }

    return $wfp_module[$module];
}
