<?php
// $Id: formselectimg.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
xoops_load('XoopsFormSelect');

/**
 * A select field
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.Xoops.com
 */
class XoopsFormSelectImage extends XoopsFormSelect
{
    public $_name;
    public $_value = array();
    public $_id;
    public $_imgcat_id;

    public $_category = "uploads";
    public $_options  = array();
    public $_multiple = false;
    public $_size     = 10;

    /**
     * Constructor
     *
     * @param string $caption  Caption
     * @param string $name     "name" attribute
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list
     * @param bool   $multiple Allow multiple selections?
     */
    public function __construct($caption, $name, $value = null, $id = 'Xoops_image', $imgcat_id, $size = 5)
    {
        $this->setCaption($caption);
        $this->_name = $name;
        if (isset($value)) {
            $this->setValue($value);
        }
        $this->_id        = $id;
        $this->_imgcat_id = $imgcat_id;
        $this->_size      = $size;
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the name
     *
     * @return int
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    public function setCategory($value)
    {
        return $this->_category = $value;
    }

    /**
     * Set Category
     *
     * @return int
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * Set Category
     *
     * @return int
     */
    public function setDir($value)
    {
        return $this->_dir = $value;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    public function getImgcat_id()
    {
        return $this->_imgcat_id;
    }

    /**
     * Set pre-selected values
     *
     * @param  $value mixed
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * XoopsFormSelectImg::getImage()
     *
     * @return
     */
    public function getImage()
    {
        $value = $this->getValue();
        $image = explode('|', $value);

        return (is_array($image)) ? $image : $value;
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute
     * @param string $name  "name" attribute
     */
    public function addOption($value, $name = "")
    {
        if ($name != "") {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple options
     *
     * @param array $options Associative array of value->name pairs
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get all options
     *
     * @return array Associative array of value->name pairs
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/include/js/wfpimage.js.php';
        if ($this->_imgcat_id > 0 && $useimagemanger = 0) {
            $image_handler  = &Xoops_gethandler('image');
            $imgcat_handler = &Xoops_gethandler('imagecategory');
            $image_cat_obj  = $imgcat_handler->get($this->_imgcat_id);
            if ($image_cat_obj) {
                $art_image_array = $image_handler->getList($this->_imgcat_id, null, 'image');
                $this->setCategory($image_cat_obj->getVar('imgcat_dirname'));
            } else {
                $art_image_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . DS . $this->getCategory());
            }
        } else {
            $art_image_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . DS . $this->getCategory());
        }

        $image_array = array();
        if ($this->getValue()) {
            $image_array = explode('|', $this->getValue());
            if (count($image_array) == 1) {
                $image_size     = @getimagesize(XOOPS_ROOT_PATH . DS . $this->getCategory() . DS . $this->getValue());
                $image_array[0] = $this->getValue();
                $image_array[1] = ($image_size[0] > 300) ? '300' : $image_size[0];
                $image_array[2] = ($image_size[1] > 250) ? '250' : $image_size[1];
            } else {
                if ($image_array[1] == 0 || $image_array[2] == 0) {
                    $image_size     = @getimagesize(XOOPS_ROOT_PATH . DS . $this->getCategory() . DS . $this->getValue());
                    $image_array[1] = ($image_size[0] > 300) ? '300' : $image_size[0];
                    $image_array[2] = ($image_size[1] > 250) ? '250' : $image_size[1];
                }
            }
        } else {
            $this->setValue('');
            $image_array[0] = '';
            $image_array[1] = 0;
            $image_array[2] = 0;
        }

        $ret = "<table border='0' width='100%' cellspacing='0' cellpadding='0'>\n<tr>\n<td style=\"vertical-align: top;\">";
        $ret .= "<select size='" . $this->getSize() . "'" . $this->getExtra();
        if ($this->isMultiple() != false) {
            $ret .= " name='" . $this->getName() . "[]' id='" . $this->getName() . "[]' multiple='multiple' ";
        } else {
            $ret .= " name='" . $this->getName() . "' id='" . $this->getName() . "' ";
        }
        /**
         */
        $ret .= " onchange='chooseImage(this, \"" . $this->_id . "\", \"" . XOOPS_URL . '/' . $this->getCategory() . "\", \"\")'>";
        $result = array_merge(array('' => _AM_WFP_NOSELECTION), $art_image_array);
        foreach ($result as $value => $name) {
            $image_name  = explode('.', $name);
            $imagesize2  = @getimagesize(XOOPS_ROOT_PATH . DS . $this->getCategory() . DS . $value);
            $imagewidth  = ($imagesize2[0] > 300) ? '300' : $imagesize2[0];
            $imageheight = ($imagesize2[1] > 250) ? '250' : $imagesize2[1];
            unset($imagesize);

            $ret .= "<option value='" . htmlspecialchars($value, ENT_QUOTES) . "|$imagewidth|$imageheight'";
            if (!$value || !isset($value) || empty($value)) {
                $value = '';
            }
            if (trim($value) == $image_array[0]) {
                $ret .= " selected='selected'";
            }
            $ret .= ">" . $image_name[0] . "</option>\n";
        }
        /**
         */
        $image         = $image_array[0];
        $image_display = XOOPS_URL . '/' . $this->getCategory() . '/' . $image_array[0];
        $ret .= "	</select></td><td width='100%' style='padding-left: 1%;'>
            <div id=\"" . $this->_id . "\" style=\"padding: 5px; text-align: center; \">
              <img src='" . XOOPS_URL . "/" . $this->getCategory() . "/" . $image_array[0] . "' onclick='openWithSelfMain(\"" . XOOPS_URL . "/" . $this->getCategory() . "/" . $image_array[0] . "\",\"image\" );' align='absmiddle' width='{$image_array[1]}' height='{$image_array[2]}' />
             </div>
            </td>
           </tr>
          </table>";

        return $ret;
    }
}
