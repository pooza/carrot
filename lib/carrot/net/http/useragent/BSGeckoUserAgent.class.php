<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Geckoユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGeckoUserAgent extends BSUserAgent {

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this->supports['html5_video'] = true;
		$this->supports['html5_audio'] = true;
		$this->supports['html5_video_webm'] = true;
		$this->supports['html5_audio_ogg'] = true;
		$this->supports['flash'] = true;
		$this->supports['cookie'] = true;
		$this->supports['attach_file'] = true;
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		if (!$this['version']) {
			if (mb_ereg('rv:([.[:digit:]]+)', $this->getName(), $matches)) {
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
		return version_compare($this->getVersion(), '1.7.5', '<'); // Firefox 1.0未満
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'Gecko/[[:digit:]]+';
	}
}

/* vim:set tabstop=4: */
