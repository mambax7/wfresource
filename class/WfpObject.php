<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * WfpObject
 *
 * @author    John
 * @copyright Copyright (c) 2007
 */
\define('XOBJ_DTYPE_BOOL', 12);

/**
 * Class WfpObject
 */
class WfpObject extends \XoopsObject
{
    /**
     * WfpObject::__construct()
     */
    public function __construct()
    {
    }

    /**
     * WfpObject::formEdit()
     *
     * @param mixed $value
     * @return bool
     */
    public function formEdit($value = null)
    {
        //require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        //        $module_prefix = mb_substr(\get_class($this), 0, 3);

        $parts     = \explode('\\', $value);
        $className = \end($parts);

        $module_prefix  = mb_substr(ClassName::short(\get_class($this)), 0, 3);
        $classNamespace = ClassName::namespace($this);
        $module_prefix2 = mb_substr(ClassName::short(\get_parent_class($this)), 0, 3);

        if ('Wfp' === $module_prefix) {
            $file = XOOPS_ROOT_PATH . '/modules/wfresource/class/Classforms/form_' . \mb_strtolower($value) . '.php';
        } else {
            $file = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/class/Classforms/form_' . \mb_strtolower($className) . '.php';
            $file2 = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/class/Classforms/Form' . $className . '.php';
        }

        //        $file = XOOPS_ROOT_PATH . '/modules/wfresource/class/Classforms/form_' . \mb_strtolower($value) . '.php';

        if (\is_file($file)) {
            require_once $file;
        } else {
            \trigger_error("Error: Form for $value not found");

            return false;
        }
    }

    /**
     * WfpObject::getTimestamp()
     *
     * @param         $value
     * @param string  $timestamp
     */
    public function getTimestamp($value, $timestamp = ''): string
    {
        if (!$this->getVar($value)) {
            return '';
        }

        return \formatTimestamp($this->getVar($value), $timestamp);
    }

    /**
     * WfpObject::getUerName()
     *
     * @param mixed  $value
     * @param string $timestamp
     * @param bool   $usereal
     * @param bool   $linked
     * @return string
     */
    public function getUserName($value, $timestamp = '', $usereal = false, $linked = true)
    {
        if (!\class_exists('XoopsLoad')) {
            require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';
        }

        \XoopsLoad::load('xoopsuserutility');

        return \XoopsUserUtility::getUnameFromId($this->getVar($value), $usereal, $linked);
    }

    /**
     * WfpObject::getUserID()
     *
     * @param mixed  $value
     * @param string $timestamp
     * @param mixed  $usereal
     * @param mixed  $linked
     * @return int|mixed
     */
    public function getUserID($value, $timestamp = '', $usereal = false, $linked = false)
    {
        if (!$this->getVar($value)) {
            return \is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getVar('uid') : 0;
        }

        return $this->getVar($value);
    }

    /**
     * WfpObject::getTextbox()
     *
     * @param mixed $id
     * @param mixed $name
     * @param int   $size
     * @param mixed $max
     */
    public function getTextbox($id = null, $name = null, $size = 25, $max = 255): string
    {
        $ret = '<input type="text" name="' . $name . '[' . $this->getVar($id) . ']" value="' . $this->getVar($name) . '" size="' . $size . '" maxlength="' . $max . '">';

        return $ret;
    }

    /**
     * Page::getYesNobox()
     *
     * @param mixed $id
     * @param mixed $name
     * @param mixed $value
     */
    public function getYesNobox($id = null, $name = null, $value = null): string
    {
        $i        = $this->getVar($id);
        $ret      = "<input type='radio' name='" . $name . '[' . $i . "]' value='1'";
        $selected = $this->getVar($name);
        if (null !== $selected && (1 === $selected)) {
            $ret .= ' checked';
        }
        $ret      .= '>' . _YES . "\n";
        $ret      .= "<input type='radio' name='" . $name . '[' . $i . "]' value='0'";
        $selected = $this->getVar($name);
        if (null !== $selected && (0 === $selected)) {
            $ret .= ' checked';
        }
        $ret .= '>' . _NO . "\n";

        return $ret;
    }

    /**
     * XoopsObject::getCheckbox()
     *
     * @param mixed $id
     */
    public function getCheckbox($id = null): string
    {
        $ret = '<input type="checkbox" value="1" name="checkbox[' . $this->getVar($id) . ']">';

        return $ret;
    }

    /**
     * Display a human readable date form
     * @param null   $_time
     * @param string $format
     * @param string $err
     */
    public function formatTimeStamp($_time = null, $format = 'D, M-d-Y', $err = '-------------'): string
    {
        if (\is_string($_time) && 'today' !== $_time) {
            $_time = $this->getVar($_time, 'e');
        } elseif (\is_numeric($_time)) {
            $_time = $_time;
        } elseif ('today' === $_time) {
            $_time = \time();
        }
        $ret = $_time ? \formatTimestamp($_time, $format) : $err;

        return $ret;
    }

    /**
     * Page::toArray()
     * @return array
     */
    public function toArray()
    {
        $ret  = [];
        $vars = &$this->getVars();
        foreach (\array_keys($vars) as $i) {
            $ret[$i] = $this->getVar($i);
        }

        return $ret;
        //        unset($ret);
    }

    /**
     * WfpObject::getImage()
     *
     * @param mixed  $value
     * @param string $imagedir
     * @return bool
     */
    public function getImage($value, $imagedir = '')
    {
        if (empty($value) || empty($imagedir)) {
            // trigger_error( 'Required value missing' );
            return false;
        }

        $cleani       = \explode('|', $this->getVar($value));
        $orginalImage = \is_array($cleani) ? $cleani[0] : $this->getVar($value);
        if (!empty($orginalImage) && \preg_match('/^blank\./', $orginalImage)) {
            return false;
        }

        $imageArray      = \explode('|', $this->getVar($value));
        $image['image']  = $imageArray[0] ?? $orginalImage;
        $image['width']  = !empty($imageArray[1]) ? $imageArray[1] : 0;
        $image['height'] = !empty($imageArray[2]) ? $imageArray[2] : 0;
        $image['url']    = XOOPS_URL . '/' . $imagedir . '/' . $image['image'];
        $imageFile       = XOOPS_ROOT_PATH . '/' . $imagedir . '/' . $image['image'];
        if (!\is_dir($imageFile) || \is_dir($imageFile)) {
            unset($image, $orginalImage, $cleani, $imagedir, $value);

            return false;
        }
        if (0 === $image['width'] || 0 === $image['height']) {
            $image_details   = \getimagesize($imageFile);
            $image['width']  = $image_details[0];
            $image['height'] = $image_details[1];
        }

        return $image;
    }

    /**
     * WfpObject::getImageEdit()
     *
     * @param mixed $value
     * @return bool
     */
    public function getImageEdit($value)
    {
        $cleani       = \explode('|', $this->getVar($value)??'');
        $orginalImage = \is_array($cleani) ? $cleani[0] : $this->getVar($value);
        if (!empty($orginalImage) && \preg_match('/^blank\./', $orginalImage)) {
            return false;
        }

        $imageArray      = \explode('|', $this->getVar($value)??'');
        $image['image']  = $imageArray[0] ?? $orginalImage;
        $image['width']  = !empty($imageArray[1]) ? $imageArray[1] : 0;
        $image['height'] = !empty($imageArray[2]) ? $imageArray[2] : 0;

        return $image;
    }
}
