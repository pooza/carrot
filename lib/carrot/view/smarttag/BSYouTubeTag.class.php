<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarttag
 */

/**
 * YouTubeタグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
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
			$params = new BSArray($this->getQueryParameters());
			$info = $this->getUserAgent()->getDisplayInfo();
			if ($info['width'] && $params['width'] && ($info['width'] < $params['width'])) {
				$params['width'] = $info['width'];
			}

			$element = new BSYouTubeObjectElement;
			$element->setUserAgent($this->getUserAgent());
			$element->setMovie($this->tag[1], $params);
			$element->setAttribute('width', $params['width']);
			$element->setAttribute('height', $params['height']);
			$replace = $element->getContents();
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}
}

/* vim:set tabstop=4: */
