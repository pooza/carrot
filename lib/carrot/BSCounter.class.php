<?php
/**
 * @package org.carrot-framework
 */

/**
 * シンプル汎用カウンター
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCounter implements BSSerializable {
	private $file;
	private $name;

	/**
	 * @access public
	 * @param string $name カウンター名
	 */
	public function __construct ($name = 'count') {
		$this->name = $name;
	}

	/**
	 * カウンタをインクリメントして、値を返す
	 *
	 * @access public
	 * @return integer カウンターの値
	 */
	public function getContents () {
		if (!$this->getUser()->hasAttribute($this->serializeName())) {
			$this->serialize();
			$this->getUser()->setAttribute($this->serializeName(), $this->getSerialized());
		}
		return $this->getUser()->getAttribute($this->serializeName());
	}

	/**
	 * カウンタを破棄
	 *
	 * @access public
	 */
	public function release () {
		if ($this->getUser()->hasAttribute($this->serializeName())) {
			$this->getUser()->removeAttribute($this->serializeName());
		}
	}

	/**
	 * carrotユーザーを返す
	 *
	 * @access private
	 * @return BSUser carrotユーザー
	 */
	private function getUser () {
		return BSUser::getInstance();
	}

	/**
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
		return get_class($this) . '.' . $this->name;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$count = (int)$this->getSerialized($this);
		$count ++;
		BSController::getInstance()->setAttribute($this, $count);
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('カウンター "%s"', $this->name);
	}
}

/* vim:set tabstop=4: */
