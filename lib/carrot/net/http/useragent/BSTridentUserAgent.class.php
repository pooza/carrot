<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Tridentユーザーエージェント
 *
 * Windows版 InternetExplorer 4.x以降
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTridentUserAgent extends BSUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->bugs['cache-control'] = true;
		$this->attributes['is_ie' . floor($this->getVersion())] = true;
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function encodeFileName ($name) {
		if (7 < $this->getVersion()) {
			$name = BSURL::encode($name);
		} else {
			$name = BSString::convertEncoding($name, 'sjis-win');
		}
		return BSString::sanitize($name);
	}

	/**
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			if (preg_match($this->getPattern(), $this->getName(), $matches)) {
				$this->attributes['platform'] = $matches[2];
			}
		}
		return $this->attributes['platform'];
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		if (!$this->attributes['version']) {
			if (preg_match($this->getPattern(), $this->getName(), $matches)) {
				$this->attributes['version'] = $matches[1];
			}
		}
		return $this->attributes['version'];
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/MSIE ([4-9]\.[0-9]+); ([^;]+);/';
	}
}

/* vim:set tabstop=4: */
