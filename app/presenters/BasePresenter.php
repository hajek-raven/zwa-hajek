<?php

namespace App\Presenters;

use App\Controls\IMenuControlFactory;
use Nette;


class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var IMenuControlFactory @inject */
	public $menuControlFactory;

	public function createComponentMenu() {
		return $this->menuControlFactory->create();
	}

	public function startup() {
		parent::startup();

		// Set dafault menuId
		$this->template->menuId = -1;
	}
}
