<?php declare(strict_types=1);

/**
 * Name: form_wfc_page.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use XoopsModules\Wfresource;
use XoopsModules\Wfresource\{
    Xoopsforms
};


defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$form = new \XoopsThemeForm(_AM_WFCHANNEL_IMPORTHTML, 'page_form', 'import.php');
$form->setExtra('enctype="multipart/form-data"');
/**
 * Hidden Values
 */
$form->addElement(new \XoopsFormHiddenToken());
$form->addElement(new \XoopsFormHidden('op', 'save'));
$form->addElement(new \XoopsFormHidden('dohtml', '1'));
$form->addElement(new \XoopsFormHidden('doxcode', '1'));
$form->addElement(new \XoopsFormHidden('dosmiley', '1'));
$form->addElement(new \XoopsFormHidden('doimage', '1'));
$form->addElement(new \XoopsFormHidden('dobr', '0'));

$uploadir = new \XoopsFormText(_AM_EWFC_PAGE_UPLOADDIR, 'uploadir', 50, 255, '');
$uploadir->setDescription(_AM_EWFC_PAGE_UPLOADDIR_DSC);
$form->addElement($uploadir, false);

/**
 * Page Title
 */
$page_title = new \XoopsFormText(_AM_EWFC_MENU_TITLE, 'wfc_title', 50, 150, '');
$page_title->setDescription(_AM_EWFC_IMENU_TITLE_DSC);
$form->addElement($page_title, false);

/**
 * Page Headline
 */
$page_subtitle = new \XoopsFormText(_AM_EWFC_PAGE_TITLE, 'wfc_headline', 50, 150, '');
$page_subtitle->setDescription(_AM_EWFC_IPAGE_TITLE_DSC);
$form->addElement($page_subtitle, false);

/**
 * Cleaning Options
 */
$clean_select = new \XoopsFormSelect(_AM_EWFC_CLEANINGOPTIONS, 'wfc_cleaningoptions', 0);
$clean_select->setDescription(_AM_EWFC_CLEANINGOPTIONS_DSC);
$clean_select->addOption(0, _AM_EWFC_CLEANRAW);
$clean_select->addOption(1, _AM_EWFC_CLEANHTML);
$clean_select->addOption(2, _AM_EWFC_CLEANMSWORD);
$clean_select->addOption(3, _AM_EWFC_CLEANALL);
$form->addElement($clean_select);

$wfc_publish = new Xoopsforms\XoopsFormTextDateSelect(_AM_EWFC_PUBLISH, 'wfc_publish', 20, time(), true);
$wfc_publish->setDescription(_AM_EWFC_PUBLISH_DSC);
$form->addElement($wfc_publish);

$wfc_expired = new Xoopsforms\XoopsFormTextDateSelect(_AM_EWFC_EXPIRE, 'wfc_expired', 0, '', false);
$wfc_expired->setDescription(_AM_EWFC_EXPIRE_DSC);
$form->addElement($wfc_expired);

/**
 * if item is Default
 */
$wfc_mainmenu = new \XoopsFormRadioYN(_AM_EWFC_MAINMENU, 'wfc_mainmenu', 0, ' ' . _AM_WFP_YES, ' ' . _AM_WFP_NO);
$wfc_mainmenu->setDescription(_AM_EWFC_MAINMENU_DSC);
$form->addElement($wfc_mainmenu);

$wfc_submenu = new \XoopsFormRadioYN(_AM_EWFC_SUBMENU, 'wfc_submenu', 0, ' ' . _AM_WFP_YES, ' ' . _AM_WFP_NO);
$wfc_submenu->setDescription(_AM_EWFC_SUBMENU_DSC);
$form->addElement($wfc_submenu);

$group = new Wfresource\Permissions(); //wfp_getClass('permissions');
$group->setPermissions('wfcpages', 'page_read', '', $GLOBALS['xoopsModule']->getVar('mid'));
$groups = new Xoopsforms\XoopsFormSelectCheckGroup(_AM_EWFP_GROUPS, 'page_read', '', '', true);
$groups->setDescription(_AM_EWFP_GROUPS_DSC);
$form->addElement($groups);

/**
 * Buttons
 */
$form->addElement(new \XoopsFormButtonTray('submit', _SUBMIT));
$form->display();
