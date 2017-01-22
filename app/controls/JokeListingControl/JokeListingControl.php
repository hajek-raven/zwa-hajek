<?php

namespace App\Controls;

use App\Model\JokeModel;
use Nette\Application\UI\Control;

class JokeListingControl extends Control  {

	/** @var JokeModel */
	private $jokeModel;


	/**
	 * JokeNavigationControl constructor.
	 * @param JokeModel $jokeModel
	 */
	public function __construct(JokeModel $jokeModel) {
		parent::__construct();

		$this->jokeModel = $jokeModel;
	}


	public function render($categoryId) {
		$this->template->setFile(__DIR__ . '/joke-listing-control.latte');

		$this->template->jokes = $this->jokeModel->getJokeDs()->where('category_id = %i', $categoryId);

		$this->template->render();

	}
}