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
		return BSController::getInstance()->getEnvironment('X-UP-SUBNO');
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
			return new BSArray;
		}
		$info = BSString::explode(',', $info);

		return new BSArray(array(
			'width' => (int)$info[0],
			'height' => (int)$info[1],
		));
	}

	/**
	 * 画像を変換
	 *
	 * @access public
	 * @param BSImage $image 対象画像
	 * @param integer $flags フラグ
	 * @return BSImage 変換後の画像
	 */
	public function convertImage (BSImage $image, $flags = self::IMAGE_FULL_SCREEN) {
		$dest = clone $image;
		if (!$this->isWAP2()) {
			if ($image->getType() == 'image/jpeg') {
				$dest->setType('image/png');
			}
		}
		if ($flags & self::IMAGE_FULL_SCREEN) {
			$dest->resize(
				$this->attributes['display']['width'],
				$this->attributes['display']['height']
			);
		}
		return $dest;
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
