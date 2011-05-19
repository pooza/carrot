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
	const DEFAULT_NAME = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6; ja-jp) AppleWebKit';
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

	/**
	 * Google Chromeか？
	 *
	 * @access public
	 * @return boolean Google ChromeならTrue
	 */
	public function isChrome () {
		return BSString::isContain('Chrome', $this->getName());
	}
}

/* vim:set tabstop=4: */
