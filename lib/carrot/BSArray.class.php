<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 配列
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSArray extends ParameterHolder implements IteratorAggregate, ArrayAccess, Countable {
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
}

/* vim:set tabstop=4 ai: */
?>