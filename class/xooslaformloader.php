<?php
/**
 * Name: xoopsformloader.php
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

xoops_load('XoopsForm');

/**
 * Start of Xoosla Forms includes
 */
require_once __DIR__ . '/xooslaforms/themetabform.php';
require_once __DIR__ . '/xooslaforms/xoosla_formselectimage.php';

/*
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formelement.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader_org.php';

if ( !class_exists( 'XoopsFormSelect' ) ) {
    require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselect.php';
}

require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectimage.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselecttype.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcalendar.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtextdateselect.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectdirlist.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectrdirlist.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtextadd.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcheckbox.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectcheckgroup.php';
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcaptcha.php';

$file = XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php';
if ( is_dir( XOOPS_ROOT_PATH . '/class/xoopseditor' ) && file_exists( $file ) ) {
    if ( !class_exists( 'XoopsFormEditor' ) ) {
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formeditor.php';
    }
    if ( !class_exists( 'XoopsFormSelectEditor' ) ) {
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselecteditor.php';
    }
}
*/
