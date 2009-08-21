<?php
/**
 * @package org.carrot-framework
 * @subpackage flash
 */

/**
 * Flashムービーファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFlashFile extends BSFile implements ArrayAccess {
	private $attributes;

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return BSArray 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
			$info = getimagesize($this->getPath());
			if (!$info || ($info['mime'] != $this->getType())) {
				throw new BSFlashException('%sはFlashムービーではありません。', $this);
			}
			$this->attributes['path'] = $this->getPath();
			$this->attributes['width'] = $info[0];
			$this->attributes['height'] = $info[1];
			$this->attributes['pixel_size'] = $this['width'] . '×' . $this['height'];
		}
		return $this->attributes;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('.swf');
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSArray $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getImageElement (BSArray $params) {
		foreach (array('href_prefix', 'player_ver', 'installer_path', 'loader_path') as $key) {
			if (BSString::isBlank($params[$key])) {
				$params[$key] = BSController::getInstance()->getConstant('flash_' . $key);
			}
		}

		$root = new BSXMLElement('div');
		$root->setAttribute('class', $params['style_class']);
		if ($params['mode'] == 'noscript') {
			$root->addElement($this->getObjectElement($params));
		} else {
			if (BSString::isBlank($params['container_id'])) {
				$params['container_id'] = $this->getContainerID();
				$container = $root->createElement('div');
				$container->setAttribute('id', $params['container_id']);
			}
			$root->addElement($this->getScriptElement($params));
		}
		return $root;
	}

	/**
	 * div要素のIDを返す
	 *
	 * @access private
	 * @return string div要素のID
	 */
	private function getContainerID () {
		return 'swf_' . $this->getID();
	}

	/**
	 * script要素を返す
	 *
	 * @access private
	 * @param BSArray $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	private function getScriptElement (BSArray $params) {
		$element = BSJavaScriptUtility::getScriptElement();
		$body = new BSStringFormat('swfobject.embedSWF(%s,%s,%d,%d,%s,%s,%s,%s);');
		$body[] = BSJavaScriptUtility::quote(
			$params['href_prefix'] . $this->getName() . $params['href_suffix']
		);
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
	 * @access private
	 * @param BSArray $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	private function getObjectElement (BSArray $params) {
		$element = new BSXMLElement('object');
		$element->setAttribute('width', $this['width']);
		$element->setAttribute('height', $this['height']);
		$element->setAttribute('type', $this->getType());
		$param = $element->createElement('param');
		$param->setAttribute('name', 'movie');
		$param->setAttribute(
			'value',
			$params['href_prefix'] . $this->getName() . $params['href_suffix']
		);
		$param = $element->createElement('param');
		$param->setAttribute('name', 'wmode');
		$param->setAttribute('value', 'transparent');
		return $element;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return $this->isReadable() && $this->getAttributes();
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return 'Flashムービーではありません。';
	}

	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->getAttributes()->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSFlashException('%sの属性を設定できません。', $this);
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSFlashException('%sの属性を削除できません。', $this);
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
