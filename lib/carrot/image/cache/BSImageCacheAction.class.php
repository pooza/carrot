<?php
/**
 * @package org.carrot-framework
 * @subpackage image.cache
 */

/**
 * 画像キャッシュアクション
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSImageCacheAction extends BSRecordAction {
	private $file;

	private function getFile () {
		if (!$this->file) {
			$this->file = $this->getRecord()->getImageFile($this->request['size']);
		}
		return $this->file;
	}

	public function execute () {
		if ($pixel = $this->request['pixel']) {
			$image = BSImageCacheHandler::getInstance()->setThumbnail(
				$this->getRecord(),
				$this->request['size'],
				$pixel,
				$this->getFile()->getEngine()
			);
			$modified = BSDate::getNow();
		} else {
			$image = $this->getFile()->getEngine();
			$modified = $this->getFile()->getUpdateDate();
		}
		$this->request->setAttribute('renderer', $image);
		$this->request->setAttribute('modified', $modified);
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}

	public function validate () {
		return ($this->getFile() != null);
	}
}

/* vim:set tabstop=4: */
