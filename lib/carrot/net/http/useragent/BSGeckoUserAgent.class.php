<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * Geckoユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSGeckoUserAgent extends BSUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'FireFox';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/Gecko\/[0-9]+/';
	}

	/**
	 * アップロードボタンのラベルを返す
	 *
	 * @access public
	 * @return string アップロードボタンのラベル
	 */
	public function getUploadButtonLabel () {
		if ($this->getAttribute('Platform') == 'MacOSX') {
			return '選択...';
		}
		return parent::getUploadButtonLabel();
	}
}

/* vim:set tabstop=4 ai: */
?>