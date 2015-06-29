<?php
/**
 * Name: admin.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: admin.php 10055 2012-08-11 12:46:10Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access');

define('_MA_WFP_ADMINPREFS','Preferences');
define('_MA_WFP_ADMININDEX','Admin Index');
define('_MA_WFP_PREFS','Preferences');
define('_MA_WFP_MODULEHOME','Module Home');
define('_MA_WFP_MODULEABOUT','About');
define('_MA_WFP_MODULEHELP','Help');
define('_MA_WFP_ADMINCOMMENTS','Comments');
define('_MA_WFP_MODULEUPGRADE','Update');
define('_MA_WFP_MODULEBLOCKS','Blocks');
define('_MA_WFP_MODULETEMPLATE','Templates');
define('_MA_WFP_SEARCH','Search');
define('_MA_WFP_MENU_MODULE','Module: ');

/**
 * Misc Lanaguge
 */
define('_MA_WFP_CREATECATEGORY','Create Category');
define('_MA_WFP_CREATENEW','Create Item');
define('_MA_WFP_PERMISSIONS','Set Permissions');
define('_MA_','');
define('_MA_ACTION','Action');
define('_MA_WFP_MODIFY','Modify Item: %s');
define('_MA_WFP_CREATE','Create New Item');
define('_MA_WFP_MAINAREA_DELETE_DSC','Delete Check! You are about to delete this item. You can cancel this action by clicking on the cancel button or you can choose to continue.<br /><br />This action is not reversible.');
define('_MA_WFP_MAINAREA_EDIT_DSC','Edit Mode: You can edit this pages properties here. Click the submit button to make your changes permanent or click Cancel to return you were you where.');
define('_MA_WFP_YES','Yes');
define('_MA_WFP_NO','No');
define('_MA_WFP_OFF','Off');
define('_MA_WFP_ON','On');
define('_MA_WFP_ERRORS' , 'Errors Found');
define('_MA_WFP_DYRWTDICONFIRM' , 'Do you really wish to delete item \'%s\'');
define('_MA_WFP_DYRWTUICONFIRM' , 'Do you really wish to update item \'%s\'');

/**
 * Global Form Items
 */
define('_MA_EWFP_GROUPS','Group Permission:');
define('_MA_EWFP_GROUPS_DSC','Please select which groups can view this item.');
define('_MA_EWFP_DOHTML',' Enable HTML Tags');
define('_MA_EWFP_DOSMILEY',' Enable Smiley Icons');
define('_MA_EWFP_DOXCODE',' Enable XOOPS Codes');
define('_MA_EWFP_BREAKS',' Enable Xoops linebreaks');

/**
 * Button Language defines
 */
define('_RESET','Reset');

/**
 * Navigation Items
 */
// // // Navigation Menu
define('_MA_WFP_NORECORDS','No Items Found');
define('_MA_WFP_RECORDSFOUND','Displaying Results %s - %s of %s entries');
define('_MA_WFP_DISPLAYAMOUNT_BOX','Display Amount: '); // // Misc
define('_MA_WFP_PAGE','Page: ');
define('_MA_WFP_SELECTALL','Check All');
define('_MA_WFP_SELECTNONE','Select None');
define('_MA_WFC_DISPLAYAMOUNT_BOX','Pages: ');
define('_MA_WFC_UPDATESELECTED','Update Selected');
define('_MA_WFC_DELETESELECTED','Delete Selected');
define('_MA_WFC_DUPLICATESELECTED','Duplicate Selected');
define('_MA_WFC_DISPLAYPUBLISHED','Display: ');

/**
 * Database Items
 */
define('_MA_WFP_DBCTREATED','New item created and database updated');
define('_MA_WFP_DBUPDATED','Item modified and database updated');
define('_MA_WFP_DBITEMDUPLICATED','Item duplicated and database updated');
define('_MA_WFP_DBERROR','Database was not updated due to an error!');
define('_MA_WFP_DBSELECTEDITEMSUPTATED','Selected items modified and database updated');
define('_MA_WFP_DBNOTUPDATED','Nothing Selected, Database not updated');
define('_MA_WFP_DBUPDATEDDELETED','Item deleted and database updated');
define('_MA_WFP_DBITEMSDUPLICATED','Selected items duplicated and database updated');
define('_MA_WFP_DBITEMSUPDATED','Selected items updated and database updated');
define('_MA_WFP_DBITEMSDELETED','Selected item deleted and database updated');

/**
 * WF-Resource Legend Defines
 */
