<?php

namespace App\Model;

use App\Model\Entities\Joke;
use Dibi\Fluent;

/**
 * Joke data model.
 */
class JokeModel extends DatabaseModel {

	const JOKE_NOT_FOUND = 100;

	// -------------------------------------------------------------------------
	// CATEGORIES
	// -------------------------------------------------------------------------

	/**
	 * All jokess
	 *
	 * @return Fluent
	 */
	public function getJokeDs() {
		$ds = $this->dbConnection
			->select('j.*, c.title as categoryName')
			->from($this->getTableName(self::TABLE_JOKES))->as('j')
			->leftJoin($this->getTableName(self::TABLE_CATEGORY))->as('c')
			->on('j.category_id = c.id');

		$ds->setupResult('setRowFactory', function ($rowData) {
			return Joke::createFromDb($rowData);
		});

		return $ds;
	}

	/**
	 * Returns joke
	 *
	 * @param $id
	 * @return Joke|FALSE
	 * @throws \Exception
	 */
	public function getJoke($id) {
		$ds = $this->getJokeDs();
		$ds->where('[j.id] = %i', $id);

		$joke = $ds->fetch();
		if(!$joke)
			throw new \Exception("Joke #$id does not exist", self::JOKE_NOT_FOUND);

		return $joke;
	}


	/**
	 * Saves joke data.
	 *
	 * @param Joke $joke
	 * @return int joke id
	 * @throws \Exception
	 */
	function saveJoke(Joke $joke) {

		$this->dbConnection->begin();

		// Check if joke can be modified
		if($joke->id) {
			$state = $this->dbConnection->query(
				'SELECT [id] FROM %n',
				$this->getTableName(self::TABLE_JOKES),
				'WHERE [id] = %i',
				$joke->id,
				'FOR UPDATE'
			)->fetchSingle();

			if($state === FALSE)
				throw new \Exception("Joke #$joke->id does not exist", self::JOKE_NOT_FOUND);
		}



		// Joke data
		$this->dbConnection->query(
			'INSERT INTO %n %v ON DUPLICATE KEY UPDATE %a',
			$this->getTableName(self::TABLE_JOKES),
			$data = array(
				'id' => $joke->id,
				'content' => $joke->content,
				'category_id' => $joke->category_id
			),
			$data
		);

		// Check for insert ID
		$id = $joke->id ?: $this->dbConnection->getInsertId();

		$this->dbConnection->commit();

		return $joke->id = $id;
	}


	/**
	 * @param $id
	 * @throws \Exception
	 */
	public function deleteJoke($id) {
		$this->dbConnection->begin();

		// Check if joke can be modified
		$state = $this->dbConnection->query(
			'SELECT [id] FROM %n',
			$this->getTableName(self::TABLE_JOKES),
			'WHERE [id] = %i',
			$id,
			'FOR UPDATE'
		)->fetchSingle();

		if($state === FALSE)
			throw new \Exception("JOKE #$id does not exist", self::JOKE_NOT_FOUND);

		$this->dbConnection->query(
			'DELETE FROM %n', $this->getTableName(self::TABLE_JOKES),
			'WHERE [id] = %i', $id
		);

		$this->dbConnection->commit();
	}



}
