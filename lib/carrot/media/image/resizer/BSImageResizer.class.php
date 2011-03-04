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
	protected $backgroundColor;

	/**
	 * @access public
	 * @param BSImage $image GD画像レンダラー
	 */
	public function __construct (BSImage $image) {
		$this->source = $image;
	}

	/**
	 * 背景色を返す
	 *
	 * @access public
	 * @return BSColor 背景色
	 */
	public function getBackgroundColor () {
		if (!$this->backgroundColor) {
			$this->backgroundColor = new BSColor(BS_IMAGE_THUMBNAIL_BGCOLOR);
		}
		return $this->backgroundColor;
	}

	/**
	 * 背景色を設定
	 *
	 * @access public
	 * @param BSColor $color 背景色
	 */
	public function setBackgroundColor (BSColor $color) {
		$this->backgroundColor = $color;
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
