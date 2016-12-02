<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.smarttag
 */

/**
 * YouTubeタグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSYouTubeTag extends BSSmartTag {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTagName () {
		return 'youtube';
	}

	/**
	 * 置換して返す
	 *
	 * @access public
	 * @param string $body 置換対象文字列
	 * @return string 置換された文字列
	 */
	public function execute ($body) {
		try {
			$service = new BSYouTubeService;
			$service->setUserAgent($this->getUserAgent());
			$params = new BSArray($this->params);
			$params->setParameters($this->getQueryParameters());
			$element = $service->createElement($this->tag[1], $params);
			$replace = $element->getContents();
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}
}

/* vim:set tabstop=4: */
