<?php
/**
 * Name: form_wfc_page.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: form_wfp_import.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 */
$form = new XoopsThemeForm( _MA_WFC_IMPORTHTML, 'page_form', 'import.php' );
$form->setExtra( 'enctype="multipart/form-data"' );
/**
 * Hidden Values
 */
$form->addElement( new XoopsFormHiddenToken() );
$form->addElement( new xoopsFormHidden( 'op', 'save' ) );
$form->addElement( new xoopsFormHidden( 'dohtml', '1' ) );
$form->addElement( new xoopsFormHidden( 'doxcode', '1' ) );
$form->addElement( new xoopsFormHidden( 'dosmiley', '1' ) );
$form->addElement( new xoopsFormHidden( 'doimage', '1' ) );
$form->addElement( new xoopsFormHidden( 'dobr', '0' ) );

$uploadir = new XoopsFormText( _MA_EWFC_PAGE_UPLOADDIR, 'uploadir', 50, 255, '' );
$uploadir->setDescription( _MA_EWFC_PAGE_UPLOADDIR_DSC );
$form->addElement( $uploadir, false );

/**
 * Page Title
 */
$page_title = new XoopsFormText( _MA_EWFC_MENU_TITLE, 'wfc_title', 50, 150, '' );
$page_title->setDescription( _MA_EWFC_IMENU_TITLE_DSC );
$form->addElement( $page_title, false );

/**
 * Page Headline
 */
$page_subtitle = new XoopsFormText( _MA_EWFC_PAGE_TITLE, 'wfc_headline', 50, 150, '' );
$page_subtitle->setDescription( _MA_EWFC_IPAGE_TITLE_DSC );
$form->addElement( $page_subtitle, false );

/**
 * Cleaning Options
 */
$clean_select = new XoopsFormSelect( _MA_EWFC_CLEANINGOPTIONS, 'wfc_cleaningoptions', 0 );
$clean_select->setDescription( _MA_EWFC_CLEANINGOPTIONS_DSC );
$clean_select->addOption( 0, _MA_EWFC_CLEANRAW );
$clean_select->addOption( 1, _MA_EWFC_CLEANHTML );
$clean_select->addOption( 2, _MA_EWFC_CLEANMSWORD );
$clean_select->addOption( 3, _MA_EWFC_CLEANALL );
$form->addElement( $clean_select );

/**
 */
$wfc_publish = new XoopsFormTextDateSelect( _MA_EWFC_PUBLISH, 'wfc_publish', 20, time(), true );
$wfc_publish->setDescription( _MA_EWFC_PUBLISH_DSC );
$form->addElement( $wfc_publish );

/**
 */
$wfc_expired = new XoopsFormTextDateSelect( _MA_EWFC_EXPIRE, 'wfc_expired', 0, '', false );
$wfc_expired->setDescription( _MA_EWFC_EXPIRE_DSC );
$form->addElement( $wfc_expired );

/**
 * if item is Default
 */
$wfc_mainmenu = new XoopsFormRadioYN( _MA_EWFC_MAINMENU, 'wfc_mainmenu', 0, ' ' . _MA_WFP_YES . '', ' ' . _MA_WFP_NO . '' );
$wfc_mainmenu->setDescription( _MA_EWFC_MAINMENU_DSC );
$form->addElement( $wfc_mainmenu );

$wfc_submenu = new XoopsFormRadioYN( _MA_EWFC_SUBMENU, 'wfc_submenu', 0, ' ' . _MA_WFP_YES . '', ' ' . _MA_WFP_NO . '' );
$wfc_submenu->setDescription( _MA_EWFC_SUBMENU_DSC );
$form->addElement( $wfc_submenu );

$group = wfp_getClass( 'permissions' );
$group->setPermissions( 'wfcpages', 'page_read', '', $GLOBALS['xoopsModule']->getVar( 'mid' ) );
$groups = new XoopsFormSelectCheckGroup( _MA_EWFP_GROUPS, 'page_read', '', '', true );
$groups->setDescription( _MA_EWFP_GROUPS_DSC );
$form->addElement( $groups );

/**
 * Buttons
 */
$form->addElement( new XoopsFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>