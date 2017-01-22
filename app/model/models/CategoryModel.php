<?php

namespace App\Model;

use App\Model\Entities\Category;
use Dibi\Fluent;

/**
 * Category data model.
 */
class CategoryModel extends DatabaseModel {

	const CATEGORY_NOT_FOUND = 100;

	// -------------------------------------------------------------------------
	// CATEGORIES
	// -------------------------------------------------------------------------

	/**
	 * All categories
	 *
	 * @return Fluent
	 */
	public function getCategoryDs() {
		$ds = $this->dbConnection
			->select('*')
			->from($this->getTableName(self::TABLE_CATEGORY))
			->as('s');

		$ds->setupResult('setRowFactory', function ($rowData) {
			return Category::createFromDb($rowData);
		});

		return $ds;
	}

	/**
	 * @return array
	 */
	public function getCategoryPairs($rootsOnly = TRUE) {
		$ds = $this->dbConnection
			->select('*')
			->from($this->getTableName(self::TABLE_CATEGORY));

		if ($rootsOnly) {
			$ds->where('parent_id IS NULL');
		}

		return $ds->fetchPairs('id', 'title');
	}

	/**
	 * Returns category
	 *
	 * @param $id
	 * @return Category|FALSE
	 * @throws \Exception
	 */
	public function getCategory($id) {
		$ds = $this->getCategoryDs();
		$ds->where('[s.id] = %i', $id);

		$category = $ds->fetch();
		if(!$category)
			throw new \Exception("Category #$id does not exist", self::CATEGORY_NOT_FOUND);

		return $category;
	}


	/**
	 * Saves category data.
	 *
	 * @param Category $category
	 * @return int category id
	 * @throws \Exception
	 */
	function saveCategory(Category $category) {

		$this->dbConnection->begin();

		// Check if category can be modified
		if($category->id) {
			$state = $this->dbConnection->query(
				'SELECT [id] FROM %n',
				$this->getTableName(self::TABLE_CATEGORY),
				'WHERE [id] = %i',
				$category->id,
				'FOR UPDATE'
			)->fetchSingle();

			if($state === FALSE)
				throw new \Exception("Category #$category->id does not exist", self::CATEGORY_NOT_FOUND);
		}



		// Category data
		$this->dbConnection->query(
			'INSERT INTO %n %v ON DUPLICATE KEY UPDATE %a',
			$this->getTableName(self::TABLE_CATEGORY),
			$data = array(
				'id' => $category->id,
				'title' => $category->title,
				'parent_id' => $category->parent_id
			),
			$data
		);

		// Check for insert ID
		$id = $category->id ?: $this->dbConnection->getInsertId();

		$this->dbConnection->commit();

		return $category->id = $id;
	}


	/**
	 * @param $id
	 * @throws \Exception
	 */
	public function deleteCategory($id) {
		$this->dbConnection->begin();

		// Check if category can be modified
		$state = $this->dbConnection->query(
			'SELECT [id] FROM %n',
			$this->getTableName(self::TABLE_CATEGORY),
			'WHERE [id] = %i',
			$id,
			'FOR UPDATE'
		)->fetchSingle();

		if($state === FALSE)
			throw new \Exception("CATEGORY #$id does not exist", self::CATEGORY_NOT_FOUND);

		$this->dbConnection->query(
			'DELETE FROM %n', $this->getTableName(self::TABLE_CATEGORY),
			'WHERE [id] = %i', $id
		);

		$this->dbConnection->commit();
	}



}
