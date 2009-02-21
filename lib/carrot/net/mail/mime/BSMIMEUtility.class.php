<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * MIMEユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEUtility {
	const ENCODE_PREFIX = '=?iso-2022-jp?B?';
	const ENCODE_SUFFIX = '?=';

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 文字列をエンコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string Bエンコードされた文字列
	 * @static
	 */
	static public function encode ($str) {
		if (BSString::getEncoding($str) == 'ascii') {
			return $str;
		}

		$str = BSString::convertKana($str, 'KV');
		while (preg_match('/[^[:print:]]+/u', $str, $matches)) {
			$word = BSString::convertEncoding($matches[0], 'iso-2022-jp');
			$encoded = self::ENCODE_PREFIX;
			$encoded .= base64_encode($word . chr(27) . '(B');
			$encoded .= self::ENCODE_SUFFIX;
			$str = str_replace($matches[0], $encoded, $str);
		}
		return $str;
	}

	/**
	 * 文字列をデコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function decode ($str) {
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

	/**
	 * レンダラーのContent-Transfer-Encodingを返す
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @return string Content-Transfer-Encoding
	 * @static
	 */
	static public function getContentTransferEncoding (BSRenderer $renderer) {
		if ($renderer instanceof BSTextRenderer) {
			if (strtolower($renderer->getEncoding()) == 'iso-2022-jp') {
				return '7bit';
			}
			return '8bit';
		}
		return 'base64';
	}

	/**
	 * レンダラーの完全なタイプを返す
	 *
	 * @access public
	 * @param BSRenderer $renderer 対象レンダラー
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getContentType (BSRenderer $renderer) {
		if ($renderer instanceof BSTextRenderer) {
			if (BSString::isBlank($charset = mb_preferred_mime_name($renderer->getEncoding()))) {
				throw new BSViewException(
					'エンコード"%s"が正しくありません。',
					$renderer->getEncoding()
				);
			}
			return sprintf('%s; charset=%s', $renderer->getType(), $charset);
		}
		return $renderer->getType();
	}
}

/* vim:set tabstop=4: */
