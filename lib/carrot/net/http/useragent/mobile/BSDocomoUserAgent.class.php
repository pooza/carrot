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
	const LIST_FILE_NAME = 'docomo_agents.xml';
	static private $displayInfo;

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
		if ($id = BSController::getInstance()->getEnvironment('X-DCMGUID')) {
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
		return '/DoCoMo/';
	}

	/**
	 * FOMA端末か？
	 *
	 * @access public
	 * @return boolean FOMA端末ならばTrue
	 */
	public function isFOMA () {
		return !preg_match('/DoCoMo\/1\.0/', $this->getName());
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
		foreach (self::getDisplayInfos() as $pattern => $values) {
			$position = stripos($this->getName(), $pattern);
			if ($position !== false) {
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
		return BSMIMEType::getType('jpg');
	}

	static private function getDisplayInfos () {
		if (!self::$displayInfo) {
			$dir = BSController::getInstance()->getDirectory('config');
			$file = $dir->getEntry(self::LIST_FILE_NAME);

			$controller = BSController::getInstance();
			if (!$agents = $controller->getAttribute($file, $file->getUpdateDate())) {
				$agents = new BSArray;
				$contents = $file->getContents();

				//libxml2がパースエラーを起こす
				$contents = preg_replace('/[+&]/', '', $contents);

				$xml = new BSXMLDocument;
				$xml->setContents($contents);
				foreach ($xml->getElements() as $element) {
					$agents[$element->getName()] = $element->getAttributes();
				}
				$agents->sort(BSArray::SORT_KEY_DESC);
				$controller->setAttribute($file, $agents->getParameters());
			}
			self::$displayInfo = new BSArray($agents);
		}
		return self::$displayInfo;
	}
}

/* vim:set tabstop=4: */
