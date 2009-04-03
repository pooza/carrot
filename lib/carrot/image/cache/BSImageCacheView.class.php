<?php
/**
 * @package org.carrot-framework
 * @subpackage image.cache
 */

/**
 * 画像キャッシュビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
abstract class BSImageCacheView extends BSView {
	public function execute () {
		if ($modified = $this->request->getAttribute('modified')) {
			$this->setHeader('Last-Modified', $modified->format('D, d M Y H:i:s T', BSDate::GMT));

			$expire = BSDate::getNow()->setAttribute('hour', '+1');
			$this->setHeader('Expires', $expire->format('D, d M Y H:i:s T', BSDate::GMT));

			$this->setHeader('Cache-Control', 'private');
			$this->setHeader('Pragma', 'private');
		}
	}
}

/* vim:set tabstop=4: */
