<?php

namespace Grapesc\GrapeFluid\PhotoGallery;

use Grapesc\GrapeFluid\BaseParametersRepository;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\PhotoModel;
use Nette\Database\Table\IRow;
use Nette\Utils\Finder;

/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ImageRepository
{

	/** @var string */
	private $directoryPath;

	/** @var string */
	private $galleryIdPrefix;

	/** @var PhotoModel */
	private $photoModel;

	/** @var GalleryModel */
	private $galleryModel;

	/** @var BaseParametersRepository */
	private $parametersRepository;


	public function __construct($directoryPath, $galleryIdPrefix = "gallery-", PhotoModel $photoModel, GalleryModel $galleryModel, BaseParametersRepository $parametersRepository)
	{
		if (!is_dir($directoryPath)) {
			if (!@mkdir($directoryPath, $parametersRepository->getParam('dirPerm'), true)) {
				throw new \InvalidArgumentException("Directory path $directoryPath for photogallery isn't accessible");
			}
		}

		$this->directoryPath        = $directoryPath;
		$this->galleryIdPrefix      = $galleryIdPrefix;
		$this->photoModel           = $photoModel;
		$this->galleryModel         = $galleryModel;
		$this->parametersRepository = $parametersRepository;
	}


	/**
	 * @param int $galleryId
	 * @param bool $createIfNotExists
	 * @return string
	 */
	public function getGalleryPath($galleryId, $createIfNotExists = false)
	{
		$path = $this->directoryPath . DIRECTORY_SEPARATOR . $this->galleryIdPrefix . $galleryId;

		if ($createIfNotExists AND !is_dir($path)) {
			mkdir($path, 0775, true);
		}

		return $path;
	}


	/**
	 * @param int|IRow $photoId
	 * @return bool
	 */
	public function deletePhoto($photoId)
	{
		if (is_object($photoId) AND $photoId instanceof IRow) {
			$item = $photoId;
		} else {
			$item = $this->photoModel->getItem($photoId);
		}

		if ($item) {
			$galleryId   = $item->photogallery_gallery_id;
			$galleryPath = $this->getGalleryPath($galleryId);

			foreach (Finder::findFiles($item->filename)->from($galleryPath) AS $file) {
				unlink($file->getPathname());
			}

			$isMain = $item->is_main;
			$item->delete();

			if ($isMain) {
				$this->photoModel->getItemsBy($galleryId,'photogallery_gallery_id')->limit(1)->order('id')->update(['is_main' => 1]);
			}

			return true;
		}

		return false;
	}


	/**
	 * @param int $galleryId
	 * @return bool
	 */
	public function deleteGallery($galleryId)
	{
		$gallery = $this->galleryModel->getItem($galleryId);

		if (!$gallery) {
			return false;
		}

		$photos = $this->photoModel->getItemsBy($galleryId, "photogallery_gallery_id");

		foreach ($photos AS $photo) {
			$this->deletePhoto($photo);
		}

		$gallery->delete();

		if (is_dir($this->getGalleryPath($galleryId))) {
			foreach (Finder::findDirectories('*')->in($this->getGalleryPath($galleryId)) AS $dir) {
				rmdir($dir->getPathname());
			}

			return rmdir($this->getGalleryPath($galleryId));
		} else {
			return true;
		}
	}

}