<?php
/**
 * @package org.carrot-framework
 * @subpackage image
 */

/**
 * 座標
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCoordinate {
	private $image;
	private $x;
	private $y;

	/**
	 * @access public
	 * @param BSImage $image 画像レンダラー
	 * @param integer $x X座標
	 * @param integer $y Y座標
	 */
	public function __construct (BSImage $image, $x, $y) {
		if (($x < 0) || ($image->getWidth() - 1 < $x)) {
			throw new BSImageException('X座標[%d]は領域外です。', $x);
		} else if (($y < 0) || ($image->getHeight() - 1 < $y)) {
			throw new BSImageException('Y座標[%d]は領域外です。', $y);
		}

		$this->image = $image;
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * X座標を返す
	 *
	 * @access public
	 * @return integer X座標
	 */
	public function getX () {
		return $this->x;
	}

	/**
	 * Y座標を返す
	 *
	 * @access public
	 * @return integer Y座標
	 */
	public function getY () {
		return $this->y;
	}

	/**
	 * 画像レンダラーを返す
	 *
	 * @access public
	 * @return BSImage 画像レンダラー
	 */
	public function getImage () {
		return $this->image;
	}
}

/* vim:set tabstop=4 ai: */
?>