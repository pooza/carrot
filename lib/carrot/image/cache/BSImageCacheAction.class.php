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
	public function execute () {
		$url = BSImageCacheHandler::getInstance()->getURL(
			$this->getRecord(),
			$this->request['size'],
			$this->request['pixel']
		);
		return $url->redirect();
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}

	public function validate () {
		return ($this->getRecord()->getImageFile($this->request['size']) != null);
	}
}

/* vim:set tabstop=4: */
