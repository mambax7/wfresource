<?php
// $Id: class.mimetype.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// wfp_ - PHP Content Management System                                 //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');
wfp_getObjectHandler();

/**
 * Xoops Mimetype Class
 *
 * @package       kernel
 * @author        John Neill AKA Catzwolf
 * @copyright (c) 2006 Xoops
 */
class wfp_Mimetype extends wfp_Object
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('mime_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mime_ext', XOBJ_DTYPE_TXTBOX, null, true, 10);
        $this->initVar('mime_name', XOBJ_DTYPE_TXTBOX, null, true, 60);
        $this->initVar('mime_types', XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('mime_images', XOBJ_DTYPE_TXTBOX, null, true, 120);
        $this->initVar('mime_safe', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('mime_category', XOBJ_DTYPE_TXTBOX, 'unknown', true, 10);
        $this->initVar('mime_display', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return ($this->getVar('mime_id') === -1);
    }

    /**
     * @param $Handler
     * @return mixed
     */
    public function mimeCategory($Handler)
    {
        $haystack =& $Handler->mimeCategory();
        $needle   = $this->getVar('mime_category');
        if (isset($haystack[$needle])) {
            return $haystack[$needle];
        } else {
            return $haystack['unknown'];
        }
    }

    /**
     * @return string
     */
    public function mimeSafe()
    {
        $ret = $this->getVar('mime_safe') ? wfp_showImage('safe') : wfp_showImage('unsafe');

        return $ret;
    }
}

/**
 * mimetypeHandler
 *
 * @package
 * @author    Catzwolf
 * @copyright Copyright (c) 2005
 * @access    public
 */
class wfp_MimetypeHandler extends wfp_ObjectHandler
{
    /**
     * constructor
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'wfp_mimetypes', 'wfp_Mimetype', 'mime_id', 'mime_name', 'mime_read');
    }

    /**
     * mimetypeHandler::getInstance()
     *
     * @param $db
     * @return wfp_MimetypeHandler
     */
    public function getInstance($db)
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * wfp_MimetypeHandler::getObj()
     * @return bool
     */
    public function getObj()
    {
        $obj = false;
        if (func_num_args() == 2) {
            $args     = func_get_args();
            $criteria = new CriteriaCompo();
            // if ($args[0]['search_text'] != '') {
            // $criteria->add( new Criteria( $args[0]['search_by'], '%' . $args[0]['search_text'] . '%', 'LIKE' ) );
            // }
            // if ($args[0]['mime_safe'] == 0 || $args[0]['mime_safe'] == 1) {
            // $criteria->add ( new Criteria( 'mime_safe', $args[0]['mime_safe'] ) );
            // }
            // if ($args[0]['mime_display'] == 0 || $args[0]['mime_display'] == 1) {
            // $criteria->add ( new Criteria( 'mime_display', $args[0]['mime_display'] ) );
            // }
            $obj['count'] = $this->getCount($criteria);
            if (!empty($args[0])) {
                $criteria->setSort($args[0]['sort']);
                $criteria->setOrder($args[0]['order']);
                $criteria->setStart($args[0]['start']);
                $criteria->setLimit($args[0]['limit']);
            }
            $obj['list'] = $this->getObjects($criteria, $args[1]);
        }

        return $obj;
    }

    /**
     * @param  array      $nav
     * @param  bool|false $value
     * @return bool
     */
    public function &getMimeType(array $nav = null, $value = false)
    {
        $obj = false;
        if (func_num_args() == 2) {
            $args     = func_get_args();
            $criteria = new CriteriaCompo();
            if ($args[0]['search_text'] !== '') {
                $criteria->add(new Criteria($args[0]['search_by'], '%' . $args[0]['search_text'] . '%', 'LIKE'));
            }
            if ($args[0]['mime_safe'] == 0 || $args[0]['mime_safe'] == 1) {
                $criteria->add(new Criteria('mime_safe', (int)$args[0]['mime_safe']));
            }
            if (isset($args[0]['mime_category']) && $args[0]['mime_category'] !== 'all') {
                $criteria->add(new Criteria('mime_category', $args[0]['mime_category']), 'LIKE');
            }
            if (isset($args[0]['alphabet']) && !empty($args[0]['alphabet'])) {
                $criteria->add(new Criteria('mime_name', $args[0]['alphabet'] . '%', 'LIKE'));
            }
            $obj['count'] = $this->getCount($criteria);
            if (!empty($args[0])) {
                if ($obj['count'] <= $args[0]['start']) {
                    $args[0]['start'] = 0;
                }
                $criteria->setSort($args[0]['sort']);
                $criteria->setOrder($args[0]['order']);
                $criteria->setStart($args[0]['start']);
                $criteria->setLimit($args[0]['limit']);
            }
            $obj['list'] = $this->getObjects($criteria, $args[1]);
        }

        return $obj;
    }

    /**
     * @param  string $gperm_name
     * @param  int    $modid
     * @return array|bool
     */
    public function &getMtypeArray($gperm_name = '', $modid = 1)
    {
        $ret        = $this->getList(null, '', null, false);
        $this_array = [];
        $new_array  = [];
        foreach ($ret as $k => $v) {
            $new_array  = explode(' ', $v);
            $this_array = array_merge($this_array, $new_array);
        }
        $ret = array_unique($this_array);
        sort($ret);

        return $ret;
    }

    /**
     * @param $filename
     * @return array
     */
    public function &ret_mime($filename)
    {
        $ret    = [];
        $ext    = pathinfo($filename, PATHINFO_EXTENSION);
        $sql    = 'SELECT mime_name, mime_ext, mime_images, mime_category FROM ' . $this->db->prefix('wfp_mimetypes') . " WHERE mime_ext='" . strtolower($ext) . "' AND mime_display=1";
        $result = $this->db->query($sql);
        list($mime_types, $mime_ext, $mime_image) = $this->db->fetchrow($result);
        $mimetypes       = explode(' ', trim($mime_types));
        $ret['mimetype'] = $mimetypes[0];
        $ret['ext']      = $mime_ext;
        $ret['image']    = $mime_image;

        return $ret;
    }

    /**
     * @return array
     */
    public function &mimetypeArray()
    {
        $ret    = [];
        $sql    = 'SELECT mime_name, mime_ext, mime_images, mime_category FROM ' . $this->db->prefix('wfp_mimetypes');
        $result = $this->db->query($sql);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $_image                  = (isset($myrow['mime_images'])
                                        && !empty($myrow['mime_images'])) ? $myrow['mime_images'] : 'default.png';
            $ret[$myrow['mime_ext']] = [
                'mime_name'     => $myrow['mime_name'],
                'mime_images'   => $_image,
                'mime_category' => $this->mimeCategory($myrow['mime_category'])
            ];
        } // while

        return $ret;
    }

    /**
     * @param  null $do_select
     * @return array
     */
    public function &mimeCategory($do_select = null)
    {
        $ret = [
            'all'      => _AM_MIME_ALLCAT,
            'unknown'  => _AM_MIME_CUNKNOWN,
            'archive'  => _AM_MIME_CARCHIVES,
            'audio'    => _AM_MIME_CAUDIO,
            'text'     => _AM_MIME_CTEXT,
            'document' => _AM_MIME_CDOCUMENT,
            'help'     => _AM_MIME_CHELP,
            'source'   => _AM_MIME_CSOURCE,
            'video'    => _AM_MIME_CVIDEO,
            'html'     => _AM_MIME_CHTML,
            'graphic'  => _AM_MIME_CGRAPHICS,
            'midi'     => _AM_MIME_CMIDI,
            'binary'   => _AM_MIME_CBINARY
        ];
        if ($do_select) {
            return $ret[$do_select];
        }

        return $ret;
    }

    /**
     * @param $image
     * @return string
     */
    public function mimetypeImage($image)
    {
        $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
        $ret     = [];
        $ext     = pathinfo($image, PATHINFO_EXTENSION);
        $sql     = 'SELECT mime_images FROM ' . $xoopsDB->prefix('wfp_mimetypes') . " WHERE mime_ext LIKE '" . strtolower($ext) . "'";
        $result  = $xoopsDB->query($sql);
        list($mime_images) = $xoopsDB->fetchrow($result);
        if (!$mime_images) {
            $mime_images = 'unknown.png';
        }

        return XOOPS_URL . '/images/mimetypes/' . $mime_images;
    }

    /**
     * @param $image
     * @return string
     */
    public function mimeImage($image)
    {
        if ($image) {
            $file = XOOPS_ROOT_PATH . '/images/mimetypes/' . $image;
            $name = pathinfo($file, PATHINFO_BASENAME);
        } else {
            $name = 'unknown.png';
        }

        return XOOPS_URL . '/images/mimetypes/' . $name;
    }

    /**
     * @param $fileext
     */
    public function open_url($fileext)
    {
        echo "<meta http-equiv=\"refresh\" content=\"0;url=http://filext.com/detaillist.php?extdetail=$fileext\">\r\n";
    }

    /**
     * @return string
     */
    public function getAlphabet()
    {
        $ret = '';
        for ($i = 65; $i <= 90; ++$i) {
            $aplha       = chr($i);
            $ret[$aplha] = $aplha;
        }

        return $ret;
    }

    /**
     * wfc_PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count)
    {
        /**
         * bad bad bad!! Need to change this
         */
        global $list_array, $nav;
        $safe_array = ['3' => _AM_SHOWSAFEALL_BOX, '0' => _AM_SHOWSAFENOT_BOX, '1' => _AM_SHOWSAFEIS_BOX];

        $ret      = '<div style="padding-bottom: 8px;">';
        $ret      .= '<form><div style="text-align: left; margin-bottom: 12px;">
         <input type="button" name="button" onclick=\'location="admin.mimetype.php?op=edit"\' value="' . _AM_WFP_CREATENEW . '">
         <input type="button" name="button" onclick=\'location="admin.mimetype.php?op=permissions"\' value="' . _AM_WFP_PERMISSIONS . '">
        </div></form>';
        $onchange = "onchange=\"location='admin.mimetype.php?%s='+this.options[this.selectedIndex].value\"";
        $ret      .= "<div>
            <span style='float: left;'>" . wfp_getSelection($this->mimeCategory(), $nav['mime_category'], 'mime_category', 1, false, false, false, sprintf($onchange, 'mime_category'), 0, false) . "</span>
            <span style='float: left;'>&nbsp;" . wfp_getSelection($safe_array, $nav['mime_safe'], 'mime_safe', 1, false, false, false, sprintf($onchange, 'mime_safe'), 0, false) . "</span>
            <span style='float: left;'>&nbsp;" . wfp_getSelection($this->getAlphabet(), $nav['alphabet'], 'alphabet', 1, 1, false, false, sprintf($onchange, 'alphabet'), 0, false) . "</span>
            <span style='float: right;'>" . _AM_WFP_DISPLAYAMOUNT_BOX . wfp_getSelection($list_array, $nav['limit'], 'limit', 1, 0, false, false, sprintf($onchange, 'limit'), 0, false) . "</span>
        </div><br clear='all'><br>";
        echo $ret;
    }
}
