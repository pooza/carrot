<?php
/**
 * @package org.carrot-framework
 */

/**
 * 配列
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSArray extends BSParameterHolder implements BSAssignable {
	const POSITION_TOP = true;
	const POSITION_BOTTOM = false;
	const SORT_KEY_ASC = 'KEY_ASC';
	const SORT_KEY_DESC = 'KEY_DESC';
	const SORT_VALUE_ASC = 'VALUE_ASC';
	const SORT_VALUE_DESC = 'VALUE_DESC';
	const WITHOUT_KEY = 1;

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($params = array()) {
		$this->setParameters($params);
	}

	/**
	 * 別の配列をマージ
	 *
	 * ハッシュではない普通の配列同士は、setParametersではマージできない。
	 *
	 * @access public
	 * @param mixed $values 配列
	 */
	public function merge ($values) {
		if ($values instanceof BSParameterHolder) {
			$values = $values->getParameters();
		} else if (!is_array($values)) {
			return;
		}
		foreach ($values as $value) {
			$this->push($value);
		}
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $value, $position = self::POSITION_BOTTOM) {
		if ($name === null) {
			if ($position == self::POSITION_TOP) {
				$this->unshift($value);
			} else {
				$this->push($value);
			}
		} else {
			if ($position == self::POSITION_TOP) {
				$this->parameters = array((string)$name => null) + $this->parameters;
			}
			$this->parameters[(string)$name] = $value;
		}
	}

	/**
	 * 先頭要素を削除し、返す
	 *
	 * @access public
	 * @return mixed 削除された先頭要素
	 */
	public function shift () {
		return array_shift($this->parameters);
	}

	/**
	 * 先頭に要素を加える
	 *
	 * @access public
	 * @param mixed $value 要素
	 * @return BSArray 自分自身
	 */
	public function unshift ($value) {
		array_unshift($this->parameters, $value);
		return $this;
	}

	/**
	 * 末尾要素を削除し、返す
	 *
	 * @access public
	 * @return mixed 削除された末尾要素
	 */
	public function pop () {
		return array_pop($this->parameters);
	}

	/**
	 * 末尾に要素を加える
	 *
	 * @access public
	 * @param mixed $value 要素
	 * @return BSArray 自分自身
	 */
	public function push ($value) {
		$this->parameters[] = $value;
		return $this;
	}

	/**
	 * ソート
	 *
	 * @access public
	 * @param string $order ソート順
	 * @return BSArray 自分自身
	 */
	public function sort ($order = self::SORT_KEY_ASC) {
		$funcs = new BSArray;
		$funcs[self::SORT_KEY_ASC] = 'ksort';
		$funcs[self::SORT_KEY_DESC] = 'krsort';
		$funcs[self::SORT_VALUE_ASC] = 'asort';
		$funcs[self::SORT_VALUE_DESC] = 'arsort';

		if (BSString::isBlank($func = $funcs[$order])) {
			throw new BSInitializeException('BSArray::sortの引数が正しくありません。');
		}
		$func($this->parameters);
		return $this;
	}

	/**
	 * 値が含まれているか？
	 *
	 * @access public
	 * @param mixed $values 値、又は値の配列
	 * @return boolean 値が含まれていればTrue
	 */
	public function isContain ($values) {
		foreach (new BSArray($values) as $value) {
			if (in_array($value, $this->getParameters())) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 要素をユニーク化
	 *
	 * @access public
	 * @return BSArray 自分自身
	 */
	public function uniquize () {
		if (version_compare(PHP_VERSION, '5.2.9', '<')) {
			$this->parameters = array_unique($this->parameters);
		} else {
			$this->parameters = array_unique($this->parameters, SORT_STRING);
		}
		return $this;
	}

	/**
	 * 要素をフラット化
	 *
	 * @access public
	 * @param string $glue 接続子
	 * @return BSArray 自分自身
	 */
	public function flatten ($glue = '_') {
		$this->parameters = self::getFlatContents(null, $this->parameters, $glue);
		return $this;
	}
	static private function getFlatContents ($prefix, $arg, $glue) {
		$contents = array();
		if (BSArray::isArray($arg)) {
			foreach ($arg as $key => $value) {
				if (!BSString::isBlank($prefix)) {
					$key = $prefix . $glue . $key;
				}
				$contents += self::getFlatContents($key, $value, $glue);
			}
		} else {
			$contents[$prefix] = $arg;
		}
		return $contents;
	}

	/**
	 * トリミング
	 *
	 * @access public
	 * @return BSArray 自分自身
	 */
	public function trim () {
		foreach ($this as $key => $value) {
			if (BSString::isBlank($value)) {
				$this->removeParameter($key);
			}
		}
		return $this;
	}

	/**
	 * セパレータで結合した文字列を返す
	 *
	 * @access public
	 * @param string $separator セパレータ
	 * @return string 結果文字列
	 */
	public function join ($separator = null) {
		return implode($separator, $this->getParameters());
	}

	/**
	 * 添字の配列を返す
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_KEY:キーを含まない
	 * @return BSArray 添字の配列
	 */
	public function getKeys ($flags = null) {
		if ($flags & self::WITHOUT_KEY) {
			$keys = array_keys($this->getParameters());
		} else {
			$keys = array_flip($this->getParameters());
		}
		return new BSArray($keys);
	}

	/**
	 * ランダムな要素を返す
	 *
	 * @access public
	 * @return mixed ランダムな要素
	 */
	public function getRandom () {
		$key = $this->getKeys(self::WITHOUT_KEY)->getParameter(
			BSNumeric::getRandom(0, $this->count() - 1)
		);
		return $this[$key];
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getParameters();
	}

	/**
	 * 配列か？
	 *
	 * @access public
	 * @param mixed $value 対象
	 * @return boolean 配列ならTrue
	 * @static
	 */
	static public function isArray ($value) {
		return is_array($value) || ($value instanceof BSArray);
	}
}

/* vim:set tabstop=4: */
