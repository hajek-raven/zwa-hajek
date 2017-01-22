<?php

namespace App\Model;

use Dibi\Connection;
use InvalidArgumentException;

/**
 * Base database data model.
 */
abstract class DatabaseModel {

	/**
	 * Database tables
	 */
	const TABLE_CATEGORY = 'category';
	const TABLE_JOKES = 'jokes';
	const TABLE_USERS = 'user';
	/**/

	/** @var array of string */
	protected $tableName = array(
		self::TABLE_CATEGORY => 'categories',
		self::TABLE_JOKES => 'jokes',
		self::TABLE_USERS => 'users',
	);

	protected $dbConnection;

	public function __construct(\Dibi\Connection $connection) {
		$this->dbConnection = $connection;
	}

	// -------------------------------------------------------------------------

	/**
	 * Sets table name
	 *
	 * @param string $table
	 * @param string $tableName
	 *
	 * @return self
	 * @throws InvalidArgumentException if trying to set invalid table
	 */
	public function setTableName($table, $tableName) {
		if(!array_key_exists($this->tableName, $table) )
			throw new \InvalidArgumentException("Invalid table '$table'");

		$this->tableName[$table] = $tableName;
		return $this;
	}

	/**
	 * Returns table name
	 *
	 * @return string table name
	 * @throws InvalidArgumentException if invalid table requested
	 */
	public function getTableName($table) {
		if(!isset($this->tableName[$table]))
			throw new \InvalidArgumentException("Table name for '$table' not set");

		return $this->tableName[$table];
	}


}
