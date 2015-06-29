<?php
if ( !defined( 'XOOPS_ROOT_PATH' ) ) {
	exit();
}

include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formelement.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader_org.php';
if ( !class_exists( 'XoopsFormButtonTray' ) ) {
	include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formbuttontray.php';
	include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectcontest.php';
}
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectimage.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselecttype.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcalendar.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtextdateselect.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectsection.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectcategory.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectdirlist.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectrdirlist.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/themetabform.php';
// include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtabs.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtextadd.php';
// include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formimagever.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcheckbox.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselectcheckgroup.php';
include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formcaptcha.php';
$file = XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php';
if ( is_dir( XOOPS_ROOT_PATH . '/class/xoopseditor' ) && file_exists( $file ) ) {
	if ( !class_exists( 'XoopsFormEditor' ) ) {
		include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formeditor.php';
	}
	if ( !class_exists( 'XoopsFormSelectEditor' ) ) {
		include_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formselecteditor.php';
	}
}

?>