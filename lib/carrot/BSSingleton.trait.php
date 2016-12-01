<?php
/**
 * @package org.carrot-framework
 */

/**
 * シングルトン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
trait BSSingleton {
	static private $instance;

	/**
	 * @access protected
	 */
	protected function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSRequest インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new RuntimeException(__CLASS__ . 'はコピーできません。');
	}
}

/* vim:set tabstop=4: */
