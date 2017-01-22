<?php

namespace App\Model\Entities;

use App\Model\DatabaseEntity;

/**
 * Joke entity
 */
class Joke extends DatabaseEntity {

	/** @var int */
	public $id;

	/** @var string */
	public $content;

	/** @var int */
	public $category_id = NULL;

	/** @var string */
	public $categoryName;

	/** @var string */
	public $type = 'text';

}