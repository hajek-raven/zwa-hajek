<?php

namespace App\Presenters;

use App\Controls\IJokeListingControlFactory;
use App\Model\CategoryModel;
use App\Model\JokeModel;
use Nette;


class JokePresenter extends BasePresenter {

	/** @var JokeModel @inject */
	public $jokeModel;

	public function actionDetail($id) {
		try {
			$joke = $this->jokeModel->getJoke($id);
			$this->template->joke = $joke;
			$this->template->menuId = $joke->id;
		} catch (\Exception $e) {
			if($e->getCode() === JokeModel::JOKE_NOT_FOUND) {
				throw new Nette\Application\BadRequestException("Joke with id #$id does not exist.", 404);
			} else {
				throw new Nette\Application\BadRequestException("Server error.", 500);
			}
		}
	}
}
