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
	const DEFAULT_DISPLAY_WIDTH = 320;
	const DEFAULT_DISPLAY_HEIGHT = 240;
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
		return BSController::getInstance()->getEnvironment('X-DCMGUID');
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
	 * 画像を変換
	 *
	 * @access public
	 * @param BSImage $image 対象画像
	 * @param integer $flags フラグ
	 * @return BSImage 変換後の画像
	 */
	public function convertImage (BSImage $image, $flags = self::IMAGE_FULL_SCREEN) {
		$dest = clone $image;
		if ($this->isFOMA()) {
			if ($image->getType() == 'image/png') {
				$dest->setType('image/jpeg');
			}
		} else {
			if ($image->getType() != 'image/gif') {
				$dest->setType('image/gif');
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
