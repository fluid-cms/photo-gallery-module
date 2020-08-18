<?php

namespace Grapesc\GrapeFluid\PhotoGalleryModule\Control\PhotoGallery;

use Grapesc\GrapeFluid\MagicControl\BaseMagicTemplateControl;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\PhotoModel;

/**
 * Class PhotoGalleryControl
 * @package Grapesc\GrapeFluid\PhotoGalleryModule\Control
 * @usage: {magicControl photoGallery, ['uid', 'int galleryId']}
 */
class PhotoGalleryControl extends BaseMagicTemplateControl
{

	/** @var PhotoModel @inject */
	public $photoModel;

	/** @var GalleryModel @inject */
	public $galleryModel;

	/** @var string|null */
	private $galleryId = null;

	/** @var string|null */
	protected $defaultTemplateFilename =  __DIR__ . '/photoGallery.latte';


	/**
	 * @param array $params
	 */
	public function setParams(array $params = [])
	{
		$params = array_values($params);

		if (isset($params[0]) && $params[0]) {
			$this->galleryId = $params[0];
		} else {
			throw new \InvalidArgumentException("Id of gallery must be set");
		}
	}


	public function render()
	{
		$this->template->photos  = $this->photoModel->getItemsBy($this->galleryId, 'photogallery_gallery_id');
		$this->template->gallery = $this->galleryModel->getItem($this->galleryId);
		$this->template->render();
	}

}
