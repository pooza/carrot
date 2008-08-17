<?php
/**
 * @package org.carrot-framework
 */

/**
 * シンプル汎用カウンター
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCounter {
	private $file;
	private $name;

	/**
	 * コンストラクタ
	 *
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
		if (!$this->getUser()->hasAttribute($this->getAttributeName())) {
			$count = BSController::getInstance()->getAttribute($this->getAttributeName());
			$count ++;
			BSController::getInstance()->setAttribute($this->getAttributeName(), $count);
			$this->getUser()->setAttribute($this->getAttributeName(), $count);
		}
		return $this->getUser()->getAttribute($this->getAttributeName());
	}

	/**
	 * カウンタを破棄
	 *
	 * @access public
	 */
	public function release () {
		if ($this->getUser()->hasAttribute($this->getAttributeName())) {
			$this->getUser()->removeAttribute($this->getAttributeName());
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
	private function getAttributeName () {
		return get_class($this) . '.' . $this->name;
	}
}

/* vim:set tabstop=4 ai: */
?>