<?php

namespace Grapesc\GrapeFluid\PhotoGallery;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\PhotoModel;
use Nette\Http\FileUpload;
use Nette\Utils\ImageException;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ImageUploader
{

	/** @var ImageRepository */
	private $imageRepository;

	/** @var PhotoModel */
	private $photoModel;


	public function __construct(ImageRepository $imageRepository, PhotoModel $photoModel)
	{
		$this->imageRepository = $imageRepository;
		$this->photoModel      = $photoModel;
	}


	/**
	 * @param FileUpload[] $filesUploads
	 * @param int $galleryId
	 * @return int
	 */
	public function uploadImage(array $filesUploads, $galleryId)
	{
		$processedCount = 0;
		$isFirstInGallery = !(boolean) $this->photoModel->getItemBy($galleryId, 'photogallery_gallery_id');

		//@todo max width
		foreach ($filesUploads AS $fileUpload) {
			if ($fileUpload->isOk() AND $fileUpload->isImage()) {
				$filename = $this->getUniqueName($fileUpload->getSanitizedName());
				$filePath = $this->imageRepository->getGalleryPath($galleryId, true) . DIRECTORY_SEPARATOR . $filename;
				try {
					$fileUpload->toImage()->save($filePath);
					$this->photoModel->insert([
						'name'                    => $fileUpload->getName(),
						'filepath'                => $filePath,
						'filename'                => $filename,
						'size'                    => $fileUpload->getSize(),
						'photogallery_gallery_id' => $galleryId,
						'is_main'                 => $isFirstInGallery
					]);
					$isFirstInGallery = false;
					$processedCount++;

				} catch (ImageException $e) {

				}
			}
		}

		return $processedCount;
	}


	/**
	 * @param string $fileName
	 * @return string
	 */
	protected function getUniqueName($fileName)
	{
		return microtime(true) . $fileName;
	}

}