<?php
/**
 * Name: admin.php
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

define('_AM_WFP_ADMINPREFS', 'Preferences');
define('_AM_WFP_ADMININDEX', 'Admin Index');
define('_AM_WFP_PREFS', 'Preferences');
define('_AM_WFP_MODULEHOME', 'Module Home');
define('_AM_WFP_MODULEABOUT', 'About');
define('_AM_WFP_MODULEHELP', 'Help');
define('_AM_WFP_ADMINCOMMENTS', 'Comments');
define('_AM_WFP_MODULEUPGRADE', 'Update');
define('_AM_WFP_MODULEBLOCKS', 'Blocks');
define('_AM_WFP_MODULETEMPLATE', 'Templates');
define('_AM_WFP_SEARCH', 'Search');
define('_AM_WFP_MENU_MODULE', 'Module: ');

/**
 * Misc Lanaguge
 */
define('_AM_WFP_CREATECATEGORY', 'Create Category');
define('_AM_WFP_CATEGORY_MODIFY', 'Modify Category');
define('_AM_WFP_CREATENEW', 'Create Item');
define('_AM_WFP_PERMISSIONS', 'Set Permissions');
define('_AM_', '');
define('_AM_ACTION', 'Action');
define('_AM_WFP_MODIFY', 'Modify Item: %s');
define('_AM_WFP_CREATE', 'Create New Item');
define('_AM_WFP_MAINAREA_DELETE_DSC', 'Delete Check! You are about to delete this item. You can cancel this action by clicking on the cancel button or you can choose to continue.<br><br>This action is not reversible.');
define('_AM_WFP_MAINAREA_EDIT_DSC', 'Edit Mode: You can edit this pages properties here. Click the submit button to make your changes permanent or click Cancel to return you were you where.');
define('_AM_WFP_YES', 'Yes');
define('_AM_WFP_NO', 'No');
define('_AM_WFP_OFF', 'Off');
define('_AM_WFP_ON', 'On');
define('_AM_WFP_ERRORS', 'Errors Found');
define('_AM_WFP_DYRWTDICONFIRM', 'Do you really wish to delete item \'%s\'');
define('_AM_WFP_DYRWTUICONFIRM', 'Do you really wish to update item \'%s\'');

/**
 * Global Form Items
 */
define('_AM_EWFP_GROUPS', 'Group Permission:');
define('_AM_EWFP_GROUPS_DSC', 'Please select which groups can view this item.');
define('_AM_EWFP_DOHTML', ' Enable HTML Tags');
define('_AM_EWFP_DOSMILEY', ' Enable Smiley Icons');
define('_AM_EWFP_DOXCODE', ' Enable XOOPS Codes');
define('_AM_EWFP_BREAKS', ' Enable Xoops linebreaks');

/**
 * Navigation Items
 */
// // // Navigation Menu
define('_AM_WFP_NORECORDS', 'No Items Found');
define('_AM_WFP_RECORDSFOUND', 'Displaying Results %s - %s of %s entries');
define('_AM_WFP_DISPLAYAMOUNT_BOX', 'Display Amount: '); // // Misc
define('_AM_WFP_PAGE', 'Page: ');
define('_AM_WFP_SELECTALL', 'Check All');
define('_AM_WFP_SELECTNONE', 'Select None');
define('_AM_WFC_DISPLAYAMOUNT_BOX', 'Pages: ');
define('_AM_WFC_UPDATESELECTED', 'Update Selected');
define('_AM_WFC_DELETESELECTED', 'Delete Selected');
define('_AM_WFC_DUPLICATESELECTED', 'Duplicate Selected');
define('_AM_WFC_DISPLAYPUBLISHED', 'Display: ');

/**
 * Database Items
 */
