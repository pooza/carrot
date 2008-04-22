<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 汎用属性リスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSList.class.php 84 2007-11-04 03:51:29Z pooza $
 * @abstract
 */
abstract class BSList implements IteratorAggregate {
	protected $attributes;

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed 属性
	 */
	public function getAttribute ($name) {
		$attributes = $this->getAttributes();
		if (isset($attributes[$name])) {
			return $attributes[$name];
		}
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 * @abstract
	 */
	abstract public function getAttributes ();

	/**
	 * 内容を返す
	 *
	 * getAttributesへのエイリアス
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 * @final
	 */
	final public function getContents () {
		return $this->getAttributes();
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		throw new BSException('属性を変更することは出来ません。');
	}

	/**
	 * 複数の属性を設定
	 *
	 * @access public
	 * @param mixed[] $values 属性の配列
	 */
	public function setAttributes ($values) {
		foreach ($values as $key => $value) {
			$this->setAttribute($key, $value);
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return ArrayIterator 配列イテレータ（PHP標準）
	 */
	public function getIterator () {
		return new ArrayIterator($this->getAttributes());
	}

	/**
	 * クラス名を返す
	 *
	 * @access public
	 * @return string クラス名
	 */
	public function getName () {
		return get_class($this);
	}
}

/* vim:set tabstop=4 ai: */
?>