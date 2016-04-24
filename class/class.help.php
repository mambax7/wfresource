<?php
/**
 * Name: class.help.php
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

/**
 * wpf_Help
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wpf_Help
{
    public $path;
    public $filename;

    /**
     * wpf_Help::wpf_Help()
     *
     * @internal param string $aboutTitle
     */
    public function __construct()
    {
    }

    /**
     * XoopsAbout::display()
     *
     */
    public function display()
    {
        $ts = MyTextSanitizer::getInstance();

        $contents        = '';
        $this->_path     = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/docs/';
        $this->_filename = 'help.txt';
        /**
         */
        $contents = '';
        if (file_exists($file = $this->_path . $this->_filename)) {
            $contents = file_get_contents($file);
        }
        echo $ts->displayTarea($contents);
    }
}
