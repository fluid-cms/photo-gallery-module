<?php

namespace Grapesc\GrapeFluid\PhotoGalleryModule\Presenters;

use Grapesc\GrapeFluid\Application\BasePresenter;
use Grapesc\GrapeFluid\PhotoGallery\ImageLoader;
use Nette\Application\Responses\FileResponse;
use Nette\Http\Response;
use Nette\Utils\Image;

class PhotoPresenter extends BasePresenter
{

	/** @var ImageLoader @inject */
	public $imageLoader;


	public function actionDefault($galleryId, $galleryName, $fileName, $size)
	{
		$file = $this->imageLoader->getImageFile($galleryId, $fileName, $size);
		if ($file) {
			header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT");
//			header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()) . " GMT");
			header("Cache-Control: max-age=31536000, must-revalidate, proxy-revalidate");
			header("Pragma: cache");
			header('Content-Type: image/' . $file->getExtension());
			echo file_get_contents($file->getPathname());
			die;
		}
	}

}