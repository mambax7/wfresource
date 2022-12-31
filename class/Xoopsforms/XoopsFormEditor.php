<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

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
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/**
 * @author        Kazumi Ono    <onokazu@xoops.org>
 * @copyright     copyright (c) 2000-2003 XOOPS.org
 */

/**
 * XoopsEditor handler
 *
 * @author       D.J.
 * @copyright    copyright (c) 2000-2005 XOOPS.org
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
        $editorHandler = new \XoopsEditorHandler();
        $this->editor  = $editorHandler->get($name, $editor_configs, $noHtml, $OnFailure);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->editor->render();
    }
}
