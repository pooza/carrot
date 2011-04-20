<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.smartphone
 */

/**
 * iOSユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSIOSUserAgent extends BSWebKitUserAgent {

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this['is_web_kit'] = true;
	}

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 */
	public function isSmartPhone () {
		return !$this->isTablet();
	}

	/**
	 * タブレット型か？
	 *
	 * @access public
	 * @return boolean タブレット型ならTrue
	 */
	public function isTablet () {
		return BSString::isContain('iPad', $this->getName());
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$info = new BSArray;
		if ($this->isSmartPhone()) {
			$info['width'] = 480;
		}
		return $info;
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
				$this->isTablet(),
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
		return 'i(Phone|Pod|Pad);';
	}
}

/* vim:set tabstop=4: */
