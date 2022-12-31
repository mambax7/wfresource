<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

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

use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * mimetypeHandler
 *
 * @author    Catzwolf
 * @copyright Copyright (c) 2005
 */
class MimetypeHandler extends Wfresource\WfpObjectHandler
{
    /**
     * constructor
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'wfp_mimetypes', Mimetype::class, 'mime_id', 'mime_name', 'mime_read');
    }

    /**
     * mimetypeHandler::getInstance()
     *
     * @param \XoopsDatabase|null $db
     * @return MimetypeHandler
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
     * MimetypeHandler::getObj()
     * @return bool
     */
    public function getObj(...$args)
    {
        $obj = false;
        if (2 == \func_num_args()) {
//            $args     = \func_get_args();
            $criteria = new \CriteriaCompo();
            // if ($args[0]['search_text'] != '') {
            // $criteria->add( new \Criteria( $args[0]['search_by'], '%' . $args[0]['search_text'] . '%', 'LIKE' ) );
            // }
            // if ($args[0]['mime_safe'] == 0 || $args[0]['mime_safe'] == 1) {
            // $criteria->add ( new \Criteria( 'mime_safe', $args[0]['mime_safe'] ) );
            // }
            // if ($args[0]['mime_display'] == 0 || $args[0]['mime_display'] == 1) {
            // $criteria->add ( new \Criteria( 'mime_display', $args[0]['mime_display'] ) );
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
     * @param bool|false $value
     * @return bool
     */
//    public function &getMimeType(array $nav = null, $value = false)
    public function &getMimeType(...$args)
    {
        $obj = false;
        if (2 == \func_num_args()) {
//            $args     = \func_get_args();
            $criteria = new \CriteriaCompo();
            if ('' !== $args[0]['search_text']) {
                $criteria->add(new \Criteria($args[0]['search_by'], '%' . $args[0]['search_text'] . '%', 'LIKE'));
            }
            if (0 == $args[0]['mime_safe'] || 1 == $args[0]['mime_safe']) {
                $criteria->add(new \Criteria('mime_safe', (int)$args[0]['mime_safe']));
            }
            if (isset($args[0]['mime_category']) && 'all' !== $args[0]['mime_category']) {
                $criteria->add(new \Criteria('mime_category', $args[0]['mime_category']), 'LIKE');
            }
            if (isset($args[0]['alphabet']) && !empty($args[0]['alphabet'])) {
                $criteria->add(new \Criteria('mime_name', $args[0]['alphabet'] . '%', 'LIKE'));
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
     * @param string $gperm_name
     * @param int    $modid
     * @return array
     */
    public function &getMtypeArray($gperm_name = '', $modid = 1)
    {
        $ret        = $this->getList(null, '', null, false);
        $this_array = [];
        $new_array  = [];
        foreach ($ret as $k => $v) {
            $new_array  = \explode(' ', $v);
            $this_array = \array_merge($this_array, $new_array);
        }
        $ret = \array_unique($this_array);
        \sort($ret);

        return $ret;
    }

    /**
     * @param $filename
     */
    public function &ret_mime($filename): array
    {
        $ret    = [];
        $ext    = \pathinfo($filename, \PATHINFO_EXTENSION);
        $sql    = 'SELECT mime_name, mime_ext, mime_images, mime_category FROM ' . $this->db->prefix('wfp_mimetypes') . " WHERE mime_ext='" . \mb_strtolower($ext) . "' AND mime_display=1";
        $result = $this->db->query($sql);
        [$mime_types, $mime_ext, $mime_image] = $this->db->fetchRow($result);
        $mimetypes       = \explode(' ', \trim($mime_types));
        $ret['mimetype'] = $mimetypes[0];
        $ret['ext']      = $mime_ext;
        $ret['image']    = $mime_image;

        return $ret;
    }

    public function &mimetypeArray(): array
    {
        $ret    = [];
        $sql    = 'SELECT mime_name, mime_ext, mime_images, mime_category FROM ' . $this->db->prefix('wfp_mimetypes');
        $result = $this->db->query($sql);
        if ($this->db->isResultSet($result)) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $_image                  = (isset($myrow['mime_images'])
                                            && !empty($myrow['mime_images'])) ? $myrow['mime_images'] : 'default.png';
                $ret[$myrow['mime_ext']] = [
                    'mime_name'     => $myrow['mime_name'],
                    'mime_images'   => $_image,
                    'mime_category' => $this->mimeCategory($myrow['mime_category']),
                ];
            } // while
        }

        return $ret;
    }

    /**
     * @param null $do_select
     */
    public function &mimeCategory($do_select = null): array
    {
        $ret = [
            'all'      => \_AM_MIME_ALLCAT,
            'unknown'  => \_AM_MIME_CUNKNOWN,
            'archive'  => \_AM_MIME_CARCHIVES,
            'audio'    => \_AM_MIME_CAUDIO,
            'text'     => \_AM_MIME_CTEXT,
            'document' => \_AM_MIME_CDOCUMENT,
            'help'     => \_AM_MIME_CHELP,
            'source'   => \_AM_MIME_CSOURCE,
            'video'    => \_AM_MIME_CVIDEO,
            'html'     => \_AM_MIME_CHTML,
            'graphic'  => \_AM_MIME_CGRAPHICS,
            'midi'     => \_AM_MIME_CMIDI,
            'binary'   => \_AM_MIME_CBINARY,
        ];
        if ($do_select) {
            return $ret[$do_select];
        }

        return $ret;
    }

    /**
     * @param $image
     */
    public function mimetypeImage($image): string
    {
        $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
        $ret     = [];
        $ext     = \pathinfo($image, \PATHINFO_EXTENSION);
        $sql     = 'SELECT mime_images FROM ' . $xoopsDB->prefix('wfp_mimetypes') . " WHERE mime_ext LIKE '" . \mb_strtolower($ext) . "'";
        $result  = $xoopsDB->query($sql);
        [$mime_images] = $xoopsDB->fetchRow($result);
        if (!$mime_images) {
            $mime_images = 'unknown.png';
        }

        return XOOPS_URL . '/images/mimetypes/' . $mime_images;
    }

    /**
     * @param $image
     */
    public function mimeImage($image): string
    {
        if ($image) {
            $file = XOOPS_ROOT_PATH . '/images/mimetypes/' . $image;
            $name = \pathinfo($file, \PATHINFO_BASENAME);
        } else {
            $name = 'unknown.png';
        }

        return XOOPS_URL . '/images/mimetypes/' . $name;
    }

    /**
     * @param $fileext
     */
    public function open_url($fileext): void
    {
        echo "<meta http-equiv=\"refresh\" content=\"0;url=https://filext.com/detaillist.php?extdetail=$fileext\">\r\n";
    }

    public function getAlphabet(): string
    {
        $ret = '';
        for ($i = 65; $i <= 90; ++$i) {
            $aplha       = \chr($i);
            $ret[$aplha] = $aplha;
        }

        return $ret;
    }

    /**
     * PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count): void
    {
        /**
         * bad bad bad!! Need to change this
         */ global $list_array, $nav;
        $safe_array = ['3' => \_AM_SHOWSAFEALL_BOX, '0' => \_AM_SHOWSAFENOT_BOX, '1' => \_AM_SHOWSAFEIS_BOX];

        $ret      = '<div style="padding-bottom: 8px;">';
        $ret      .= '<form><div style="text-align: left; margin-bottom: 12px;">
         <input type="button" name="button" onclick=\'location="admin.mimetype.php?op=edit"\' value="' . \_AM_WFP_CREATENEW . '">
         <input type="button" name="button" onclick=\'location="admin.mimetype.php?op=permissions"\' value="' . \_AM_WFP_PERMISSIONS . '">
        </div></form>';
        $onchange = "onchange=\"location='admin.mimetype.php?%s='+this.options[this.selectedIndex].value\"";
        $ret      .= "<div>
            <span style='float: left;'>" . Utility::getSelection($this->mimeCategory(), $nav['mime_category'], 'mime_category', 1, false, false, false, \sprintf($onchange, 'mime_category'), 0, false) . "</span>
            <span style='float: left;'>&nbsp;" . Utility::getSelection($safe_array, $nav['mime_safe'], 'mime_safe', 1, false, false, false, \sprintf($onchange, 'mime_safe'), 0, false) . "</span>
            <span style='float: left;'>&nbsp;" . Utility::getSelection($this->getAlphabet(), $nav['alphabet'], 'alphabet', 1, 1, false, false, \sprintf($onchange, 'alphabet'), 0, false) . "</span>
            <span style='float: right;'>" . \_AM_WFP_DISPLAYAMOUNT_BOX . Utility::getSelection($list_array, $nav['limit'], 'limit', 1, 0, false, false, \sprintf($onchange, 'limit'), 0, false) . "</span>
        </div><br clear='all'><br>";
        echo $ret;
    }
}
