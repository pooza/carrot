<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 配列
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSArray.class.php 189 2008-04-12 10:25:31Z pooza $
 */
class BSArray extends ParameterHolder implements IteratorAggregate, ArrayAccess, Countable {
	private $keys;
	const POSITION_TOP = true;
	const POSITION_BOTTOM = false;
	const SORT_KEY_ASC = 'KEY_ASC';
	const SORT_KEY_DESC = 'KEY_DESC';
	const SORT_VALUE_ASC = 'VALUE_ASC';
	const SORT_VALUE_DESC = 'VALUE_DESC';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($params = array()) {
		if ($params instanceof BSArray) {
			$this->setParameters($params->getParameters());
		} else if (is_array($params)) {
			$this->setParameters($params);
		} else {
			$this->addParameter($params);
		}
	}

	/**
	 * 要素をまとめて設定する
	 *
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function setParameters ($params) {
		$this->parameters += $params;
	}

	/**
	 * 要素をまとめて設定する
	 *
	 * setParametersのエイリアス
	 *
	 * @access public
	 * @param mixed[] $attributes 要素の配列
	 * @final
	 */
	public final function setAttributes ($attributes) {
		$this->setParameters($attributes);
	}

	/**
	 * 要素を設定する
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $value, $position = self::POSITION_BOTTOM) {
		if ($name === null) {
			if ($position == self::POSITION_TOP) {
				array_unshift($this->parameters, $value);
			} else {
				$this->parameters[] = $value;
			}
		} else {
			if ($position == self::POSITION_TOP) {
				$this->parameters = array($name => null) + $this->parameters;
			}
			$this->parameters[$name] = $value;
		}
	}

	/**
	 * 要素を設定する
	 *
	 * setParameterのエイリアス
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 * @final
	 */
	public final function setAttribute ($name, $value, $position = self::POSITION_BOTTOM) {
		$this->setParameter($name, $value, $position);
	}

	/**
	 * 要素を削除する
	 *
	 * removeParameterのエイリアス
	 *
	 * @access public
	 * @param string $name 名前
	 * @final
	 */
	public final function removeAttribute ($name) {
		$this->removeParameter($name);
	}

	/**
	 * 要素を含むか？
	 *
	 * hasParameterのエイリアス
	 *
	 * @access public
	 * @param string $name 名前
	 * @final
	 */
	public final function hasAttribute ($name) {
		return $this->hasParameter($name);
	}

	/**
	 * ソート
	 *
	 * @access public
	 * @param string $order ソート順
	 */
	public function sort ($order = self::SORT_KEY_ASC) {
		$funcs = new BSArray();
		$funcs[self::SORT_KEY_ASC] = 'ksort';
		$funcs[self::SORT_KEY_DESC] = 'krsort';
		$funcs[self::SORT_VALUE_ASC] = 'asort';
		$funcs[self::SORT_VALUE_DESC] = 'arsort';

		if ($func = $funcs[$order]) {
			$func($this->parameters);
		}
	}

	/**
	 * 値が含まれているか？
	 *
	 * @access public
	 * @param mixed $value 値
	 * @return boolean 値が含まれていればTrue
	 */
	public function isIncluded ($value) {
		return in_array($value, $this->getParameters());
	}

	/**
	 * セパレータで結合した文字列を返す
	 *
	 * @access public
	 * @param string $separator セパレータ
	 * @return string 結果文字列
	 */
	public function implode ($separator = null) {
		return implode($separator, $this->getParameters());
	}

	/**
	 * セパレータで結合した文字列を返す
	 *
	 * implodeのエイリアス
	 *
	 * @access public
	 * @param string $separator セパレータ
	 * @return string 結果文字列
	 * @final
	 */
	public final function join ($separator = null) {
		return $this->implode($separator);
	}

	/**
	 * 添字の配列を返す
	 *
	 * @access public
	 * @return BSArray 添字の配列
	 */
	public final function getKeys () {
		if (!$this->keys) {
			$this->keys = new BSArray(array_keys($this->getParameters()));
		}
		return $this->keys;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return ArrayIterator 配列イテレータ
	 */
	public function getIterator () {
		return new ArrayIterator($this->getParameters());
	}

	/**
	 * 要素が存在するか
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getParameter($key);
	}

	/**
	 * 要素を設定する
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->setParameter($key, $value);
	}

	/**
	 * 要素を削除する
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->removeParameter($key);
	}

	/**
	 * 要素数を返す
	 *
	 * @access public
	 * @return integer 要素数
	 */
	public function count () {
		return count($this->getParameters());
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return $this->join("\n");
	}
}

/* vim:set tabstop=4 ai: */
?>