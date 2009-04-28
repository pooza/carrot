<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime
 */

/**
 * MIMEユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEUtility {
	const ATTACHMENT = 'attachment';
	const INLINE = 'inline';
	const ENCODE_PREFIX = '=?iso-2022-jp?B?';
	const ENCODE_SUFFIX = '?=';
	const WITH_SPLIT = 1;
	const WITHOUT_HEADER = 0;
	const WITH_HEADER = 1;
	const IGNORE_INVALID_TYPE = 1;

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
		preg_match_all('/[^[:print:]]+/u', $str, $matchesAll, PREG_SET_ORDER);
		foreach ($matchesAll as $matches) {
			$word = BSString::convertEncoding($matches[0], 'iso-2022-jp');
			$encoded = self::ENCODE_PREFIX . self::encodeBase64($word) . self::ENCODE_SUFFIX;
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
		$pattern = '/=\\?([^\\?]+)\\?([bq])\\?([^\\?]+)\\?=/i';
		preg_match_all($pattern, $str, $matchesAll, PREG_SET_ORDER);
		foreach ($matchesAll as $matches) {
			switch (strtolower($matches[2])) {
				case 'b':
					$decoded = self::decodeBase64($matches[3]);
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
	 * @param string $str 対象文字列
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function decodeQuotedPrintable ($str) {
		preg_match_all('/=([a-f0-9]{2})/i', $str, $matchesAll, PREG_SET_ORDER);
		foreach ($matchesAll as $matches) {
			$str = str_replace($matches[0], chr(hexdec($matches[1])), $str);
		}
		return $str;
	}

	/**
	 * Bエンコードされた文字列をデコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string デコードされた文字列
	 * @static
	 */
	static public function decodeBase64 ($str) {
		return base64_decode($str);
	}

	/**
	 * 文字列をBエンコード
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @param integer $flag フラグ
	 *   self::WITH_SPLIT
	 * @return string エンコードされた文字列
	 * @static
	 */
	static public function encodeBase64 ($str, $flag = null) {
		$str = base64_encode($str);
		if ($flag & self::WITH_SPLIT) {
			$str = chunk_split($str);
		}
		return $str;
	}

	/**
	 * レンダラーのContent-Transfer-Encodingを返す
	 *
	 * BSContentTransferEncodingMIMEHeader::getContentTransferEncodingのエイリアス
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @return string Content-Transfer-Encoding
	 * @static
	 */
	static public function getContentTransferEncoding (BSRenderer $renderer) {
		return BSContentTransferEncodingMIMEHeader::getContentTransferEncoding($renderer);
	}

	/**
	 * レンダラーの完全なタイプを返す
	 *
	 * BSContentTypeMIMEHeader::getContentTypeのエイリアス
	 *
	 * @access public
	 * @param BSRenderer $renderer 対象レンダラー
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getContentType (BSRenderer $renderer) {
		return BSContentTypeMIMEHeader::getContentType($renderer);
	}

	/**
	 * アップロード可能なメディアタイプを返す
	 *
	 * BSMIMEType::getAttachableTypesのエイリアス
	 *
	 * @access public
	 * @return BSArray メディアタイプの配列
	 * @static
	 */
	static public function getAttachableTypes () {
		return BSMIMEType::getAttachableTypes();
	}

	/**
	 * 規定のメディアタイプを返す
	 *
	 * BSMIMEType::getTypeのエイリアス
	 *
	 * @access public
	 * @param string $suffix サフィックス
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getType ($suffix) {
		return BSMIMEType::getType($suffix);
	}

	/**
	 * ファイル名から拡張子を返す
	 *
	 * @access public
	 * @param string $filename 又はファイル名
	 * @return string 拡張子
	 * @static
	 */
	static public function getFileNameSuffix ($filename) {
		$parts = explode('.', $filename);
		return '.' . array_pop($parts);
	}
}

/* vim:set tabstop=4: */
