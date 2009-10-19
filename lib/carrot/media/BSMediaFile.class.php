<?php
/**
 * @package org.carrot-framework
 * @subpackage media
 */

/**
 * メディアファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMediaFile extends BSFile implements ArrayAccess {
	protected $attributes;

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
			$this->analize();
		}
		return $this->attributes;
	}

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 * @abstract
	 */
	abstract protected function analize ();

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->getAttributes()->getParameter('type');
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getImageElement (BSParameterHolder $params) {
		$style = $this->getPixelSizeCSSSelector($params);
		$root = new BSXMLElement('div');
		if (!BSString::isBlank($params['style_class'])) {
			$root->setAttribute('class', $params['style_class']);
		}
		if ($params['mode'] == 'noscript') {
			$root->setAttribute('style', $style->getContents());
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
	 * スタイル属性を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSCSSSelector スタイル属性
	 */
	protected function getPixelSizeCSSSelector (BSParameterHolder $params) {
		$style = new BSCSSSelector;
		if ($params['width']) {
			$style['width'] = $params['width'] . 'px';
		} else {
			$style['width'] = $this['width'] . 'px';
		}
		if ($params['height']) {
			$style['height'] = $params['height'] . 'px';
		} else {
			$style['height'] = $this['height_full'] . 'px';
		}
		return $style;
	}

	/**
	 * メディアURLを返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSURL メディアURL
	 */
	protected function getMediaURL (BSParameterHolder $params) {
		$url = BSURL::getInstance();
		$url['path'] = $params['href_prefix'] . $this->getName() . $params['href_suffix'];
		if (BSUser::getInstance()->isAdministrator()) {
			$url->setParameter('at', BSNumeric::getRandom());
		}
		return $url;
	}

	/**
	 * div要素のIDを返す
	 *
	 * @access protected
	 * @return string div要素のID
	 */
	protected function getContainerID () {
		return get_class($this) . $this->getID();
	}

	/**
	 * script要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 * @abstract
	 */
	abstract protected function getScriptElement (BSParameterHolder $params);

	/**
	 * object要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 * @abstract
	 */
	abstract protected function getObjectElement (BSParameterHolder $params);

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return $this->isReadable() && $this->getAttributes()->count();
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return ' 正しいメディアファイルではありません。';
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
		throw new BSFlashException($this . 'の属性を設定できません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSFlashException($this . 'の属性を削除できません。');
	}
}

/* vim:set tabstop=4: */