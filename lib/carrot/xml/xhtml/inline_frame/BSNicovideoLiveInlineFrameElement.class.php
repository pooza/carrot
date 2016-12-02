<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.inline_frame
 */

/**
 * ニコニコ生放送のiframe要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSNicovideoLiveInlineFrameElement extends BSInlineFrameElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('width', BS_SERVICE_NICOVIDEO_LIVE_WIDTH);
		$this->setAttribute('height', BS_SERVICE_NICOVIDEO_LIVE_HEIGHT);
		$this->setStyle('border', 'solid 1px #ccc');
	}

	/**
	 * チャンネルを設定
	 *
	 * @access public
	 * @param string $id チャンネルID
	 */
	public function setChannel ($id) {
		$url = BSURL::create();
		$url['host'] = BSNicovideoLiveService::DEFAULT_HOST;
		$url['path'] = '/embed/' . $id;
		$this->setURL($url);
	}
}

/* vim:set tabstop=4: */
