<?php
/**
 * @package org.carrot-framework
 */

/**
 * 数値演算に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSNumeric {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 数値を四捨五入
	 *
	 * @access public
	 * @param float $num 処理対象の数値
	 * @return integer 四捨五入された数値
	 * @static
	 */
	static public function round ($num) {
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
	static public function getString ($num, $digits = 2) {
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
	static public function getSign ($num) {
		if (0 < $num) {
			return '+';
		} else if ($num < 0) {
			return '-';
		}
	}

	/**
	 * バイナリ書式化して返す
	 *
	 * @access public
	 * @param float $number 処理対象の数値
	 * @return string バイナリ書式化された数値
	 * @static
	 */
	static public function getBinarySize ($num) {
		foreach (array('', 'K', 'M', 'G', 'T', 'P', 'E', 'z') as $i => $unit) {
			$unitsize = pow(1024, $i);
			if ($num < ($unitsize * 1024 * 2)) {
				return number_format(floor($num / $unitsize)) . $unit;
			}
		}
	}

	/**
	 * 数字で分けた配列を返す
	 *
	 * @access public
	 * @param integer $num 処理対象の数値
	 * @return BSArray 数字の配列
	 * @static
	 */
	static public function getDigits ($num) {
		$digits = new BSArray;
		for ($i = 0 ; $i < strlen($num) ; $i ++) {
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
	static public function getRandom ($from = 1000000, $to = 9999999) {
		return mt_rand($from, $to);
	}
}

/* vim:set tabstop=4 ai: */
?>