define('_AM_WFP_DBCTREATED', 'New item created and database updated');
define('_AM_WFP_DBUPDATED', 'Item modified and database updated');
define('_AM_WFP_DBITEMDUPLICATED', 'Item duplicated and database updated');
define('_AM_WFP_DBERROR', 'Database was not updated due to an error!');
define('_AM_WFP_DBSELECTEDITEMSUPTATED', 'Selected items modified and database updated');
define('_AM_WFP_DBNOTUPDATED', 'Nothing Selected, Database not updated');
define('_AM_WFP_DBUPDATEDDELETED', 'Item deleted and database updated');
define('_AM_WFP_DBITEMSDUPLICATED', 'Selected items duplicated and database updated');
define('_AM_WFP_DBITEMSUPDATED', 'Selected items updated and database updated');
define('_AM_WFP_DBITEMSDELETED', 'Selected item deleted and database updated');

/**
 * WF-Resource Legend Defines
 */
define('_AM_WFP_VIEW', 'View Item');
define('_AM_WFP_EDIT', 'Edit Item');
define('_AM_WFP_DUPLICATE', 'Duplicate Item');
define('_AM_WFP_APPROVE', 'Approve Item');
define('_AM_WFP_DELETE', 'Delete Item');
define('_AM_WFP_INFO', 'Item Information');
define('_AM_WFP_VIEW_LEG', 'View selected item in user mode');
define('_AM_WFP_EDIT_LEG', 'Edit Item: Selecting this will allow you to edit the selected item.');
define('_AM_WFP_DELETE_LEG', 'Delete Item: Remove the selected item, this option is not reversible');
define('_AM_WFP_DUPLICATE_LEG', 'Duplicate Item: You can create a carbon copy of the selected item.');
define('_AM_WFP_APPROVE_LEG', 'Approve Item: This item has not been approved and requires moderation.');
define('_AM_WFP_INFO_LEG', 'Item Information: Display information regarding the selected item.');

/**
 * Uploads
 */
define('_AM_WFP_SERVERSTATUS', 'Server Status');
define('_AM_WFP_SAFEMODE', 'Safe Mode: ');
define('_AM_WFP_UPLOADS', 'Server Uploads: ');
define('_AM_WFP_SAFEMODEPROBLEMS', ' (This may cause problems) ');
define('_AM_WFP_ANDTHEMAX', 'Max Upload Size: ');
define('_AM_WFP_UPLOADIMAGE', 'Upload Image');
define('_AM_WFP_UPLOADLINKIMAGE', 'Upload File:');
define('_AM_WFP_DIRSELECT', 'Choose upload directory:');
define('_AM_WFP_UPLOADPATH', 'Upload Path:');
define('_AM_WFP_VIEWIMAGE', 'View Image:');
define('_AM_WFP_CHAN_UPLOADDIR', 'Images upload directory');
define('_AM_WFP_CHAN_LINKIMAGES', 'Link images upload Directory');
define('_AM_WFP_CHAN_HTMLUPLOADDIR', 'HTML files upload directory');
define('_AM_WFP_UPLOADCHANLOGO', 'This page logo');
define('_AM_WFP_UPLOADCHANTYPE', 'Choose Upload Type');
define('_AM_WFP_UPLOADCHANHTML', 'Static HTML File');
define('_AM_WFP_CHANHTML', 'Select HTML Document:');
define('_AM_WFP_CHANHTML_DSC', 'This document will be used as the maintext of the page.');
define('_AM_WFP_FILE_DSC', 'Use this method to import or link to a html file of your choice.');
define('_AM_WFP_DELETEFILE', 'WARNING<br>Delete File %s from the server?<br>');
define('_AM_WFP_ERRORDELETEFILE', 'Error Deleting File %s');
define('_AM_WFP_FILEDELETED', 'File %s deleted');
define('_AM_WFP_FILEUPLOAD', 'File Uploaded');
define('_AM_WFP_FILEDOESNOTEXIST', 'The file you are trying to upload does not exist.');
/**
 * Votes language defines
 */
define('_AM_VOTE_ID', '#');
define('_AM_VOTE_UID', 'Submitter');
define('_AM_VOTE_RATING', 'Rating');
define('_AM_VOTE_IPADDRESS', 'IP Address');
define('_AM_VOTE_DATE', 'Date');
define('_AM_VOTE_ANAME', 'Article');
define('_AM_WFP_ACTION', 'Actions');

/**
 * Broken language defines
 */
