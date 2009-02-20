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
	 * ヘッダをエンコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string Bエンコードされた文字列
	 * @static
	 */
	static public function encodeHeader ($str) {
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
	 * ヘッダをデコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function decodeHeader ($str) {
		while (preg_match('/=\\?([^\\?]+)\\?([bq])\\?([^\\?]+)\\?=/i', $str, $matches)) {
			switch (strtolower($matches[2])) {
				case 'b':
					$decoded = base64_decode($matches[3]);
					break;
				case 'q':
					$decoded = self::decodeQuotedPrintable($matches[3]);
					break;
			}
			$decoded = BSString::convertEncoding($decoded, 'utf-8', $matches[1]);
			$str = str_replace($matches[0], $decoded, $str);
		}
		return $str;
	}

	/**
	 * Qエンコードされた文字列をデコード
	 *
	 * @access public
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function decodeQuotedPrintable ($str) {
		while (preg_match('/=([a-f0-9]{2})/i', $str, $matches)) {
			$str = str_replace($matches[0], chr(hexdec($matches[1])), $str);
		}
		return $str;
	}
}

/* vim:set tabstop=4: */
