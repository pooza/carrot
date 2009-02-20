<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail
 */

/**
 * メールユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 文字列をbase64エンコード
	 *
	 * @access public
	 * @return string MIME'B'エンコードされた文字列
	 * @static
	 */
	static public function base64Encode ($str) {
		if (BSString::getEncoding($str) == 'ascii') {
			return $str;
		}

		$str = BSString::convertKana($str, 'KV');
		while (preg_match('/[^[:print:]]+/u', $str, $matches)) {
			$encoded = BSString::convertEncoding($matches[0], 'iso-2022-jp');
			$encoded = '=?iso-2022-jp?B?' . base64_encode($encoded . chr(27) . '(B') . '?=';
			$str = str_replace($matches[0], $encoded, $str);
		}
		return $str;
	}

	/**
	 * 文字列をbase64デコード
	 *
	 * @access public
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function base64Decode ($str) {
		while (preg_match('/=\\?iso-2022-jp\\?b\\?([^\\?]+)\\?=/i', $str, $matches)) {
			$decoded = base64_decode($matches[1]);
			$decoded = BSString::convertEncoding($decoded);
			$str = str_replace($matches[0], $decoded, $str);
		}
		return $str;
	}
}

/* vim:set tabstop=4: */
