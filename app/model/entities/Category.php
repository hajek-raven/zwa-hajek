<?php

namespace App\Model\Entities;

use App\Model\DatabaseEntity;

/**
 * Category entity
 */
class Category extends DatabaseEntity {

	/** @var int */
	public $id;

	/** @var string */
	public $title;

	/** @var int */
	public $parent_id = NULL;

}