<?php

namespace App\Presenters;

use App\Controls\IMenuControlFactory;
use App\Model\CategoryModel;
use App\Model\Entities\Category;
use App\Model\Entities\Joke;
use App\Model\JokeModel;
use Nette;
use IPub\VisualPaginator\Components as VisualPaginator;


class AdminPresenter extends Nette\Application\UI\Presenter {

	/** @var CategoryModel @inject */
	public $categoryModel;

	/** @var JokeModel @inject */
	public $jokeModel;

	public function startup() {
		parent::startup();
		$user = $this->getUser();

		if(!$user->isLoggedIn() && $this->action !== "in") {
			$this->flashMessage('Nejdříve se musít přihlásit.', 'danger');
			$this->redirect('Admin:in');
		}
	}

	// -------------------------------------------------------------------------
	// JOKES
	// -------------------------------------------------------------------------

	public function handleJokeDelete($id) {
		try {
			$this->jokeModel->deleteJoke($id);
		} catch (\Exception $e) {
			$this->flashMessage('Při mazání nastala chyba.', 'danger');
			$this->redirect('joke');
		}

		if(!$this->isAjax()) {
			$this->redirect('joke');
		}

		$this->redrawControl('jokeListing');
	}


	public function renderJoke() {

		$jokes = $this->jokeModel->getJokeDs();
		$visualPaginator = $this['visualPaginator'];
		$paginator = $visualPaginator->getPaginator();
		$paginator->itemsPerPage = 5;
		$paginator->itemCount = count($jokes);


		$this->template->jokes = $jokes->fetchAll($paginator->offset, $paginator->itemsPerPage);
	}


	public function actionJokeEdit($id) {
		$this->template->joke = $joke = $this->jokeModel->getJoke($id);

		$this['addJokeForm']['id']->setDefaultValue($joke->id);
		$this['addJokeForm']['content']->setDefaultValue($joke->content);
		$this['addJokeForm']['category_id']->setDefaultValue($joke->category_id);
	}


	// -------------------------------------------------------------------------
	// CATEGORIES
	// -------------------------------------------------------------------------

	public function handleCategoryDelete($id) {
		try {
			$this->categoryModel->deleteCategory($id);
		} catch (\Exception $e) {
			$this->flashMessage('Při mazání nastala chyba.', 'danger');
			$this->redirect('category');
		}

		if(!$this->isAjax()) {
			$this->redirect('category');
		}

		$this->redrawControl('categoryListing');
	}


	public function renderCategory() {

		$categories = $this->categoryModel->getCategoryDs();
		$visualPaginator = $this['visualPaginator'];
		$paginator = $visualPaginator->getPaginator();
		$paginator->itemsPerPage = 5;
		$paginator->itemCount = count($categories);


		$this->template->categories = $categories->fetchAll($paginator->offset, $paginator->itemsPerPage);
	}


	public function actionCategoryEdit($id) {
		$this->template->category = $category = $this->categoryModel->getCategory($id);

		$this['addCategoryForm']['id']->setDefaultValue($category->id);
		$this['addCategoryForm']['title']->setDefaultValue($category->title);
		$this['addCategoryForm']['parent_id']->setDefaultValue($category->parent_id);
	}


	// -------------------------------------------------------------------------
	// LOGIN
	// -------------------------------------------------------------------------


	public function actionIn() { }

	public function actionOut() {
		$this->user->logout();

		$this->flashMessage('Byl jste úspěšně odhlášen.', 'success');
		$this->redirect('in');
	}


	// -------------------------------------------------------------------------
	// COMPONENT FACTORIES
	// -------------------------------------------------------------------------


	public function createComponentSignInForm() {
		$form = new Nette\Application\UI\Form();

		$form->addText('username', "Uživatelské jméno")
			->setRequired();

		$form->addPassword('password', "Heslo")
			->setRequired();


		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;
	}


	public function signInFormSucceeded($form, $values) {

		try {
			$this->user->login($values->username, $values->password);
		} catch (\Exception $e) {
			$this->flashMessage('Neprávné jméno nebo heslo.', 'danger');
			$this->redirect('in');
		}

		$this->redirect('default');
	}


	protected function createComponentVisualPaginator()
	{

		$control = new VisualPaginator\Control();

		$control->setTemplateFile('bootstrap.latte');

		$_this = $this;
		$control->onShowPage[] = (function ($component, $page) use ($_this) {
			if ($_this->isAjax()){
				$_this->redrawControl();
			}
		});

		//$control->disableAjax();

		return $control;
	}

	public function createComponentAddCategoryForm() {
		$form = new Nette\Application\UI\Form();


		$form->addHidden('id');

		$form->addText('title', "Název kategorie")
			->setRequired();


		$rootCategories = $this->categoryModel->getCategoryPairs();
		array_unshift($rootCategories, "-- Zádná nadřazená kategorie --");
		$form->addSelect('parent_id', 'Nadřazená kategorie', $rootCategories)
			->setRequired();

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = [$this, 'addCategoryFormSucceeded'];

		return $form;
	}


	public function addCategoryFormSucceeded($from, $values) {
		$category = $values->id ? $this->categoryModel->getCategory($values->id) : new Category();

		$category->title = $values->title;
		if($values->parent_id != 0) {
			$category->parent_id = $values->parent_id;
		}

		$this->categoryModel->saveCategory($category);

		$this->flashMessage($values->id ? "Kategorie byla upravena" : "Nová kategorie přidána", "success");
		$this->redirect('category');
	}

	public function createComponentAddJokeForm() {
		$form = new Nette\Application\UI\Form();

		$form->addHidden('id');

		$form->addTextArea('content', "Text vtipu")
			->setRequired();


		$rootCategories = $this->categoryModel->getCategoryPairs(FALSE);
		$form->addSelect('category_id', 'Kategorie', $rootCategories)
			->setRequired();

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = [$this, 'addJokeFormSucceeded'];

		return $form;
	}


	public function addJokeFormSucceeded($from, $values) {
		$joke = $values->id ? $this->jokeModel->getJoke($values->id) : new Joke();

		$joke->content = $values->content;
		$joke->category_id = $values->category_id;

		$this->jokeModel->saveJoke($joke);

		$this->flashMessage($values->id ? "Vtip byl úspěšně upraven." : "Nový vtip přidán", "success");
		$this->redirect('joke');
	}

}
