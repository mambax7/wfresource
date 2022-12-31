<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

use MyTextSanitizer;

/**
 * Name: class.help.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * Help
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Help
{
    public $path;
    public $filename;

    /**
     * Wfresource\Help::__construct()
     *
     * @internal param string $aboutTitle
     */
    public function __construct()
    {
    }

    /**
     * XoopsAbout::display()
     */
    public function display(): void
    {
        $ts = MyTextSanitizer::getInstance();

        $contents        = '';
        $this->_path     = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/docs/';
        $this->_filename = 'help.txt';

        $contents = '';
        if (\file_exists($file = $this->_path . $this->_filename)) {
            $contents = file_get_contents($file);
        }
        echo $ts->displayTarea($contents);
    }
}
