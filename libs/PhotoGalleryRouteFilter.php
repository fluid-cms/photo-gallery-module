<?php
namespace Grapesc\GrapeFluid\PhotoGalleryModule\RouteFilter;

use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Nette\Utils\Strings;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class PhotoGallery
{

	/** @var GalleryModel */
	private $galleryModel;


	public function __construct(GalleryModel $galleryModel)
	{
		$this->galleryModel = $galleryModel;
	}


	/**
	 * @param int $arg
	 * @return string
	 */
	public function filterOut($arg)
	{
		$gallery = $this->galleryModel->getItem($arg);
		return $gallery ? ($arg . '-' . Strings::webalize($gallery->name)) : $arg;
	}


	/**
	 * @param string $arg
	 * @return int
	 */
	public function filterIn($arg)
	{
		return explode('-', $arg)[0];
	}

}