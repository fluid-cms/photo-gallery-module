<?php

namespace Grapesc\GrapeFluid\PhotoGalleryModule\Presenters;

use Grapesc\GrapeFluid\Application\BasePresenter;
use Grapesc\GrapeFluid\PhotoGalleryModule\Model\GalleryModel;
use Nette\Application\BadRequestException;
use Nette\Utils\DateTime;
use Nette\Utils\Paginator;

class GalleryPresenter extends BasePresenter
{

	/**
	 * @var int
	 * @persistent
	 */
	public $page = 1;

	/** @var GalleryModel @inject */
	public $galleryModel;

	/** @var int */
	private $itemPerPage = 20;

	/** @var int */
	private $pageMove = 4;


	public function actionList($filter = [])
	{
		$itemCount = $this->getGallerySelection($this->galleryModel->getTableSelection()->select("count(*)"), $filter)->fetchField();
		$paginator = new Paginator();

		$paginator->setItemCount($itemCount);
		$paginator->setItemsPerPage($this->itemPerPage);
		$paginator->setPage($this->page);

		if ($this->page > ceil($itemCount / $this->itemPerPage)) {
			$this->page = $itemCount > 0 ? ceil($itemCount / $this->itemPerPage) : 0;
		}

		$this->template->paginator = $paginator;
		$this->template->pageMove  = $this->pageMove;

		$galleries = $this->getGallerySelection($this->galleryModel->getTableSelection(), $filter)
			->limit($this->itemPerPage, ($this->page > 0 ? $this->page - 1 : 0) * $this->itemPerPage)
			->order('created_on DESC');

		$this->template->galleries = $galleries;
		$this->breadCrumbService->addLink('Fotogalerie');
	}


	public function actionDefault($galleryId, $galleryName)
	{
		$gallery = $this->galleryModel->getItem($galleryId);

		if (!$gallery) {
			$this->error('Galerie neexistuje');
		}

		if (!$gallery->is_publish) {
			if (!$this->user->isAllowed('backend:photoGallery')) {
				throw new BadRequestException;
			} else {
				$this->template->noPublishedAlert = true;
			}
		} else {
			$this->template->noPublishedAlert = false;
		}

		$this->template->gallery = $gallery;

		$this->breadCrumbService->addLink('Fotogalerie', [':PhotoGallery:Gallery:list']);
		$this->breadCrumbService->addLink($gallery->name);
	}


	private function getGallerySelection($selection, $filter = [])
	{
		$selection->where('photogallery_gallery.is_publish', 1)
			->where(':article_page.photogallery_gallery_id IS NULL');

		if (array_key_exists('author_name', $filter)) {
			$selection->where('photogallery_gallery.author_name LIKE ?', str_replace('-', ' ', $filter['author_name']));
		} elseif (array_key_exists('author_id', $filter)) {
			$selection->where('photogallery_gallery.author_id', (int)$filter['author_id'])
				->where('(photogallery_gallery.author_name IS NULL OR photogallery_gallery.author_name = ?)', '');
		}

		if (array_key_exists('yearMonth', $filter)) {
			$selection->where('photogallery_gallery.created_on >= ?', DateTime::from($filter['yearMonth']))
			->where('photogallery_gallery.created_on < ?', DateTime::from($filter['yearMonth'])->modify('+1 month'));
		}

		return $selection;
	}

}