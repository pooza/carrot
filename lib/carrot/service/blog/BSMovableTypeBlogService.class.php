<?php
/**
 * @package jp.co.b-shock.carrot
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
			$constants = new BSConstantHandler('BLOG');
			if (!$url = $constants[$type . '_URL']) {
				throw new BSConfigException('URLが指定されていません。');
			}
			$this->urls[$type] = BSURL::create($url);
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
		$query = new BSWWWFormRenderer;
		$query->setParameters($values);
		$query['entry_id'] = $entry;
		$query['post'] = true;
		$http = new BSCurlHTTP($url['host']);
		$http->sendPOST($url->getFullPath(), $query);

		$message = new BSStringFormat('%sにコメントを送信しました。');
		$message[] = $url;
		BSLogManager::getInstance()->put($message, $this);
	}
}

/* vim:set tabstop=4: */
