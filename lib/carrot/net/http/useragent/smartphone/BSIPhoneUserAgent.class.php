<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.smartphone
 */

/**
 * iPhoneユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSIPhoneUserAgent extends BSWebKitUserAgent {

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 */
	public function isSmartPhone () {
		return true;
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		return new BSArray(array(
			'width' => 480,
		));
	}

	/**
	 * レンダーダイジェストを返す
	 *
	 * @access public
	 * @return string レンダーダイジェスト
	 */
	public function getRenderDigest () {
		if (!$this->renderDigest) {
			$this->renderDigest = BSCrypt::getDigest(new BSArray(array(
				__CLASS__,
			)));
		}
		return $this->renderDigest;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'i(Phone|Pod);';
	}
}

/* vim:set tabstop=4: */
