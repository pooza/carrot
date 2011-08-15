<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Operaユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSOperaUserAgent extends BSUserAgent {

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
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function encodeFileName ($name) {
		$name = BSString::convertEncoding($name, 'utf-8');
		return BSString::sanitize($name);
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'Opera';
	}
}

/* vim:set tabstop=4: */
