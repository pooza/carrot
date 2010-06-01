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
		foreach ($this->getCarrier()->getAttribute('display_infos') as $pattern => $values) {
			if (BSString::isContain($pattern, $this->getName(), true)) {
				return new BSArray($values);
			}
		}
		return parent::getDisplayInfo();
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function getMovieElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$object = $container->addElement(new BSObjectElement);
		$object->setAttribute('declare', 'declare');
		$object->setAttribute('type', $params['type']);
		$object->setAttribute('data', $params['url']);
		$object->setID('3gp_' . BSCrypt::getDigest($params['url']));
		$anchor = $container->addElement(new BSDivisionElement);
		$anchor = $anchor->addElement(new BSAnchorElement);
		$anchor->setAttribute('href', '#' . $object->getID());
		if (BSString::isBlank($label = $params['label'])) {
			$label = '3GP表示';
		}
		$anchor->setBody($label);
		return $container;
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
