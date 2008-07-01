<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * パラメータホルダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSParameterHolder implements IteratorAggregate, ArrayAccess {
	protected $parameters = array();

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		if ($this->hasParameter($name)) {
			return $this->parameters[$name];
		}
	}

	/**
	 * パラメータを設定する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 * 全てのパラメータを返す
	 *
	 * @access public
	 * @return mixed[] 全てのパラメータ
	 */
	public function getParameters () {
		return $this->parameters;
	}

	/**
	 * パラメータをまとめて設定する
	 *
	 * @access public
	 * @param mixed[] パラメータ
	 */
	public function setParameters ($parameters) {
		foreach ($parameters as $key => $value) {
			$this->setParameter($key, $value);
		}
	}

	/**
	 * パラメータが存在するか？
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return boolean 存在すればTrue
	 */
	public function hasParameter ($name) {
		return isset($this->parameters[$name]);
	}

	/**
	 * 全てのパラメータ名を返す
	 *
	 * @access public
	 * @return string[] 全てのパラメータ名
	 */
	public function getParameterNames () {
		return array_keys($this->getParameters());
	}

	/**
	 * パラメータを削除する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 */
	public function removeParameter ($name) {
		if ($this->hasParameter($name)) {
			unset($this->parameters[$name]);
		}
	}

	/**
	 * 全てのパラメータを削除する
	 *
	 * clearParametersのエイリアス
	 *
	 * @access public
	 * @final
	 */
	final public function clear () {
		$this->clearParameters();
	}

	/**
	 * 全てのパラメータを削除する
	 *
	 * @access public
	 */
	public function clearParameters () {
		foreach ($this as $name => $value) {
			$this->removeParameter($name);
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->getParameters());
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