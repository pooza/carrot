<?php
/**
 * @package org.carrot-framework
 * @subpackage service.blog
 */

/**
 * MovableTypeクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMovableTypeBlogService {
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
			$this->urls[$type] = BSURL::getInstance($url);
		}
		return $this->urls[$type];
	}

	/**
	 * コメントを送信
	 *
	 * @access public
	 * @param integer $entry エントリーID
	 * @param BSArray $values パラメータ
	 */
	public function postComment ($entry, BSArray $values) {
		$url = $this->getURL('comment');
		$values['entry_id'] = $entry;
		$values['post'] = true;
		$http = new BSCurlHTTP($url['host']);
		$http->sendPOST($url->getFullPath(), $values);

		$message = new BSStringFormat('%sにコメントを送信しました。');
		$message[] = $url;
		BSLogManager::getInstance()->put($message, $this);
	}
}

/* vim:set tabstop=4: */
