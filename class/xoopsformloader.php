<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

if (!class_exists('XoopsFormButtonTray')) {
    require XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formbuttontray.php';
    require XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectcontest.php';
}
require XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectimage.php';
require XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formdateselect.php';
