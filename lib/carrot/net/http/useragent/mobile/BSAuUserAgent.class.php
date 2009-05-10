<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * Auユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAuUserAgent extends BSMobileUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_wap2'] = $this->isWAP2();
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if ($id = BSController::getInstance()->getEnvironment('X-UP-SUBNO')) {
			return $id;
		}
		return parent::getID();
	}

	/**
	 * WAP2.0端末か？
	 *
	 * @access public
	 * @return boolean WAP2.0端末ならばTrue
	 */
	public function isWAP2 () {
		return preg_match('/^KDDI/', $this->getName());
	}

	/**
	 * 旧機種か？
	 *
	 * @access public
	 * @return boolean 旧機種ならばTrue
	 */
	public function isLegacy () {
		return !$this->isWAP2();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$controller = BSController::getInstance();
		if (BSString::isBlank($info = $controller->getEnvironment('X-UP-DEVCAP-SCREENPIXELS'))) {
			return new BSArray(array(
				'width' => self::DEFAULT_DISPLAY_WIDTH,
				'height' => self::DEFAULT_DISPLAY_HEIGHT,
			));
		}
		$info = BSString::explode(',', $info);

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
		return '/^(UP\.Browser|KDDI)/';
	}
}

/* vim:set tabstop=4: */