define('_AM_BROKEN_ID', '#');
define('_AM_BROKEN_UID', 'Submitter');
define('_AM_BROKEN_FILE', 'File Name');
define('_AM_BROKEN_IP', 'IP Address');
define('_AM_BROKEN_DATE', 'Submitted');
define('_AM_BROKEN_CONFIRMED', 'Confirm');
define('_AM_BROKEN_ACKNOWLEDGED', 'Acknowledge');

/**
 * Category Language Defines
 */
define('_AM_ECATEGORY_CSECTION', 'Category Section:');
define('_AM_ECATEGORY_CSUBCATEGORY', 'Category Sub-Category:');
define('_AM_ECATEGORY_TITLE', 'Category Title:');
define('_AM_ECATEGORY_TITLE_DSC', '');
define('_AM_ECATEGORY_TYPE', 'Category Type:');
define('_AM_ECATEGORY_TYPE_DSC', '');
define('_AM_ECATEGORY_TEXT', 'Category Description:');
define('_AM_ECATEGORY_RGRP', 'Access Groups:');
define('_AM_ECATEGORY_WGRP', 'Submission Groups:');
define('_AM_ECATEGORY_SIDE', 'Category Image Position:');
define('_AM_ECATEGORY_SIDE_DSC', '');
define('_AM_ECATEGORY_WEIGHT', 'Category Order:');
define('_AM_ECATEGORY_WEIGHT_DSC', '');
define('_AM_ECATEGORY_DISPLAY', 'Activate Category?');
define('_AM_ECATEGORY_DISPLAY_DSC', '');
define('_AM_ECATEGORY_SELECT', 'Image Folders:');
define('_AM_ECATEGORY_SELECT_DSC', 'Select which Image folders can be used with this category');
define('_AM_ECATEGORY_SELECTIMAGE', 'Select Category Image:');
define('_AM_ECATEGORY_SELECTIMAGE_DSC', 'Select which Image that will be shown with this category');
define('_AM_ECATEGORY_METATITLE', 'Section Meta Title');
define('_AM_ECATEGORY_METATITLE_DSC', '');
define('_AM_ECATEGORY_MDESCRIPTION', 'Section Meta Description');
define('_AM_ECATEGORY_MDESCRIPTION_DSC', '');
define('_AM_ECATEGORY_MKEYWORDS', 'Section Meta Keywords');
define('_AM_ECATEGORY_MKEYWORDS_DSC', '');
define('_AM_SMILIES_SELECTIMAGE', 'Select Category Image:');
define('_AM_SMILIES_SELECTIMAGE_DSC', '');

/**
 * Mimetypes
 */
define('_AM_MIME_ALLCAT', 'Display All');
define('_AM_MIME_CUNKNOWN', 'Unknown');
define('_AM_MIME_CARCHIVES', 'Archive');
define('_AM_MIME_CAUDIO', 'Audio');
define('_AM_MIME_CTEXT', 'Text');
define('_AM_MIME_CDOCUMENT', 'Document');
define('_AM_MIME_CHELP', 'Help');
define('_AM_MIME_CSOURCE', 'Source');
define('_AM_MIME_CVIDEO', 'Video');
define('_AM_MIME_CHTML', 'Internet');
define('_AM_MIME_CGRAPHICS', 'Graphic');
define('_AM_MIME_CMIDI', 'Midi');
define('_AM_MIME_CBINARY', 'Binary/exe');
// Form Header
define('_AM_MIME_ID', '#');
define('_AM_MIME_EXT', 'Ext');
define('_AM_MIME_NAME', 'Application Type');
define('_AM_MIME_ASCENDING', 'ASC');
define('_AM_MIME_DESCENDING', 'DESC');
define('_AM_MIME_FINDTYPE', 'Find Mimetype');
define('_AM_MIME_SEARCHURL', 'Search');
define('_AM_TEXT_ASCENDING', 'Ascending');
define('_AM_TEXT_DESCENDING', 'Descending');
define('_AM_TEXT_NUMBER_PER_PAGE', 'Number Per Page:');
define('_AM_TEXT_ORDER_BY', 'Order By:');
define('_AM_TEXT_SEARCH_MIME', 'Search Mimetypes');
define('_AM_TEXT_SORT_BY', 'Sort By:');
define('_AM_MIME_SAFE', 'Safe');
define('_AM_DISPLAY_CATEGORY_BOX', 'Display Category');
define('_AM_DISPLAY_SAFE_BOX', 'Display Safe Types');
define('_AM_SHOWSAFEALL_BOX', 'All Types');
define('_AM_SHOWSAFENOT_BOX', 'Unsafe Types');
define('_AM_SHOWSAFEIS_BOX', 'Safe Types');