define('_WFP_VIEW','View Item');
define('_WFP_EDIT','Edit Item');
define('_WFP_DUPLICATE','Duplicate Item');
define('_WFP_APPROVE','Approve Item');
define('_WFP_DELETE','Delete Item');
define('_WFP_INFO','Item Information');
define('_WFP_VIEW_LEG','View selected item in user mode');
define('_WFP_EDIT_LEG','Edit Item: Selecting this will allow you to edit the selected item.');
define('_WFP_DELETE_LEG','Delete Item: Remove the selected item, this option is not reversible');
define('_WFP_DUPLICATE_LEG','Duplicate Item: You can create a carbon copy of the selected item.');
define('_WFP_APPROVE_LEG','Approve Item: This item has not been approved and requires moderation.');
define('_WFP_INFO_LEG','Item Information: Display information regating the selected item.');

/**
 * Uploads
 */
define('_MA_WFP_SERVERSTATUS','Server Status');
define('_MA_WFP_SAFEMODE','Safe Mode: ');
define('_MA_WFP_UPLOADS','Server Uploads: ');
define('_MA_WFP_SAFEMODEPROBLEMS',' (This may cause problems) ');
define('_MA_WFP_ANDTHEMAX','Max Upload Size: ');
define('_MA_WFP_UPLOADIMAGE','Upload Image');
define('_MA_WFP_UPLOADLINKIMAGE','Upload File:');
define('_MA_WFP_DIRSELECT','Choose upload directory:');
define('_MA_WFP_UPLOADPATH','Upload Path:');
define('_MA_WFP_VIEWIMAGE','View Image:');
define('_MA_WFP_CHAN_UPLOADDIR','Images upload directory');
define('_MA_WFP_CHAN_LINKIMAGES','Link images upload Directory');
define('_MA_WFP_CHAN_HTMLUPLOADDIR','HTML files upload directory');
define('_MA_WFP_UPLOADCHANLOGO','This page logo');
define('_MA_WFP_UPLOADCHANTYPE','Choose Upload Type');
define('_MA_WFP_UPLOADCHANHTML','Static HTML File');
define('_MA_WFP_CHANHTML','Select HTML Document:');
define('_MA_WFP_CHANHTML_DSC','This document will be used as the maintext of the page.');
define('_MA_WFP_FILE_DSC','Use this method to import or link to a html file of your choice.');
define('_MA_WFP_DELETEFILE','WARNING<br/>Delete File %s from the server?<br />');
define('_MA_WFP_ERRORDELETEFILE','Error Deleting File %s');
define('_MA_WFP_FILEDELETED','File %s deleted');
define('_MA_WFP_FILEUPLOAD','File Uploaded');
define('_MA_WFP_FILEDOESNOTEXIST','The file you are trying to upload does not exist.');
/**
 * Votes language defines
 */
define('_MA_VOTE_ID','#');
define('_MA_VOTE_UID','Submitter');
define('_MA_VOTE_RATING','Rating');
define('_MA_VOTE_IPADDRESS','IP Address');
define('_MA_VOTE_DATE','Date');
define('_MA_VOTE_ANAME','Article');
define('_MA_ACTIONS','Actions');

/**
 * Broken language defines
 */
define('_MA_BROKEN_ID','#');
define('_MA_BROKEN_UID','Submitter');
define('_MA_BROKEN_FILE','File Name');
define('_MA_BROKEN_IP','IP Address');
define('_MA_BROKEN_DATE','Submitted');
define('_MA_BROKEN_CONFIRMED','Confirm');
define('_MA_BROKEN_ACKNOWLEDGED','Acknowledge');

/**
 * Category Language Defines
 */
define('_MA_ECATEGORY_CSECTION','Category Section:');
define('_MA_ECATEGORY_CSUBCATEGORY','Category Sub-Category:');
define('_MA_ECATEGORY_TITLE','Category Title:');
define('_MA_ECATEGORY_TITLE_DSC','');
define('_MA_ECATEGORY_TYPE','Category Type:');
define('_MA_ECATEGORY_TYPE_DSC','');
define('_MA_ECATEGORY_TEXT','Category Description:');
define('_MA_ECATEGORY_RGRP','Access Groups:');
define('_MA_ECATEGORY_WGRP','Submission Groups:');
define('_MA_ECATEGORY_SIDE','Category Image Position:');
define('_MA_ECATEGORY_SIDE_DSC','');
define('_MA_ECATEGORY_WEIGHT','Category Order:');
define('_MA_ECATEGORY_WEIGHT_DSC','');
define('_MA_ECATEGORY_DISPLAY','Activate Category?');
define('_MA_ECATEGORY_DISPLAY_DSC','');
define('_MA_ECATEGORY_SELECT','Image Folders:');
define('_MA_ECATEGORY_SELECT_DSC','Select which Image folders can be used with this category');
define('_MA_ECATEGORY_SELECTIMAGE','Select Category Image:');
define('_MA_ECATEGORY_SELECTIMAGE_DSC','Select which Image that will be shown with this category');
define('_MA_ECATEGORY_METATITLE','Section Meta Title');
define('_MA_ECATEGORY_METATITLE_DSC','');
define('_MA_ECATEGORY_MDESCRIPTION','Section Meta Description');
define('_MA_ECATEGORY_MDESCRIPTION_DSC','');
define('_MA_ECATEGORY_MKEYWORDS','Section Meta Keywords');
define('_MA_ECATEGORY_MKEYWORDS_DSC','');
define('_MA_SMILIES_SELECTIMAGE','Select Category Image:');
define('_MA_SMILIES_SELECTIMAGE_DSC','');

