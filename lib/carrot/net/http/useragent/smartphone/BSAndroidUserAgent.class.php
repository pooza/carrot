<?php
/**
 * @package jp.co.b-shock.carrot
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
		$this->supports['html5_audio'] = version_compare('534.30', $this->getVersion(), '<');
		$this->supports['html5_video'] = version_compare('534.30', $this->getVersion(), '<');
		$this->supports['flash'] = false;
	}

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 * @link http://googlewebmastercentral-ja.blogspot.com/2011/05/android.html
	 */
	public function isSmartPhone () {
		return BSString::isContain('Mobile', $this->getName());
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
			$this->digest = BSCrypt::digest([
				__CLASS__,
				$this->supports['html5_video'],
				$this->supports['html5_audio'],
				$this->isTablet(),
			]);
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
