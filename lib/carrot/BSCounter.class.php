<?php
/**
 * @package org.carrot-framework
 */

/**
 * シンプル汎用カウンター
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCounter {
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
		if (!$this->getUser()->hasAttribute($this->getSerializedName())) {
			$count = BSController::getInstance()->getAttribute($this->getSerializedName());
			$count ++;
			BSController::getInstance()->setAttribute($this->getSerializedName(), $count);
			$this->getUser()->setAttribute($this->getSerializedName(), $count);
		}
		return $this->getUser()->getAttribute($this->getSerializedName());
	}

	/**
	 * カウンタを破棄
	 *
	 * @access public
	 */
	public function release () {
		if ($this->getUser()->hasAttribute($this->getSerializedName())) {
			$this->getUser()->removeAttribute($this->getSerializedName());
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
	 * 属性に使用する名前を返す
	 *
	 * @access private
	 * @return string 名前
	 */
	private function getSerializedName () {
		return get_class($this) . '.' . $this->name;
	}
}

/* vim:set tabstop=4: */
