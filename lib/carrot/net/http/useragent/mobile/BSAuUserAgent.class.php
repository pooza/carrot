<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * Auユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAuUserAgent extends BSMobileUserAgent {
	const DEFAULT_NAME = 'KDDI';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		if (BSString::isBlank($name)) {
			$name = self::DEFAULT_NAME;
		}
		parent::__construct($name);
		$this->bugs['multipart_form'] = true;
		$this->supports['image_copyright'] = true;
		$this->supports['cookie'] = true;
		$this['is_wap2'] = $this->isWAP2();
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if ($id = BSController::getInstance()->getAttribute('X-UP-SUBNO')) {
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
		return mb_ereg('^KDDI', $this->getName());
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
		if (BSString::isBlank($info = $controller->getAttribute('X-UP-DEVCAP-SCREENPIXELS'))) {
			return parent::getDisplayInfo();
		}
		$info = BSString::explode(',', $info);

		return new BSArray([
			'width' => (int)$info[0],
			'height' => (int)$info[1],
		]);
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function createMovieElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$object = $container->addElement(new BSObjectElement);
		$object->setAttribute('type', $params['type']);
		$object->setAttribute('standby', $params['label']);
		$object->setAttribute('data', $params['url']);
		$object->setParameter('disposition', 'devmpzz');
		$object->setParameter('size', $params['size']);
		$object->setParameter('title', $params['title']);
		if (!BS_IMAGE_STORABLE && $this->hasSupport('image_copyright')) {
			$object->setAttribute('copyright', 'yes');
		}
		return $container;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '^(UP\\.Browser|KDDI)';
	}
}

/* vim:set tabstop=4: */
