<?php

namespace Grapesc\GrapeFluid\PhotoGallery;
use Nette\Utils\Image;
use function PHPSTORM_META\type;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ImageLoader
{

	/** @var ImageRepository */
	private $imageRepository;


	public function __construct(ImageRepository $imageRepository)
	{
		$this->imageRepository = $imageRepository;
	}


	/**
	 * @param $galleryId
	 * @param $fileName
	 * @param array|null|string $size
	 * @return null|\SplFileInfo
	 */
	public function getImageFile($galleryId, $fileName, $size = null)
	{
		$galleryPath = $this->imageRepository->getGalleryPath($galleryId);
		$filePath    = $galleryPath . DIRECTORY_SEPARATOR . $fileName;

		if (is_string($size) AND strpos($size,"x") !== false) {
			$size = explode("x", $size);
		}

		if (is_array($size) AND count($size) === 2) {
			$size    = array_values($size);
			$width   = (int)$size[0];
			$height  = (int)$size[1];

			$filePath = $this->getFilePath($galleryId, $fileName, $width, $height);
		}

		if (file_exists($filePath)) {
			return new \SplFileInfo($filePath);
		} else {
			return null;
		}
	}

	/**
	 * @param int $galleryId
	 * @param string $fileName
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	private function getFilePath($galleryId, $fileName, $width, $height)
	{
		$galleryPath      = $this->imageRepository->getGalleryPath($galleryId);
		$originalFilePath = $galleryPath . DIRECTORY_SEPARATOR . $fileName;

		if (!is_int($width) || !is_int($height) || $width <= 0 || $height <= 0) {
			return $originalFilePath;
		}

		$resizeGalleryImagePath = $galleryPath . DIRECTORY_SEPARATOR . (implode("x", [$width, $height]));
		$resizeFilePath         = $resizeGalleryImagePath . DIRECTORY_SEPARATOR . $fileName;

		if (!is_dir($resizeGalleryImagePath)) {
			mkdir($resizeGalleryImagePath, 0775, true);
		}

		if (!is_file($resizeFilePath)) {
			 $image = Image::fromFile($originalFilePath);
			 $image->resize($width, $height, Image::EXACT);
			 $image->save($resizeFilePath);
		}

		return $resizeFilePath;
	}

}