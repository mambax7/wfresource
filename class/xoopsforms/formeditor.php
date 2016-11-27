<?php
// $Id: formeditor.php 8181 2011-11-07 01:14:53Z beckmi $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 *
 *
 * @package       kernel
 * @subpackage    form
 *
 * @author        Kazumi Ono    <onokazu@xoops.org>
 * @copyright     copyright (c) 2000-2003 XOOPS.org
 */

/**
 * XoopsEditor hanlder
 *
 * @author       D.J.
 * @copyright    copyright (c) 2000-2005 XOOPS.org
 *
 * @package      kernel
 * @subpackage   form
 */
class XoopsFormEditor extends XoopsFormTextArea
{
    public $editor;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param null   $editor_configs
     * @param bool   $noHtml    use non-WYSIWYG eitor onfailure
     * @param string $OnFailure editor to be used if current one failed
     */
    public function __construct($caption, $name, $editor_configs = null, $noHtml = false, $OnFailure = '')
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php';
        parent::__construct($caption, $editor_configs['name']);
        $editorHandler = new XoopsEditorHandler();
        $this->editor   = $editorHandler->get($name, $editor_configs, $noHtml, $OnFailure);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->editor->render();
    }
}
