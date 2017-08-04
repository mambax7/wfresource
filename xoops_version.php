<?php
/**
 * Name: xoops_version.php
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
$moduleDirName = basename(__DIR__);
$modversion['version']       = 1.07;
$modversion['module_status'] = 'Final';
$modversion['release_date']  = '2016/11/26';
$modversion['name']          = _MD_WF_RESOURCE;
$modversion['description']   = _MD_WF_RESOURCE_DSC;
$modversion['author']        = 'Catzwolf';
$modversion['credits']       = 'Mark Boyden, Mamba';
$modversion['releasedate']   = 'NOT RELEASED';
$modversion['status']        = '1.07 Beta 1';
$modversion['help']          = '';
$modversion['license']       = 'GPL see LICENSE';
$modversion['official']      = 0;
$modversion['dirname']       = $moduleDirName;
$modversion['image']         = 'images/logoModule.png';
// Admin things
$modversion['hasAdmin']                    = 0;
$modversion['templates'][1]['file']        = 'wfp_addto.tpl';
$modversion['templates'][1]['description'] = 'Displays an AddTo bar';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = array(
    ['name' => _MI_WF_RESOURCE_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_WF_RESOURCE_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_WF_RESOURCE_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_WF_RESOURCE_SUPPORT, 'link' => 'page=support'],
);
