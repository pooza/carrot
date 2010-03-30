<?php
/**
 * @package org.carrot-framework
 * @subpackage media.flash
 */

/**
 * Flashムービーファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFlashFile extends BSMediaFile {

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analyze () {
		$info = getimagesize($this->getPath());
		if (!$info || ($info['mime'] != BSMIMEType::getType('swf'))) {
			throw new BSMediaException($this . 'はFlashムービーではありません。');
		}
		$this->attributes['path'] = $this->getPath();
		$this->attributes['type'] = $info['mime'];
		$this->attributes['width'] = $info[0];
		$this->attributes['height'] = $info[1];
		$this->attributes['height_full'] = $info[1];
		$this->attributes['pixel_size'] = $this['width'] . '×' . $this['height'];
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getElement (BSParameterHolder $params) {
		if ($params['flash_light']) {
			return $this->getFlashLightElement($params);
		}

		$constants = BSConstantHandler::getInstance();
		foreach (array('player_ver', 'installer_href') as $key) {
			if (BSString::isBlank($params[$key])) {
				$params[$key] = $constants['flash_' . $key];
			}
		}
		return parent::getElement($params);
	}

	/**
	 * FlashLight表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getFlashLightElement (BSParameterHolder $params) {
		$div = new BSDivisionElement;
		$object = $div->addElement(new BSFlashLightObjectElement);
		$object->setURL($this->getMediaURL($params));
		$object->setAttribute('id', 'swf' . $this->getID());
		$anchor = $div->addElement(new BSDivisionElement);
		$anchor = $anchor->addElement(new BSAnchorElement);
		$anchor->setAttribute('iswf', 'swf' . $this->getID());
		$anchor->setURL($this->getMediaURL($params));

		if (BSString::isBlank($params['label'])) {
			$params['label'] = 'FlashLight表示';
		}
		$anchor->setBody($params['label']);

		return $div;
	}

	/**
	 * script要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	protected function getScriptElement (BSParameterHolder $params) {
		$element = new BSScriptElement;
		$body = new BSStringFormat('swfobject.embedSWF(%s,%s,%d,%d,%s,%s,%s,%s);');
		$body[] = BSJavaScriptUtility::quote($this->getMediaURL($params)->getContents());
		$body[] = BSJavaScriptUtility::quote($params['container_id']);
		$body[] = $this['width'];
		$body[] = $this['height'];
		$body[] = BSJavaScriptUtility::quote($params['player_ver']);
		$body[] = BSJavaScriptUtility::quote($params['installer_path']);
		$body[] = BSJavaScriptUtility::quote(null);
		$body[] = BSJavaScriptUtility::quote(array('wmode' => 'transparent'));
		$element->setBody($body->getContents());
		return $element;
	}

	/**
	 * object要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	protected function getObjectElement (BSParameterHolder $params) {
		$element = new BSFlashObjectElement;
		$element->setURL($this->getMediaURL($params));
		return $element;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!parent::validate()) {
			return false;
		}
		return ($this->analyzeType() == BSMIMEType::getType('swf'));
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Flashムービーファイル "%s"', $this->getShortPath());
	}

	/**
	 * 探す
	 *
	 * @access public
	 * @param mixed $file パラメータ配列、BSFile、ファイルパス文字列
	 * @param string $class クラス名
	 * @return BSFile ファイル
	 * @static
	 */
	static public function search ($file, $class = 'BSFlashFile') {
		return parent::search($file, $class);
	}
}

/* vim:set tabstop=4: */