/**
 * Mimetypes
 */
define('_MA_MIME_ALLCAT','Display All');
define('_MA_MIME_CUNKNOWN','Unknown');
define('_MA_MIME_CARCHIVES','Archive');
define('_MA_MIME_CAUDIO','Audio');
define('_MA_MIME_CTEXT','Text');
define('_MA_MIME_CDOCUMENT','Document');
define('_MA_MIME_CHELP','Help');
define('_MA_MIME_CSOURCE','Source');
define('_MA_MIME_CVIDEO','Video');
define('_MA_MIME_CHTML','Internet');
define('_MA_MIME_CGRAPHICS','Graphic');
define('_MA_MIME_CMIDI','Midi');
define('_MA_MIME_CBINARY','Binary/exe');
// Form Header
define('_MA_MIME_ID','#');
define('_MA_MIME_EXT','Ext');
define('_MA_MIME_NAME','Application Type');
define('_MA_MIME_ASCENDING','ASC');
define('_MA_MIME_DESCENDING','DESC');
define('_MA_MIME_FINDTYPE','Find Mimetype');
define('_MA_MIME_SEARCHURL','Search');
define('_MA_TEXT_ASCENDING','Ascending');
define('_MA_TEXT_DESCENDING','Descending');
define('_MA_TEXT_NUMBER_PER_PAGE','Number Per Page:');
define('_MA_TEXT_ORDER_BY','Order By:');
define('_MA_TEXT_SEARCH_MIME','Search Mimetypes');
define('_MA_TEXT_SORT_BY','Sort By:');
define('_MA_MIME_SAFE','Safe');
define('_MA_DISPLAY_CATEGORY_BOX','Display Category');
define('_MA_DISPLAY_SAFE_BOX','Display Safe Types');
define('_MA_SHOWSAFEALL_BOX','All Types');
define('_MA_SHOWSAFENOT_BOX','Unsafe Types');
define('_MA_SHOWSAFEIS_BOX','Safe Types');

/**
 * Mimetype editForm
 */
define('_MA_MIME_CREATEF','Create Mimetype');
define('_MA_MIME_MODIFYF','Modify Mimetype');
define('_MA_MIME_EXTF','File Extension:');
define('_MA_MIME_NAMEF','Application Type/Name:<div style="padding-top: 8px;"><span style="font-weight: normal;">Enter application associated with this extension.</span></div>');
define('_MA_MIME_TYPEF','Mimetypes:');
define('_MA_MIME_USEFULTAGS','Enter each mimetype associated with the file extension. Each mimetype must be seperated with a space.');
define('_MA_MIME_FINDMIMETYPE','Find New Mimetype:');
define('_MA_MIME_EXTFIND','Search File Extension:<div style="padding-top: 8px;"><span style="font-weight: normal;">Enter file extension you wish to search.</span></div>');
define('_MA_MIME_EACTIVATE','Activate Mimetype?');
define('_MA_MIME_EACTIVATE_DSC','');
define('_MA_MIME_EIMAGE','Mimetype Image:');
define('_MA_MIME_EIMAGE_DSC','');
define('_MA_MIME_ESAFE','Safe Mimetype:');
define('_MA_MIME_ESAFE_DSC','This mimetype is considered safe and can be used by anyone. If marked unsafe only admin will be able to use this');
define('_MA_MIME_ECATEGORY','Mimetype Category?');
define('_MA_MIME_ECATEGORY_DSC','');
define('_MA_MIME_YSAFE','Safe');
define('_MA_MIME_YUNSAFE','Unsafe');
define('_MA_MIME_CATEGORY','Category');
define('_MA_MIME_DISPLAY','Active');
define('_MA_MIME_LIST','List Mimetypes');

/**
 * Menu items
 */
define('_MA_MIME_PERMISSION','Set Permissions');
define('_MA_MIME_CREATE_NEW','New Mimetype');
define('_MA_MIME_LISTMIME','List Mimetypes');
define('_MA_MIME_SEARCH_TEXT','Search Text:');
define('_MA_MIME_SEARCH_BY','Search By:');
define('_MA_MIME_PERMISSON','Mimetypes Permissions');
define('_MA_MIME_MODULE','Modules: ');
define('_MA_MIME_GROUP','Group: ');
define('_MA_MIME_CSELECTPERMISSIONS','Select Group Permissions: ');

define('_SR_ANY','Any (OR)');
define('_SR_ALL','All (AND)');
define('_SR_EXACT','Exact Match');

?>