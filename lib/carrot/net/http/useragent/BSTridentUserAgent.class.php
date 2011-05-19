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
		$this['is_kuso'] = ($this->getVersion() < 8);
		$this['is_ie' . floor($this->getVersion())] = true;
		$this->supports['html5_audio'] = (8 < $this->getVersion());
		$this->supports['html5_video'] = (8 < $this->getVersion());
		$this->supports['html5_video_h264'] = (8 < $this->getVersion());
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
		if (!$this['platform']) {
			if (mb_ereg($this->getPattern(), $this->getName(), $matches)) {
				$this['platform'] = $matches[2];
			}
		}
		return $this['platform'];
	}

	/**
	 * メジャーバージョンを返す
	 *
	 * @access public
	 * @return string メジャーバージョン
	 */
	public function getVersion () {
		if (!$this['version']) {
			if (mb_ereg($this->getPattern(), $this->getName(), $matches)) {
				$this['version'] = (int)$matches[1];
			}
		}
		return $this['version'];
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
			));
		}
		return $this->digest;
	}

	/**
	 * レガシー環境/旧機種か？
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 */
	public function isLegacy () {
		return $this->getVersion() < 6;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'MSIE ([[:digit:].]+); ([^;]+);';
	}
}

/* vim:set tabstop=4: */
