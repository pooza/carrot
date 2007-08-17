<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 数値演算に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSNumeric {

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * 数値を四捨五入
	 *
	 * @access public
	 * @return integer 四捨五入された数値
	 * @param float $num 処理対象の数値
	 * @static
	 */
	public static function round ($num) {
		return floor($num + 0.5);
	}

	/**
	 * 数値をカンマ区切りに書式化
	 *
	 * @access public
	 * @param float $num 処理対象の数値
	 * @param int $digits 処理対象が小数であったときの有効桁数、既定値は2。
	 * @return string カンマ区切りされた数値
	 * @static
	 */
	public static function getString ($num, $digits = 2) {
		if (!$num) {
			return '';
		} else if ($num != floor($num)) {
			return number_format($num, $digits);
		} else {
			return number_format($num);
		}
	}

	/**
	 * 数値の符号を返す
	 *
	 * @access public
	 * @param float $num 処理対象の数値
	 * @return string 符号
	 * @static
	 */
	public static function getSign ($num) {
		if (0 < $num) {
			return '+';
		} else if ($num < 0) {
			return '-';
		}
	}

	/**
	 * 数字で分けた配列を返す
	 *
	 * @access public
	 * @param integer $num 処理対象の数値
	 * @return integer[] 数字の配列
	 * @static
	 */
	public static function getDigits ($num) {
		$digits = array();
		for ($i = 0 ; $i <= strlen($num) - 1 ; $i ++) {
			$digits[] = substr($num, $i, 1);
		}
		return $digits;
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