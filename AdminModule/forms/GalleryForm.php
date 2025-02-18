<?php

namespace Grapesc\GrapeFluid\PhotoGallery;

use Grapesc\GrapeFluid\AdminModule\Model\UserModel;
use Grapesc\GrapeFluid\FluidFormControl\FluidForm;
use Grapesc\GrapeFluid\ModuleRepository;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Nette\Security\User;


class GalleryForm extends FluidForm
{

	/** @var GalleryModel @inject */
	public $model;

	/** @var ModuleRepository @inject */
	public $moduleRepository;

	/** @var User @inject */
	public $user;

	/** @var UserModel @inject */
	public $userModel;


	/**
	 * @param Form $form
	 */
	protected function build(Form $form)
	{
		$form->addHidden("id");

		$form->addText("name", "Název galerie")
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 80)
			->setRequired("Vyplntě název galerie");

		$form->addText("keywords", "Klíčová slova (SEO keywords)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->setAttribute("help", "Jednotlivá slova (oddělujte čárkou)")
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200);

		$form->addTextArea("description", "Popis galerie", null, 6)
			->setRequired(false)
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 65535);

		$form->addSelect('author_id', 'Autor', $this->userModel->getTableSelection()->fetchPairs('id', 'name'))
			->setPrompt("Zvolte autora")
			->setAttribute("cols", 6)
			->setRequired(false);

		$form->addText('author_name', 'Jméno autora')
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 64)
			->setRequired(false);

		$form->addCheckbox('is_publish', 'Publikovat')
			->setOption('cols', 2)
			->setDefaultValue(true);

		if ($this->moduleRepository->moduleExist('comment')) {
			$form->addCheckbox('enable_comments', 'Povolit komentáře')
				->setOption('cols', 2)
				->setDefaultValue(true);
		}
	}


	/**
	 * @param Control $control
	 * @param Form $form
	 */
	public function onSuccessEvent(Control $control, Form $form)
	{
		parent::onSuccessEvent($control, $form);

		$presenter = $control->getPresenter();
		$values    = $form->getValues('array');

		if ($values['id']) {
			$this->model->update($values, $values['id']);
			$presenter->flashMessage("Změny uloženy", "success");
			$presenter->redirect('gallery');
		} else {
			unset($values['id']);

			$values['created_by_id'] = $this->user->getId();
			$values['created_on']    = new DateTime();

			$createdId = $this->model->insert($values)->id;
			$presenter->flashMessage("Galerie vytvořena, můžete nahrát fotografie", "success");
			$presenter->redirect("galleryPhotos", $createdId);
		}
	}

}