<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * 基底URL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSURL implements ArrayAccess, BSAssignable {
	protected $attributes;
	protected $contents;
	const PATTERN = '/^[a-z]+:(\/\/)?[-_.!~*()a-z0-9;\/?:@&=+$,%#]+$/i';

	/**
	 * @access public
	 * @param string $url URL
	 */
	public function __construct ($url = null) {
		$this->attributes = new BSArray;
		$this->setContents($url);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string URL
	 */
	public function getContents () {
		if (!$this->contents) {
			if (BSString::isBlank($this->contents = $this->getHeadString())) {
				return null;
			}
			$this->contents .= $this->getFullPath();
		}
		return $this->contents;
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param string $url URL
	 */
	public function setContents ($url) {
		$this->attributes->clear();
		if (!preg_match(self::PATTERN, $url)) {
			return false;
		}
		foreach (parse_url($url) as $name => $value) {
			$this[$name] = $value;
		}
	}

	/**
	 * フルパスを除いた前半を返す
	 *
	 * @access protected
	 * @return string 前半
	 */
	protected function getHeadString () {
		if (BSString::isBlank($this['scheme']) || BSString::isBlank($this['host'])) {
			return null;
		}

		$head = $this['scheme'] . '://';

		if (!BSString::isBlank($this['user'])) {
			$head .= $this['user'];
			if (!BSString::isBlank($this['pass'])) {
				$head .= ':' . $this['pass'];
			}
			$head .= '@';
		}

		$head .= $this['host']->getName();

		if ($this['port'] != BSNetworkService::getPort($this['scheme'])) {
			$head .= ':' . $this['port'];
		}

		return $head;
	}

	/**
	 * path以降を返す
	 *
	 * @access public
	 * @return string URLのpath以降
	 */
	public function getFullPath () {
		return $this['path'];
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return string 属性
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return BSURL 自分自身
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'scheme':
				$this->attributes['scheme'] = $value;
				if (BSString::isBlank($this['port'])) {
					$this['port'] = BSNetworkService::getPort($value);
				}
				break;
			case 'host':
				if (($value instanceof BSHost) == false) {
					$value = new BSHost($value);
				}
				$this->attributes['host'] = $value;
				break;
			case 'path':
			case 'port':
			case 'user':
			case 'pass':
				$this->attributes[$name] = $value;
				break;
			default:
				throw new BSNetException('"%s"は正しくない属性名です。', $name);
		}
		$this->contents = null;
		return $this;
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return string[] 属性
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 妥当なURLか？
	 *
	 * @access public
	 * @return boolean 妥当ならtrue
	 */
	public function validate () {
		return !BSString::isBlank($this->getContents());
	}

	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attribute->hasParameter($key);
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
		$this->setAttribute($key, $value);
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->setAttribute($key, null);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('URL "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
