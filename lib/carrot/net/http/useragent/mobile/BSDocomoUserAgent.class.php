<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * Docomoユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDocomoUserAgent extends BSMobileUserAgent {
	const DEFAULT_NAME = 'DoCoMo/2.0';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		if (BSString::isBlank($name)) {
			$name = self::DEFAULT_NAME;
		}
		parent::__construct($name);
		$this['is_foma'] = $this->isFOMA();
		$this->supports['cookie'] = $this->isFOMA() && !BSString::isContain('c100;', $name);
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
		return !$this->isFOMA() && !BSString::isContain('bot', $this->getName());
	}

	/**
	 * クエリーパラメータを返す
	 *
	 * @access public
	 * @return BSWWWFormRenderer
	 */
	public function getQuery () {
		$query = parent::getQuery();
		$query['guid'] = 'ON';
		return $query;
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$carrier = $this->getCarrier();
		foreach ($carrier['display_infos'] as $pattern => $values) {
			if (BSString::isContain($pattern, $this->getName(), true)) {
				return new BSArray($values);
			}
		}
		return parent::getDisplayInfo();
	}
}

/* vim:set tabstop=4: */
