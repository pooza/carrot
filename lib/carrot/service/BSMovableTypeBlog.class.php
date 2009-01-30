<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * MovableTypeクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMovableTypeBlog {
	private $urls;

	private function getURL ($type = 'comment') {
		if (!$this->urls) {
			$this->urls = new BSArray;
		}
		if (!$url = $this->urls[$type]) {
			$constants = BSConstantHandler::getInstance();
			if (!$url = $constants['BLOG_' . $type . '_URL']) {
				throw new BSConfigException('URLが指定されていません。');
			}
			$this->urls[$type] = new BSURL($url);
		}
		return $this->urls[$type];
	}

	/**
	 * コメントを送信
	 *
	 * @access public
	 * @param integer $entry エントリーID
	 * @param string[] $values パラメータ
	 */
	public function postComment ($entry, $values) {
		$url = $this->getURL('comment');
		$values['entry_id'] = $entry;
		$values['post'] = true;
		$http = new BSCurlHTTP($url['host']);
		$http->sendPostRequest($url->getFullPath(), $values);
	}
}

/* vim:set tabstop=4: */
