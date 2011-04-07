<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * SoftBankユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSoftBankUserAgent extends BSMobileUserAgent {
	const DEFAULT_NAME = 'SoftBank';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		if (BSString::isBlank($name)) {
			$name = self::DEFAULT_NAME;
		}
		parent::__construct($name);
		$this['is_3gc'] = $this->is3GC();
	}

	/**
	 * ビューを初期化
	 *
	 * @access public
	 * @param BSSmartyView 対象ビュー
	 * @return boolean 成功時にTrue
	 */
	public function initializeView (BSSmartyView $view) {
		parent::initializeView($view);
		if (BS_STRING_MOBILE_SOFTBANK_RAW_OUTPUT) {
			$view->getRenderer()->setEncoding('utf8');
		}
		return true;
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if ($id = BSController::getInstance()->getAttribute('X-JPHONE-UID')) {
			return $id;
		}
		return parent::getID();
	}

	/**
	 * 3GC端末か？
	 *
	 * @access public
	 * @return boolean 3GC端末ならばTrue
	 */
	public function is3GC () {
		return !mb_ereg('^J-PHONE', $this->getName());
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
		if (BSString::isBlank($info = $controller->getAttribute('X-JPHONE-DISPLAY'))) {
			return parent::getDisplayInfo();
		}
		$info = BSString::explode('*', $info);

		return new BSArray(array(
			'width' => (int)$info[0],
			'height' => (int)$info[1],
		));
	}

	/**
	 * 添付可能か？
	 *
	 * @access public
	 * @return boolean 添付可能ならTrue
	 */
	public function isAttachable () {
		return true;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '^(J-PHONE|MOT|Vodafone|SoftBank)';
	}
}

/* vim:set tabstop=4: */
