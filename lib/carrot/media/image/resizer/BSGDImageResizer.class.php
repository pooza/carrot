<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.resizer
 */

/**
 * GD画像リサイズ機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSGDImageResizer extends BSImageResizer {

	/**
	 * 実行
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 * @return BSImage リサイズ後のレンダラー
	 */
	public function execute ($width, $height) {
		$dest = new BSImage($width, $height);
		if ($this->source->getAspect() < $dest->getAspect()) {
			$width = $dest->getHeight() * $this->source->getAspect();
			$x = BSNumeric::round(($dest->getWidth() - $width) / 2);
			$coord = $dest->getCoordinate($x, 0);
		} else {
			$height = $dest->getWidth() / $this->source->getAspect();
			$y = BSNumeric::round(($dest->getHeight() - $height) / 2);
			$coord = $dest->getCoordinate(0, $y);
		}

		imagecopyresampled(
			$dest->getImage(), //コピー先
			$this->source->getImage(), //コピー元
			$coord->getX(), $coord->getY(),
			$this->source->getOrigin()->getX(), $this->source->getOrigin()->getY(),
			BSNumeric::round($width), BSNumeric::round($height), //コピー先サイズ
			$this->source->getWidth(), $this->source->getHeight() //コピー元サイズ
		);
		return $dest;
	}
}

/* vim:set tabstop=4: */
