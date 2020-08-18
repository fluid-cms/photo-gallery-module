<?php

namespace Grapesc\GrapeFluid\AdminModule\Presenters;

use Grapesc\GrapeFluid\FluidFormControl\FluidFormFactory;
use Grapesc\GrapeFluid\FluidGrid\FluidGridFactory;
use Grapesc\GrapeFluid\PhotoGallery\ImageRepository;
use Grapesc\GrapeFluid\PhotoGallery\ImageUploader;
use Grapesc\GrapeFluid\PhotoGalleryModule\Grid\GalleryGrid;
use Grapesc\GrapeFluid\PhotoGallery\GalleryForm;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\PhotoModel;

class PhotoGalleryPresenter extends BasePresenter
{

	/** @var FluidFormFactory @inject */
	public $fluidFormFactory;

	/** @var FluidGridFactory @inject */
	public $fluidGridFactory;

	/** @var GalleryModel @inject */
	public $galleryModel;

	/** @var ImageUploader @inject */
	public $imageUploader;

	/** @var ImageRepository @inject */
	public $imageRepository;

	/** @var PhotoModel @inject */
	public $photoModel;


	public function renderGalleryForm($id = null)
	{
		if ($id) {
			$gallery = $this->galleryModel->getItem($id);
			if (!$gallery) {
				$this->flashMessage("Galerie neexistuje", "warning");
				$this->redirect("gallery");
			}
			$this->getComponent("galleryForm")->setDefaults($this->galleryModel->getItem($id));
		}
	}


	public function renderGalleryPhotos($id)
	{
		$gallery = $this->galleryModel->getItem($id);

		if (!$gallery) {
			$this->flashMessage("Galerie neexistuje", "warning");
			$this->redirect("gallery");
		}

		$this->template->gallery = $gallery;
	}


	public function handleUpload($galleryId)
	{
		$filesUploads  = $this->getHttpRequest()->getFiles();
		$sucessedCount = $this->imageUploader->uploadImage($filesUploads, $galleryId);
		$this->payload->errors = count($filesUploads) - $sucessedCount;
		$this->sendPayload();
	}


	public function handleDelete($photoId, $galleryId)
	{
		if ($this->imageRepository->deletePhoto($photoId)) {
			$this->flashMessage('Fotografie odebrána');
		} else {
			$this->flashMessage('Fotografii se nepodařilo odebrat', 'danger');
		}

		$this->redrawControl('flashMessages');
		$this->redrawControl('photos');
	}


	public function handleSetMain($photoId, $galleryId)
	{
		$this->photoModel->getTableSelection()->where("photogallery_gallery_id", $galleryId)->update(['is_main' => 0]);
		$this->photoModel->getTableSelection()->where("id", $photoId)->update(['is_main' => 1]);

		$this->flashMessage('Fotografie nastavena jako hlavní', 'success');
		$this->redrawControl('flashMessages');
		$this->redrawControl('photos');
	}


	public function handleRefreshPhotos($galleryId)
	{
		$this->redrawControl('photos');
	}


	public function createComponentGalleryForm()
	{
		return $this->fluidFormFactory->create(GalleryForm::class);
	}


	public function createComponentGalleryGrid()
	{
		return $this->fluidGridFactory->create(GalleryGrid::class);
	}

}