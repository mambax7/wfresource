<?php

/**
 *
 * @version   $Id: form_wfp_mimetype.php 8181 2011-11-07 01:14:53Z beckmi $
 * @copyright 2007
 */
require XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader.php';

global $modversion, $xoopsConfigUser;

$mime_handler = &wfp_gethandler('mimetype');

$form = new XoopsThemeForm($forminfo = ($this->isNew()) ? _AM_MIME_CREATEF : _AM_MIME_MODIFYF, 'mimetype_form', $modversion['adminpath']);
$form->addElement(new XoopsFormText(_AM_MIME_EXTF, 'mime_ext', 5, 60, $this->getVar('mime_ext', 'e')), true);
$form->addElement(new XoopsFormText(_AM_MIME_NAMEF, 'mime_name', 50, 60, $this->getVar('mime_name', 'e')), true);

$textarea = new XoopsFormTextArea(_AM_MIME_TYPEF, 'mime_types', $this->getVar('mime_types', 'e'), 15, 70);
$textarea->setDescription('<span style="font-size: x-small; font-weight: normal;">' . _AM_MIME_USEFULTAGS . '</span></span>');
$form->addElement($textarea, true);

$mimetype_image_dir = new XoopsFormSelectImage(_AM_MIME_EIMAGE, 'mime_images', $this->getVar('mime_images'), $id = 'xoops_image', 0);
$mimetype_image_dir->setDescription(_AM_MIME_EIMAGE_DSC);
$mimetype_image_dir->setCategory('/modules/wfresource/images/mimetypes');
$form->addElement($mimetype_image_dir);
/*
        $mimetype_tray = new XoopsFormElementTray( _AM_EAVATAR_FILE, '&nbsp;' );
        $mimetype_tray->setDescription( sprintf( _AM_EAVATAR_FILE_DSC, $xoopsConfigUser['avatar_maxsize'], $xoopsConfigUser['avatar_width'], $xoopsConfigUser['avatar_height'] ) );
        $mimetype_tray->addElement( new XoopsFormFile( '', 'upload_file', $xoopsConfigUser['avatar_maxsize'] ) );
        $form->addElement( $mimetype_tray );
        */
$mime_category = new XoopsFormSelect(_AM_MIME_ECATEGORY, 'mime_category', $this->getVar('mime_category'));
$mime_category->setDescription(_AM_MIME_ECATEGORY_DSC);
$cat = &$mime_handler->mimeCategory();
unset($cat['all']);
$mime_category->addOptionArray($cat);
$form->addElement($mime_category);

$mime_safe = new XoopsFormRadioYN(_AM_MIME_ESAFE, 'mime_safe', $this->getVar('mime_safe'), ' ' . _AM_MIME_YSAFE . '', ' ' . _AM_MIME_YUNSAFE . '');
$mime_safe->setDescription(_AM_MIME_ESAFE_DSC);
$form->addElement($mime_safe);

/*Set display name*/
$mime_display = new XoopsFormRadioYN(_AM_MIME_EACTIVATE, 'mime_display', $this->getVar('mime_display'), ' ' . _YES . '', ' ' . _NO . '');
$mime_display->setDescription(_AM_MIME_EACTIVATE_DSC);
$form->addElement($mime_display);
$form->addElement(new XoopsFormHidden('op', 'save'));
$form->addElement(new XoopsFormHidden('mime_id', $this->getVar('mime_id')));
$form->addElement(new XoopsFormButtontray('submit', _SUBMIT));
$form->display();
