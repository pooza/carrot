<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage numeric
 */

/**
 * 乱数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSRandom.class.php 261 2007-01-03 13:25:18Z pooza $
 */
class BSRandom {
	private static $instance;

	/**
	 * コンストラクタ
	 *
	 * @access protected
	 */
	protected function __construct () {
		mt_srand(time());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSRandom インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSRandom();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 乱数を返す
	 *
	 * @access public
	 * @param float $from 乱数の範囲（最小値）
	 * @param float $to 乱数の範囲（最大値）
	 * @return integer 乱数
	 * @static
	 */
	public static function getRandom ($from = 1000000, $to = 9999999) {
		return mt_rand($from, $to);
	}
}

/* vim:set tabstop=4 ai: */
?>