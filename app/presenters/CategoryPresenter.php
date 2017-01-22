<?php

namespace App\Presenters;

use App\Controls\IJokeListingControlFactory;
use App\Model\CategoryModel;
use Nette;


class CategoryPresenter extends BasePresenter {

	/** @var CategoryModel @inject */
	public $categoryModel;

	/** @var IJokeListingControlFactory @inject */
	public $jokeListFactory;

	public function actionDetail($id) {
		try {
			$category = $this->categoryModel->getCategory($id);
			$this->template->category = $category;
			$this->template->menuId = $category->id;
		} catch (\Exception $e) {
			if($e->getCode() === CategoryModel::CATEGORY_NOT_FOUND) {
				throw new Nette\Application\BadRequestException("Category with id #$id does not exist.", 404);
			} else {
				throw new Nette\Application\BadRequestException("Server error.", 500);
			}
		}
	}

	public function createComponentJokeListing() {
		$ctrl = $this->jokeListFactory->create();

		return $ctrl;
	}
}
