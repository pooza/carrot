<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent.smartphone
 */

/**
 * Windows Phoneユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWindowsPhoneUserAgent extends BSWebKitUserAgent {

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this->supports['flash'] = false;
	}

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 */
	public function isSmartPhone () {
		return true;
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		$info = new BSArray;
		$info['width'] = BS_VIEW_LAYOUT_SMARTPHONE_WIDTH;
		return $info;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'Windows Phone';
	}
}

