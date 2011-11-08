<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.smartphone
 */

/**
 * Androidユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAndroidUserAgent extends BSWebKitUserAgent {

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this['is_web_kit'] = true;
		$this->supports['html5_audio'] = version_compare('533.0', $this->getVersion(), '<');
		$this->supports['html5_audio_mp3'] = $this->supports['html5_audio'];
		$this->supports['html5_audio_aac'] = $this->supports['html5_audio'];
		$this->supports['html5_video'] = version_compare('533.0', $this->getVersion(), '<');
		$this->supports['html5_video_webm'] = $this->supports['html5_video'];
		$this->supports['html5_video_h264'] = false;
		$this->supports['flash'] = version_compare('530.0', $this->getVersion(), '<');
	}

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 * @link http://googlewebmastercentral-ja.blogspot.com/2011/05/android.html
	 * @link http://blog.fkoji.com/2011/05021907.html
	 */
	public function isSmartPhone () {
		return (BSString::isContain('Mobile', $this->getName())
			&& !BSString::isContain('SC-01C', $this->getName()) //GALAXY Tabは例外として除外
		);
	}

	/**
	 * タブレット型か？
	 *
	 * @access public
	 * @return boolean タブレット型ならTrue
	 */
	public function isTablet () {
		return !$this->isSmartPhone();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$info = new BSArray;
		if ($this->isSmartPhone()) {
			$info['width'] = 480;
		}
		return $info;
	}

	/**
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest(array(
				__CLASS__,
				$this->isTablet(),
			));
		}
		return $this->digest;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'Android';
	}
}

/* vim:set tabstop=4: */
