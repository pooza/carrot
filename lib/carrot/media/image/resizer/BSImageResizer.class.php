<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.resizer
 */

/**
 * 画像リサイズ機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
abstract class BSImageResizer {
	protected $source;

	/**
	 * @access public
	 * @param BSImage $image GD画像レンダラー
	 */
	public function __construct (BSImage $image) {
		$this->source = $image;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 * @return BSImage リサイズ後のレンダラー
	 */
	abstract public function execute ($width, $height);
}

/* vim:set tabstop=4: */