/**
 * Mimetype editForm
 */
define('_AM_MIME_CREATEF', 'Create Mimetype');
define('_AM_MIME_MODIFYF', 'Modify Mimetype');
define('_AM_MIME_EXTF', 'File Extension:');
define('_AM_MIME_NAMEF', 'Application Type/Name:<div style="padding-top: 8px;"><span style="font-weight: normal;">Enter application associated with this extension.</span></div>');
define('_AM_MIME_TYPEF', 'Mimetypes:');
define('_AM_MIME_USEFULTAGS', 'Enter each mimetype associated with the file extension. Each mimetype must be seperated with a space.');
define('_AM_MIME_FINDMIMETYPE', 'Find New Mimetype:');
define('_AM_MIME_EXTFIND', 'Search File Extension:<div style="padding-top: 8px;"><span style="font-weight: normal;">Enter file extension you wish to search.</span></div>');
define('_AM_MIME_EACTIVATE', 'Activate Mimetype?');
define('_AM_MIME_EACTIVATE_DSC', '');
define('_AM_MIME_EIMAGE', 'Mimetype Image:');
define('_AM_MIME_EIMAGE_DSC', '');
define('_AM_MIME_ESAFE', 'Safe Mimetype:');
define('_AM_MIME_ESAFE_DSC', 'This mimetype is considered safe and can be used by anyone. If marked unsafe only admin will be able to use this');
define('_AM_MIME_ECATEGORY', 'Mimetype Category?');
define('_AM_MIME_ECATEGORY_DSC', '');
define('_AM_MIME_YSAFE', 'Safe');
define('_AM_MIME_YUNSAFE', 'Unsafe');
define('_AM_MIME_CATEGORY', 'Category');
define('_AM_MIME_DISPLAY', 'Active');
define('_AM_MIME_LIST', 'List Mimetypes');

/**
 * Menu items
 */
define('_AM_MIME_PERMISSION', 'Set Permissions');
define('_AM_MIME_CREATE_NEW', 'New Mimetype');
define('_AM_MIME_LISTMIME', 'List Mimetypes');
define('_AM_MIME_SEARCH_TEXT', 'Search Text:');
define('_AM_MIME_SEARCH_BY', 'Search By:');
define('_AM_MIME_PERMISSON', 'Mimetypes Permissions');
define('_AM_MIME_MODULE', 'Modules: ');
define('_AM_MIME_GROUP', 'Group: ');
define('_AM_MIME_CSELECTPERMISSIONS', 'Select Group Permissions: ');

define('_SR_ANY', 'Any (OR)');
define('_SR_ALL', 'All (AND)');
define('_SR_EXACT', 'Exact Match');

define('_AM_WFP_NOSELECTION', 'No Selection');

define('_AM_WF_RESOURCE_UPGRADEFAILED0', "Update failed - couldn't rename field '%s'");
define('_AM_WF_RESOURCE_UPGRADEFAILED1', "Update failed - couldn't add new fields");
define('_AM_WF_RESOURCE_UPGRADEFAILED2', "Update failed - couldn't rename table '%s'");
define('_AM_WF_RESOURCE_ERROR_COLUMN', 'Could not create column in database : %s');
define('_AM_WF_RESOURCE_ERROR_BAD_XOOPS', 'This module requires XOOPS %s+ (%s installed)');
define('_AM_WF_RESOURCE_ERROR_BAD_PHP', 'This module requires PHP version %s+ (%s installed)');
define('_AM_WF_RESOURCE_ERROR_TAG_REMOVAL', 'Could not remove tags from Tag Module');
