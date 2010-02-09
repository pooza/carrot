<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * Docomoユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDocomoUserAgent extends BSMobileUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['query']['guid'] = 'ON';
		$this->attributes['is_foma'] = $this->isFOMA();
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if ($id = BSController::getInstance()->getAttribute('X-DCMGUID')) {
			return $id;
		}
		return parent::getID();
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'DoCoMo';
	}

	/**
	 * FOMA端末か？
	 *
	 * @access public
	 * @return boolean FOMA端末ならばTrue
	 */
	public function isFOMA () {
		return !mb_ereg('DoCoMo/1\\.0', $this->getName());
	}

	/**
	 * 旧機種か？
	 *
	 * @access public
	 * @return boolean 旧機種ならばTrue
	 */
	public function isLegacy () {
		return !$this->isFOMA();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		foreach ($this->getCarrier()->getAttribute('display_infos') as $pattern => $values) {
			if (BSString::isContain($pattern, $this->getName(), true)) {
				return new BSArray($values);
			}
		}
		return new BSArray(array(
			'width' => self::DEFAULT_DISPLAY_WIDTH,
			'height' => self::DEFAULT_DISPLAY_HEIGHT,
		));
	}

	/**
	 * 規定の画像形式を返す
	 *
	 * @access public
	 * @return string 規定の画像形式
	 */
	public function getDefaultImageType () {
		if ($this->isLegacy()) {
			return BSMIMEType::getType('gif');
		}
		return parent::getDefaultImageType();
	}
}

/* vim:set tabstop=4: */
