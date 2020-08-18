<?php

namespace Grapesc\GrapeFluid\PhotoGalleryModule\Model;

use Grapesc\GrapeFluid\Model\BaseModel;


class PhotoModel extends BaseModel
{

	/**
	 * @param $galleryId
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function getMainPhotoByGalleryId($galleryId)
	{
		return $this->getTableSelection()
			->where('photogallery_gallery_id', $galleryId)
			->where('is_main', 1)
			->fetch();
	}

}