<?php

namespace App\Model\Work\Entity;

use \App\Model\DatabaseEntity;

/**
 * User
 */
class User extends DatabaseEntity {

	/** @var int */
	public $id;

	/** @var string */
	public $firstname;

	/** @var string */
	public $lastname;

}