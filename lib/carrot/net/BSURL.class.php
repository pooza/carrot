<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

/**
 * 基底URL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSURL implements ArrayAccess, BSAssignable {
	use BSBasicObject;
	protected $attributes;
	protected $contents;
	const PATTERN = '^[[:alnum:]]+:(//)?[[:graph:]]+$';

	/**
	 * @access protected
	 * @param mixed $contents URL
	 */
	protected function __construct ($contents) {
		$this->attributes = new BSArray;
		$this->setContents($contents);
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $contents URL文字列、又はパラメータ配列
	 * @param string $class 生成クラス名
	 * @return BSURL
	 * @static
	 */
	static public function create ($contents = null, $class = 'http') {
		if (!$class = BSLoader::getInstance()->getClass($class, 'URL')) {
			throw new BSNetException('URLクラスが見つかりません。');
		}

		if (BSString::isBlank($contents)) {
			return new $class;
		} else if (is_string($contents)) {
			$params = new BSArray(parse_url($contents));
		} else if (is_array($contents)) {
			$params = new BSArray($contents);
		} else if ($contents instanceof BSParameterHolder) {
			$params = new BSArray($contents->getParameters());
		} else {
			return null;
		}

		switch ($params['scheme']) {
			case 'mailto':
			case 'tel':
			case 'skype':
				return new BSContactURL($params);
			case 'javascript':
				return new BSJavaScriptURL($params);
			default:
				return new $class($params);
		}
	}

	/**
	 * @access public
	 */
	public function __clone () {
		$this->attributes = clone $this->attributes;
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
	 * @param mixed $contents URL
	 */
	public function setContents ($contents) {
		$this->attributes->clear();
		if (!is_array($contents) && !($contents instanceof BSParameterHolder)) {
			if (!mb_ereg(self::PATTERN, $contents)) {
				return false;
			}
			$contents = parse_url($contents);
		}

		foreach ($contents as $key => $value) {
			$this[$key] = $value;
		}
	}

	/**
	 * フルパスを除いた前半を返す
	 *
	 * @access protected
	 * @return string 前半
	 */
	protected function getHeadString () {
		if (BSString::isBlank($this['scheme']) || !$this['host']) {
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
		$this->contents = null;
		switch ($name) {
			case 'scheme':
				$this->attributes['scheme'] = $value;
				$this->attributes['port'] = BSNetworkService::getPort($value);
				break;
			case 'host':
				if (!($value instanceof BSHost)) {
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
		}
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
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->setAttribute($key, $value);
	}

	/**
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
	public function assign () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('URL "%s"', $this->getContents());
	}

	/**
	 * 文字列をURLエンコード
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string URLエンコードされた文字列
	 * @static
	 */
	static public function encode ($value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::encode($item);
			}
		} else {
			$value = urlencode($value);
		}
		return $value;
	}

	/**
	 * 文字列をURLデコード
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string URLデコードされた文字列
	 * @static
	 */
	static public function decode ($value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::decode($item);
			}
		} else {
			$value = urldecode($value);
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
