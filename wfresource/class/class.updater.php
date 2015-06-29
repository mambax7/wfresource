<?php
/**
 * Name: class.updater.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: class.updater.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * wfp_Updater
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class.updater.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_Updater {
	var $_table;
	var $_query = array();
	var $errors = array();
	var $success = array();

	/**
	 * wfp_Updater::wfp_Updater()
	 */
	function wfp_Updater() {
		$this->db = &XoopsDatabaseFactory::getDatabaseConnection();
	}

	/**
	 * wfp_Updater::setTable()
	 *
	 * @param mixed $value
	 * @return
	 */
	function setTable( $value ) {
		$this->_table = $value;
	}

	/**
	 * wfp_Updater::getTable()
	 *
	 * @return
	 */
	function getTable() {
		return $this->_table;
	}

	/**
	 * wfp_Updater::addField()
	 *
	 * @param mixed $fieldname
	 * @param mixed $sql
	 * @param string $after
	 * @return
	 */
	function addField( $fieldname = '', $sql, $after = '' ) {
		$this->_query['type'][] = 'ADD';
		$this->_query['fieldname'][] = ( !empty( $fieldname ) ) ? $fieldname : '';
		$this->_query['sql'][] = $sql;
		$this->_query['after'][] = $after;
		$this->_query['bit'][] = 1;
	}

	/**
	 * wfp_Updater::changeField()
	 *
	 * @param mixed $fieldname
	 * @param mixed $sql
	 * @return
	 */
	function changeField( $fieldname, $sql ) {
		$this->_query['type'][] = "CHANGE";
		$this->_query['fieldname'][] = $fieldname;
		$this->_query['sql'][] = $sql;
		$this->_query['after'][] = '';
		$this->_query['bit'][] = '';
	}

	/**
	 * wfp_Updater::modifyField()
	 *
	 * @param mixed $fieldname
	 * @param mixed $sql
	 * @param string $after
	 * @return
	 */
	function modifyField( $fieldname, $sql, $after = '' ) {
		$this->_query['type'][] = "MODIFY";
		$this->_query['fieldname'][] = $fieldname;
		$this->_query['sql'][] = $sql;
		$this->_query['after'][] = $after;
		$this->_query['bit'][] = 1;
	}

	/**
	 * wfp_Updater::dropField()
	 *
	 * @param mixed $fieldname
	 * @return
	 */
	function dropField( $fieldname = '', $sql ) {
		$this->_query['type'][] = 'DROP';
		$this->_query['fieldname'][] = $fieldname;
		$this->_query['sql'][] = $sql;
		$this->_query['after'][] = '';
		$this->_query['bit'][] = '';
	}

	/**
	 * wfp_Updater::RenameTable()
	 *
	 * @param mixed $oldName
	 * @param mixed $newName
	 * @return
	 */
	function RenameTable( $oldName, $newName ) {
		if ( !in_array( $oldName, array( 'wfschannel', 'wfslinktous', 'wfsrefer' ) ) ) {
			return false;
		}
		if ( $this->table_exists( $newName ) ) {
			$this->setSuccess( 'Notice: Table ' . $newName . ' Already exists and no need to update ' );
			return true;
		}
		$sql = 'RENAME TABLE ' . $this->db->prefix( $oldName ) . ' TO ' . $this->db->prefix( $newName );
		$result = $this->db->queryF( $sql );
		if ( !$result && ( $this->db->errno() != '1050' ) ) {
			$this->setError( $this->db->error() . ' ' . $this->db->errno() . ": Table $oldName could not be renamed" );
			return false;
		} else {
			if ( $this->db->errno() != '1050' ) {
				$this->setSuccess( "Notice: Table $oldName renamed to $newName" );
				return true;
			} else {
				$this->setError( $this->db->errno() . ": Unknown error updating table $oldName to $newName" );
			}
		}
	}

	/**
	 * wfp_Updater::CreateTable()
	 *
	 * @param mixed $tablename
	 * @param mixed $data
	 * @param mixed $addAuto
	 * @return
	 */
	function CreateTable( $tablename, $data, $addAuto = 0 ) {
		if ( $this->table_exists( $tablename ) ) {
			$this->setSuccess( 'Notice: Table ' . $tablename . ' Already exists and no need to update ' );
			return true;
		}
		if ( in_array( $tablename, array( 'wfcrefers' ) ) ) {
			$sql = "CREATE TABLE " . $this->db->prefix( $tablename ) . " (";
			$sql .= "$data";
			$sql .= ") ENGINE=MyISAM ";
			if ( $addAuto ) {
				$sql .= "AUTO_INCREMENT=0";
			}
		}
		$result = $this->db->queryF( $sql );
		if ( !$result && ( $this->db->errno() != '1050' ) ) {
			$this->setError( "Table $tablename could not be created<br /<br />" . $this->db->error() . ' ' . $this->db->errno() );
			return false;
		} else {
			$this->setSuccess( "Notice: Table $tablename as been created" );
			return true;
		}
	}

	/**
	 * wfp_Updater::doChange()
	 *
	 * @return
	 */
	function doChange() {
		foreach( array_keys( $this->_query['type'] ) as $i ) {
			$sql = "ALTER TABLE " . $this->db->prefix( $this->_table ) . " ";
			$sql .= $this->_query['type'][$i];
			// if ( !empty( $this->_query['fieldname'][$i] ) && empty( $this->_query['bit'][$i] ) ) {
			$sql .= " " . $this->_query['fieldname'][$i] . " ";
			// }
			if ( !empty( $this->_query['sql'][$i] ) ) {
				$sql .= " " . $this->_query['sql'][$i] . " ";
			}
			if ( !empty( $this->_query['after'][$i] ) && $this->_query['after'][$i] == 'FIRST' ) {
				$sql .= " FIRST ";
			} elseif ( !empty( $this->_query['after'][$i] ) ) {
				$sql .= " AFTER " . $this->_query['after'][$i] . " ";
			}
			$sql .= "\n\n";
			$result = $this->db->queryF( $sql );
			if ( !$result ) {
				$this->setError( "Field <span style=\"color: red;\">" . $this->_query['fieldname'][$i] . "</span> could not be updated <br />Error: " . $this->db->errno() . ' ' . $this->db->error() );
			} else {
				$this->setSuccess( "Field " . $this->_query['fieldname'][$i] . " updated " );
			}
		}
	}

	/**
	 * wfp_Updater::table_exists()
	 *
	 * @param mixed $tablename
	 * @return
	 */
	function table_exists( $tablename ) {
		$result = $this->db->query( "SELECT 1 FROM " . $this->db->prefix( $tablename ) . " LIMIT 0" );
		return ( $result ) ? 1 : 0;
	}
	/**
	 * wfp_Updater::setError()
	 *
	 * @param mixed $value
	 * @return
	 */
	function setError( $value ) {
		$this->error[] = $value;
	}

	/**
	 * wfp_Updater::setSuccess()
	 *
	 * @param mixed $value
	 * @return
	 */
	function setSuccess( $value ) {
		$this->success[] = $value;
	}

	/**
	 * wfp_Updater::getError()
	 *
	 * @return
	 */
	function getError() {
		return $this->error;
	}

	/**
	 * wfp_Updater::getSuccess()
	 *
	 * @return
	 */
	function getSuccess() {
		return $this->success;
	}

	/**
	 * wfp_Updater::render()
	 *
	 * @return
	 */
	function render() {
		foreach( $this->error as $errors ) {
			echo $errors;
		}
	}

	/**
	 * wfp_Updater::renderS()
	 *
	 * @return
	 */
	function renderS() {
		foreach( $this->success as $success ) {
			echo $success;
		}
	}
}

?>