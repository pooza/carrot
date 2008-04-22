<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * Webkitユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSWebKitUserAgent.class.php 105 2007-12-14 02:26:43Z pooza $
 */
class BSWebKitUserAgent extends BSUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'Safari';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/AppleWebKit/';
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 * @todo 正しいファイル名を返せる様に対応（Apple側のバグフィックス待ち）
	 */
	public function getEncodedFileName ($name) {
		// 漢字を "?" に変える。そのほうが少しだけマシなので。
		$name = BSString::convertEncoding($name, 'iso-8859-1');

		return BSString::sanitize($name);
	}

	/**
	 * アップロードボタンのラベルを返す
	 *
	 * @access public
	 * @return string アップロードボタンのラベル
	 */
	public function getUploadButtonLabel () {
		return 'ファイルを選択';
	}
}

/* vim:set tabstop=4 ai: */
?>