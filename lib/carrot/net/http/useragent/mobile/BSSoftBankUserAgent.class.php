<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * SoftBankユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSoftBankUserAgent extends BSMobileUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['query']['guid'] = 'ON';
		$this->attributes['is_3gc'] = $this->is3GC();
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		return BSController::getInstance()->getEnvironment('X-JPHONE-UID');
	}

	/**
	 * 3GC端末か？
	 *
	 * @access public
	 * @return boolean 3GC端末ならばTrue
	 */
	public function is3GC () {
		return !preg_match('/^J-PHONE/', $this->getName());
	}

	/**
	 * 旧機種か？
	 *
	 * @access public
	 * @return boolean 旧機種ならばTrue
	 */
	public function isLegacy () {
		return !$this->is3GC();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$controller = BSController::getInstance();
		if (BSString::isBlank($info = $controller->getEnvironment('X-JPHONE-DISPLAY'))) {
			return new BSArray;
		}
		$info = BSString::explode('*', $info);

		return new BSArray(array(
			'width' => (int)$info[0],
			'height' => (int)$info[1],
		));
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/^(J-PHONE|MOT|Vodafone|SoftBank)/';
	}
}

/* vim:set tabstop=4: */
