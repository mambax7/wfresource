<?php declare(strict_types=1);

// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

use XoopsModules\Wfresource;
use XoopsModules\Wfresource\Xoopsforms;

//require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader.php';

global $modversion, $xoopsUser, $categoryHandler;

$wfs = false;

$caption = (!$this->isNew()) ? $caption = sprintf(_AM_WFP_CATEGORY_MODIFY, $this->getVar('category_title')) : _AM_WFP_CREATECATEGORY;
$form    = new Xoopsforms\XoopsThemeTabForm($caption, 'cat_form', $modversion['adminpath']);
$form->setExtra('enctype="multipart/form-data"');
$form->doTabs();

$form->addElement(new \XoopsFormHidden('op', 'save'));
$form->addElement(new \XoopsFormHidden('category_id', $this->getVar('category_id')));
$buttons = new \XoopsFormButtonTray('submit', _SUBMIT);

$form->startTab('Main', 'xo_category-info');
if ($wfs) {
    $category_sid = new Xoopsforms\XoopsFormSelectSection(_AM_ECATEGORY_CSECTION, 'category_sid', $this->getVar('category_sid'), 1, false);
    $category_sid->setDescription(_AM_SMILIES_SELECTIMAGE_DSC);
    $form->addElement($category_sid);
}
// $categorys = call_user_func( array( &$categoryHandler, 'getObj' ), $this->getVar('category_id') );
// if ($categorys['list']) {
// $category_id = new \XoopsFormTree( _AM_ECATEGORY_CSUBCATEGORY, 'category_pid', 'category_title', '-', $this->getVar( 'category_pid' ), true, 0 );
// $category_id->addOptions( $categorys['list'], 'category_id', 'category_pid' );
// $form->addElement( $category_id, true );
// }
// Set display name
$category_title = new \XoopsFormText(_AM_ECATEGORY_TITLE, 'category_title', 50, 60, $this->getVar('category_title', 'e'));
$category_title->setDescription(_AM_ECATEGORY_TITLE_DSC);
$form->addElement($category_title, true);

if ($wfs) {
    $category_type = new Xoopsforms\XoopsFormSelectType(_AM_ECATEGORY_TYPE, 'category_type', $this->getVar('category_type'));
    $category_type->setDescription(_AM_ECATEGORY_SIDE_DSC);
    $form->addElement($category_type, true);
}

if (class_exists('XoopsFormEditor')) {
    $options['name']  = 'category_description';
    $options['value'] = $this->getVar('category_description', 'e');
    $ele              = new \XoopsFormEditor(_AM_ECATEGORY_TEXT, $xoopsUser->getVar('editor'), $options, $nohtml = false, $onfailure = 'textarea');
    $ele->setNocolspan(1);
    $form->addElement($ele);
} else {
    $category_description = new \XoopsFormDhtmlTextArea('', 'category_description', $this->getVar('category_description', 'e'), 15, 60);
    $form->addElement($category_description);
}
$category_image = new Xoopsforms\XoopsFormSelectImage(_AM_ECATEGORY_SELECTIMAGE, 'category_image', $this->getVar('category_image'), 'xoops_image', 1);
$category_image->setDescription(_AM_ECATEGORY_SELECTIMAGE_DSC);
$category_image->setCategory('images');
$form->addElement($category_image);

$form->addElement($buttons);
$form->endTab();

$form->startTab('Publication', 'xo_category-Publication');

$group_array = call_user_func([$categoryHandler, 'getRead_permissions'], $this, 'category_read');
$form->addElement(new \XoopsFormSelectGroup(_AM_ECATEGORY_RGRP, 'category_read', true, $group_array, 5, true), false);

$group_array = call_user_func([$categoryHandler, 'getRead_permissions'], $this, 'category_write');
$form->addElement(new \XoopsFormSelectGroup(_AM_ECATEGORY_WGRP, 'category_write', true, $group_array, 5, true), false);

/*Set display name*/
$category_weight = new \XoopsFormText(_AM_ECATEGORY_WEIGHT, 'category_weight', 3, 4, $this->getVar('category_weight', 'e'));
$category_weight->setDescription(_AM_ECATEGORY_WEIGHT_DSC);
$form->addElement($category_weight, true);
/*Set display name*/
$category_display = new \XoopsFormRadioYN(_AM_ECATEGORY_DISPLAY, 'category_display', $this->getVar('category_display'), ' ' . _YES, ' ' . _NO);
$category_display->setDescription(_AM_ECATEGORY_DISPLAY_DSC);
$form->addElement($category_display, false);

/*button_tray*/
$form->addElement($buttons);
$form->endTab();

$form->startTab('Image folder', 'xo_category-image');
$_files           = $this->isNew() ? '*1*' : $this->getVar('category_folders', 'e');
$category_folders = new Xoopsforms\XoopsFormSelectRDirList(_AM_ECATEGORY_SELECT, 'category_folders', $_files, 10, true, true);
$category_folders->setDescription(_AM_ECATEGORY_SELECT_DSC);
$form->addElement($category_folders, false);
$form->addElement($buttons);
$form->endTab();

$form->startTab('Metatags', 'category-metatags');
$options['rows'] = 5;
$options['cols'] = 75;

// Set display name
$category_metatitle = new \XoopsFormText(_AM_ECATEGORY_METATITLE, 'category_metatitle', 50, 60, $this->getVar('category_metatitle', 'e'));
$category_metatitle->setDescription(_AM_ECATEGORY_TITLE_DSC);
$form->addElement($category_metatitle, false);

$category_meta = new \XoopsFormTextArea(_AM_ECATEGORY_MDESCRIPTION, 'category_meta', $this->getVar('category_meta', 'e'), $rows = 5, $cols = 50);
$category_meta->setDescription(_AM_ECATEGORY_MDESCRIPTION_DSC);
$category_meta->setNocolspan(0);
$form->addElement($category_meta);

// $options['name'] = 'category_keywords';
// $options['value'] = $this->getVar( 'category_keywords', 'e' );
$category_keywords = new Xoopsforms\XoopsFormTextArea(_AM_ECATEGORY_MKEYWORDS, 'category_keywords', $this->getVar('category_keywords', 'e'), $rows = 5, $cols = 50);
$category_keywords->setDescription(_AM_ECATEGORY_MKEYWORDS_DSC);
$category_keywords->setNocolspan(0);
$form->addElement($category_keywords);

$form->addElement($buttons);
$form->endTab();
/**
 * Display form
 */
$form->display();
