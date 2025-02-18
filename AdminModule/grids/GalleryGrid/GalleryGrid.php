<?php

namespace Grapesc\GrapeFluid\PhotoGalleryModule\Grid;

use Grapesc\GrapeFluid\FluidGrid;
use Grapesc\GrapeFluid\PhotoGallery\ImageRepository;
use Nette\Database\Table\ActiveRow;
use TwiGrid\Components\Column;


/**
 * @model \Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel
 */
class GalleryGrid extends FluidGrid
{

	/** @var ImageRepository @inject */
	public $imageRepository;


	protected function build(): void
	{
		$this->setItemsPerPage(15);
		$this->skipColumns(["keywords", "created_by_id", "author_name", "enable_comments", "description"]);
		$this->enableFilters(['name']);
		$this->setSortableColumns(['name', 'created_on']);
		$this->setDefaultOrderBy("created_on", Column::DESC);

		$this->addRowAction("photos", "Spravovat fotografie", [$this, 'photos']);
		$this->addRowAction("edit", "Upravit", [$this, 'editGallery']);
		$this->addRowAction("delete", "Smazat", [$this, 'deleteGallery']);

		$this->addRowAction("changePublish", "Změna publikace", function (ActiveRow $record) {
			try {
				$data = ['is_publish' => !$record->is_publish];
				$this->model->update($data, $record->id);

				if ($record->is_publish) {
					$this->flashMessage("Publikace galerie '$record->name' zrušena", "success");
				} else {
					$this->flashMessage("Galerie '$record->name' publikována", "success");
				}
			} catch (\Exception $e) {
				$this->flashMessage("Nepodařilo se změnit publikaci", "error");
			}

			$this->getPresenter()->redrawControl('galleryGrid');
			$this->getPresenter()->redrawControl('flashMessages');
		});

		parent::build();
		$this->addColumn("count");
	}


	public function deleteGallery(ActiveRow $record)
	{
		if ($this->imageRepository->deleteGallery($record->id)) {
			$this->getPresenter()->flashMessage("Galerie smazána", "success");
		} else {
			$this->getPresenter()->flashMessage("Galerii se nepodařilo odstranit", "danger");
		}
		$this->getPresenter()->redrawControl("flashMessages");
	}


	public function editGallery(ActiveRow $record)
	{
		$this->getPresenter()->redirect("gallery-form", ["id" => $record->id]);
	}


	public function photos(ActiveRow $record)
	{
		$this->getPresenter()->redirect("gallery-photos", ["id" => $record->id]);
	}

}