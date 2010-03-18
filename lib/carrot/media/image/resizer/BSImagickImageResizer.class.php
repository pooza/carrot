<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.resizer
 */

/**
 * ImageMagick画像リサイズ機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImagickImageResizer extends BSImageResizer {
	private $file;

	/**
	 * @access public
	 */
	public function __destruct () {
		if ($file = $this->file) {
			$file->delete();
		}
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 * @return BSImage リサイズ後のレンダラー
	 */
	public function execute ($width, $height) {
		$dest = new BSImagickImage($width, $height);
		if ($this->source->getAspect() < $dest->getAspect()) {
			$width = $dest->getHeight() * $this->source->getAspect();
			$x = BSNumeric::round(($dest->getWidth() - $width) / 2);
			$coord = $dest->getCoordinate($x, 0);
		} else {
			$height = $dest->getWidth() / $this->source->getAspect();
			$y = BSNumeric::round(($dest->getHeight() - $height) / 2);
			$coord = $dest->getCoordinate(0, $y);
		}

		if ($this->source instanceof BSImagickImage) {
			$resized = clone $this->source->getImagick();
		} else {
			$resized = new Imagick($this->getFile()->getPath());
		}
		$resized->thumbnailImage($width, $height, true);
		$dest->getImagick()->compositeImage(
			$resized,
			Imagick::COMPOSITE_DEFAULT,
			$coord->getX(), $coord->getY()
		);
		return $dest;
	}

	private function getFile () {
		if (!$this->file) {
			$this->file = BSFileUtility::getTemporaryFile();
			$this->file->setContents($this->source->getContents());
		}
		return $this->file;
	}
}

/* vim:set tabstop=4: */
