<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * InternetExplorerユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSMSIEUserAgent.class.php 293 2007-02-20 13:33:02Z pooza $
 */
class BSMSIEUserAgent extends BSUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'InternetExplorer';
	}

	/**
	 * メジャーバージョンを返す
	 *
	 * @access public
	 * @return string メジャーバージョン
	 */
	public function getMajorVersion () {
		preg_match('/MSIE ([0-9]+)\./', $this->getName(), $matches);
		return $matches[1];
	}

	/**
	 * マイナーバージョンを返す
	 *
	 * @access public
	 * @return string マイナーバージョン
	 */
	public function getMinorVersion () {
		preg_match('/MSIE ([0-9]+\.[0-9]+)/', $this->getName(), $matches);
		return $matches[1];
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		return array_merge(
			array(
				'is_msie' => true,
				'is_msie' . $this->getMajorVersion() => true,
				'is_msie' . $this->getMinorVersion() => true,
			),
			parent::getAttributes()
		);
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function getEncodedFileName ($name) {
		$name = BSString::convertEncoding($name, 'sjis');
		return BSString::sanitize($name);
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/MSIE/';
	}
}

/* vim:set tabstop=4 ai: */
?>