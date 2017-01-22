<?php

namespace App\Controls;

use App\Model\CategoryModel;
use Nette\Application\UI\Control;

class MenuControl extends Control  {

	/** @var CategoryModel */
	private $categoryModel;


	/**
	 * CategoryNavigationControl constructor.
	 * @param CategoryModel $categoryModel
	 */
	public function __construct(CategoryModel $categoryModel) {
		parent::__construct();

		$this->categoryModel = $categoryModel;
	}


	public function render($actualId) {
		$this->template->setFile(__DIR__ . '/menu-control.latte');

		$this->template->actualId = $actualId;
		$this->template->categories = $this->categoryModel->getCategoryDs()->where('parent_id IS NULL');

		$this->template->render();

	}
}