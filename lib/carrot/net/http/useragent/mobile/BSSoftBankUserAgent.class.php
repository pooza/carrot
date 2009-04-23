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
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		return BSController::getInstance()->getEnvironment('HTTP_X_JPHONE_UID');
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$controller = BSController::getInstance();
		if (BSString::isBlank($info = $controller->getEnvironment('HTTP_X_JPHONE_DISPLAY'))) {
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
