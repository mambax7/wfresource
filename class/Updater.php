<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.updater.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * Updater
 *
 * @author    John
 * @copyright Copyright (c) 2007
 */
class Updater
{
    public $_table;
    public $_query  = [];
    public $errors  = [];
    public $success = [];

    /**
     * Updater::__constructor()
     */
    public function __construct()
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * Updater::setTable()
     *
     * @param mixed $value
     */
    public function setTable($value): void
    {
        $this->_table = $value;
    }

    /**
     * Updater::getTable()
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Updater::addField()
     *
     * @param mixed  $fieldname
     * @param mixed  $sql
     * @param string $after
     */
    public function addField($fieldname, $sql, $after = ''): void
    {
        $this->_query['type'][]      = 'ADD';
        $this->_query['fieldname'][] = !empty($fieldname) ? $fieldname : '';
        $this->_query['sql'][]       = $sql;
        $this->_query['after'][]     = $after;
        $this->_query['bit'][]       = 1;
    }

    /**
     * Updater::changeField()
     *
     * @param mixed $fieldname
     * @param mixed $sql
     */
    public function changeField($fieldname, $sql): void
    {
        $this->_query['type'][]      = 'CHANGE';
        $this->_query['fieldname'][] = $fieldname;
        $this->_query['sql'][]       = $sql;
        $this->_query['after'][]     = '';
        $this->_query['bit'][]       = '';
    }

    /**
     * Updater::modifyField()
     *
     * @param mixed  $fieldname
     * @param mixed  $sql
     * @param string $after
     */
    public function modifyField($fieldname, $sql, $after = ''): void
    {
        $this->_query['type'][]      = 'MODIFY';
        $this->_query['fieldname'][] = $fieldname;
        $this->_query['sql'][]       = $sql;
        $this->_query['after'][]     = $after;
        $this->_query['bit'][]       = 1;
    }

    /**
     * Updater::dropField()
     *
     * @param mixed $fieldname
     * @param       $sql
     */
    public function dropField($fieldname, $sql): void
    {
        $this->_query['type'][]      = 'DROP';
        $this->_query['fieldname'][] = $fieldname;
        $this->_query['sql'][]       = $sql;
        $this->_query['after'][]     = '';
        $this->_query['bit'][]       = '';
    }

    /**
     * Updater::RenameTable()
     *
     * @param mixed $oldName
     * @param mixed $newName
     * @return bool
     */
    public function RenameTable($oldName, $newName): ?bool
    {
        if (!\in_array($oldName, ['wfschannel', 'wfslinktous', 'wfsrefer'], true)) {
            return false;
        }
        if ($this->table_exists($newName)) {
            $this->setSuccess('Notice: Table ' . $newName . ' Already exists and no need to update ');

            return true;
        }
        $sql    = 'RENAME TABLE ' . $this->db->prefix($oldName) . ' TO ' . $this->db->prefix($newName);
        $result = $this->db->queryF($sql);
        if (!$result && ('1050' !== $this->db->errno())) {
            $this->setError($this->db->error() . ' ' . $this->db->errno() . ": Table $oldName could not be renamed");

            return false;
        }
        if ('1050' !== $this->db->errno()) {
            $this->setSuccess("Notice: Table $oldName renamed to $newName");

            return true;
        }
        $this->setError($this->db->errno() . ": Unknown error updating table $oldName to $newName");
    }

    /**
     * Updater::CreateTable()
     *
     * @param mixed $tablename
     * @param mixed $data
     * @param mixed $addAuto
     * @return bool
     */
    public function createTable($tablename, $data, $addAuto = 0)
    {
        if ($this->table_exists($tablename)) {
            $this->setSuccess('Notice: Table ' . $tablename . ' Already exists and no need to update ');

            return true;
        }
        if (\in_array($tablename, ['wfcrefers'], true)) {
            $sql = 'CREATE TABLE ' . $this->db->prefix($tablename) . ' (';
            $sql .= $data;
            $sql .= ') ENGINE=MyISAM ';
            if ($addAuto) {
                $sql .= 'AUTO_INCREMENT=0';
            }
        }
        $result = $this->db->queryF($sql);
        if (!$result && ('1050' !== $this->db->errno())) {
            $this->setError("Table $tablename could not be created<br /<br>" . $this->db->error() . ' ' . $this->db->errno());

            return false;
        }
        $this->setSuccess("Notice: Table $tablename as been created");

        return true;
    }

    /**
     * Updater::doChange()
     */
    public function doChange(): void
    {
        foreach (\array_keys($this->_query['type']) as $i) {
            $sql = 'ALTER TABLE ' . $this->db->prefix($this->_table) . ' ';
            $sql .= $this->_query['type'][$i];
            // if ( !empty( $this->_query['fieldname'][$i] ) && empty( $this->_query['bit'][$i] ) ) {
            $sql .= ' ' . $this->_query['fieldname'][$i] . ' ';
            // }
            if (!empty($this->_query['sql'][$i])) {
                $sql .= ' ' . $this->_query['sql'][$i] . ' ';
            }
            if (!empty($this->_query['after'][$i]) && 'FIRST' === $this->_query['after'][$i]) {
                $sql .= ' FIRST ';
            } elseif (!empty($this->_query['after'][$i])) {
                $sql .= ' AFTER ' . $this->_query['after'][$i] . ' ';
            }
            $sql    .= "\n\n";
            $result = $this->db->queryF($sql);
            if (!$result) {
                $this->setError('Field <span style="color: red;">' . $this->_query['fieldname'][$i] . '</span> could not be updated <br>Error: ' . $this->db->errno() . ' ' . $this->db->error());
            } else {
                $this->setSuccess('Field ' . $this->_query['fieldname'][$i] . ' updated ');
            }
        }
    }

    /**
     * Updater::table_exists()
     *
     * @param mixed $tablename
     */
    public function table_exists($tablename): int
    {
        $result = $this->db->query('SELECT 1 FROM ' . $this->db->prefix($tablename) . ' LIMIT 0');

        return $result ? 1 : 0;
    }

    /**
     * Updater::setError()
     *
     * @param mixed $value
     */
    public function setError($value): void
    {
        $this->error[] = $value;
    }

    /**
     * Updater::setSuccess()
     *
     * @param mixed $value
     */
    public function setSuccess($value): void
    {
        $this->success[] = $value;
    }

    /**
     * Updater::getError()
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Updater::getSuccess()
     */
    public function getSuccess(): array
    {
        return $this->success;
    }

    /**
     * Updater::render()
     */
    public function render(): void
    {
        foreach ($this->error as $errors) {
            echo $errors;
        }
    }

    /**
     * Updater::renderS()
     */
    public function renderS(): void
    {
        foreach ($this->success as $success) {
            echo $success;
        }
    }
}
