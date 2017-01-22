<?php

namespace App\Model;

use Nette;

/**
 * Base database entity
 */
class DatabaseEntity extends Nette\Object {

    /**
     * Loads data from DB
     *
     * @param array $rowData
     * @return static
     */
	static function createFromDb(array $rowData = array()) {
		$_this = new static;

		foreach($rowData as $key => $value)
			$_this->{$key} = $value;

		return $_this;
	}

}