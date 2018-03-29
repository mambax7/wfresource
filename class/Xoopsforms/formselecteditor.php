<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/**
 * base class
 */
//require_once __DIR__."/formelementtray.php";
require_once XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php';

/**
 * A select box with available editors
 *
 * @package       kernel
 * @subpackage    form
 *
 * @author        phppp (D.J.)
 * @copyright     copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormSelectEditor extends XoopsFormElementTray
{
    /**
     * Constructor
     *
     * @param object $form   the form calling the editor selection
     * @param string $name   editor name
     * @param string $value  Pre-selected text value
     * @param bool   $noHtml dohtml disabled
     */
    public function __construct($form, $name = 'editor', $value = null, $noHtml = false)
    {
        parent::__construct(_SELECT);

        $editorHandler = new \XoopsEditorHandler();
        $option_select = new \XoopsFormSelect('', $name, $value);
        $extra         = 'onchange="if (this.options[this.selectedIndex].value.length > 0) {
            window.document.forms.' . $form->getName() . '.submit();
            }"';
        $option_select->setExtra($extra);
        $option_select->addOptionArray($editorHandler->getList($noHtml));

        $this->addElement($option_select);
    }
}
