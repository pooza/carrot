<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Webkitユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWebKitUserAgent extends BSUserAgent {
	const DEFAULT_NAME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6;) AppleWebKit/533';
	const ACCESSOR = 'force_webkit';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this->supports['html5_video'] = true;
		$this->supports['html5_video_webm'] = $this->isChrome();
		$this->supports['html5_video_h264'] = !$this->isChrome();
		$this->supports['html5_audio'] = true;
		$this->supports['html5_audio_mp3'] = true;
		$this->supports['html5_audio_aac'] = true;
		$this->supports['flash'] = true;
		$this->supports['cookie'] = true;
		$this->supports['attach_file'] = true;
	}

	/**
	 * Google Chromeか？
	 *
	 * @access public
	 * @return boolean Google ChromeならTrue
	 */
	public function isChrome () {
		return BSString::isContain('Chrome', $this->getName());
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		if (!$this['version']) {
			if (mb_ereg('AppleWebKit/([.[:digit:]]+)', $this->getName(), $matches)) {
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
		return version_compare($this->getVersion(), '100.0', '<'); // Safari 1.1未満
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'AppleWebKit';
	}
}

/* vim:set tabstop=4: */
