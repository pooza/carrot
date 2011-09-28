<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Tridentユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTridentUserAgent extends BSUserAgent {
	const DEFAULT_NAME = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)';
	const ACCESSOR = 'force_trident';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this->bugs['cache_control'] = true;
		$this->bugs['object_wmode'] = (8 < $this->getVersion());
		$this->supports['html5_audio'] = (8 < $this->getVersion());
		$this->supports['html5_video'] = (8 < $this->getVersion());
		$this->supports['html5_video_h264'] = (8 < $this->getVersion());
		$this->supports['flash'] = true;
		$this['is_ie' . (int)$this->getVersion()] = true;
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
	 * レンダリング用ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest(array(
				__CLASS__,
				$this->getVersion(),
				$this->isSmartPhone(),
				$this->isTablet(),
			));
		}
		return $this->digest;
	}

	/**
	 * バージョンを返す
	 *
	 * 取得できないケースがある為、IEとしてのバージョンで代用。
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		if (!$this['version']) {
			if (mb_ereg('MSIE ([.[:digit:]]+);', $this->getName(), $matches)) {
				$this['version'] = $matches[1];
			}
		}
		return $this['version'];
	}

	/**
	 * レガシー環境/旧機種か？
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 */
	public function isLegacy () {
		return $this->getVersion() < 6; // IE6未満
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'MSIE';
	}
}

/* vim:set tabstop=4: */
