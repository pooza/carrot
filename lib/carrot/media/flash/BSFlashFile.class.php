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
		$this->attributes['width'] = $info[0];
		$this->attributes['type'] = $info['mime'];
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
	public function getImageElement (BSParameterHolder $params) {
		$constants = BSConstantHandler::getInstance();
		foreach (array('href_prefix', 'player_ver', 'installer_href') as $key) {
			if (BSString::isBlank($params[$key])) {
				$params[$key] = $constants['flash_' . $key];
			}
		}
		return parent::getImageElement($params);
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
		return BSFlashUtility::getObjectElement($this->getMediaURL($params));
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Flashムービーファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
