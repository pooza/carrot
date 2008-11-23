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
class BSArray extends BSParameterHolder implements Countable {
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
	 * 要素をまとめて設定
	 *
	 * @access public
	 * @param mixed[] $values 要素の配列
	 */
	public function setParameters ($values) {
		if ($values instanceof BSParameterHolder) {
			$values = $values->getParameters();
		} else if (BSNumeric::isZero($values)) {
			$values = array(0);
		} else if (!$values) {
			return;
		}
		foreach ((array)$values as $name => $value) {
			$this->parameters[$name] = $value;
		}
	}

	/**
	 * 要素をまとめて設定
	 *
	 * setParametersのエイリアス
	 *
	 * @access public
	 * @param mixed[] $attributes 要素の配列
	 * @final
	 */
	final public function setAttributes ($attributes) {
		$this->setParameters($attributes);
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
		} else if (BSNumeric::isZero($values)) {
			$values = array(0);
		} else if (!$values) {
			return;
		}
		foreach ((array)$values as $value) {
			$this->parameters[] = $value;
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
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('パラメータ名が文字列ではありません。');
		}
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
	 * 要素を設定
	 *
	 * setParameterのエイリアス
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 * @final
	 */
	final public function setAttribute ($name, $value, $position = self::POSITION_BOTTOM) {
		$this->setParameter($name, $value, $position);
	}

	/**
	 * 要素を削除
	 *
	 * removeParameterのエイリアス
	 *
	 * @access public
	 * @param string $name 名前
	 * @final
	 */
	final public function removeAttribute ($name) {
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
	final public function hasAttribute ($name) {
		return $this->hasParameter($name);
	}

	/**
	 * ソート
	 *
	 * @access public
	 * @param string $order ソート順
	 */
	public function sort ($order = self::SORT_KEY_ASC) {
		$funcs = new BSArray;
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
	 * @param mixed $values 値、又は値の配列
	 * @return boolean 値が含まれていればTrue
	 */
	public function isIncluded ($values) {
		if (!BSArray::isArray($values)) {
			$values = array($values);
		}

		foreach ($values as $value) {
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
	 */
	public function uniquize () {
		$this->parameters = array_unique($this->parameters);
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
	final public function join ($separator = null) {
		return $this->implode($separator);
	}

	/**
	 * 添字の配列を返す
	 *
	 * @access public
	 * @param string $option オプションのビット列
	 *   self::WITHOUT_KEY:キーを含まない
	 * @return BSArray 添字の配列
	 */
	public function getKeys ($option = null) {
		if ($option & self::WITHOUT_KEY) {
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
	 * 要素数を返す
	 *
	 * @access public
	 * @return integer 要素数
	 */
	public function count () {
		return count($this->getParameters());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return $this->join("\n");
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

/* vim:set tabstop=4 ai: */
?>