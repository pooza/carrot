<?php
/**
 * @package org.carrot-framework
 * @subpackage service.blog
 */

/**
 * Blog更新Pingサービス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBlogUpdatePingService extends BSCurlHTTP {

	/**
	 * 更新Pingを送る
	 *
	 * @access public
	 * @param string $href 送信先
	 * @param BSBlogUpdatePingRequest $xml リクエスト文書
	 */
	public function sendPing ($href, BSBlogUpdatePingRequest $xml) {
		$url = $this->createURL($href);

		$request = new BSHTTPRequest;
		$request->setURL($url);
		$request->setMethod('post');
		$request->setRenderer($xml);
		$this->setAttribute('post', true);
		$this->setAttribute('customrequest', $request->getContents());

		try {
			$response = $this->execute($href);
			$message = new BSStringFormat('%sへ更新Ping送信を送信しました。');
			$message[] = $url->getContents();
			BSLogManager::getInstance()->put($message, $this);
		} catch (Exception $e) {
			$message = new BSStringFormat('%sへの更新Ping送信に失敗しました。 (%s)');
			$message[] = $url->getContents();
			$message[] = $e->getMessage();
			throw new BSBlogException($message);
		}
	}

	/**
	 * 更新Pingをまとめて送る
	 *
	 * @access public
	 * @static
	 */
	static public function sendPings () {
		$config = BSConfigManager::getInstance()->compile('blog');
		if (!isset($config['ping']['urls'])) {
			throw new BSBlogException('更新Pingの送信先を取得できません。');
		}
		$urls = new BSArray($config['ping']['urls']);

		$request = new BSBlogUpdatePingRequest;
		$request->registerParameter(BS_APP_NAME_JA);
		$request->registerParameter(BS_ROOT_URL);

		foreach ($urls as $url) {
			try {
				$url = BSURL::getInstance($url);
				$server = new BSBlogUpdatePingService($url['host']);
				$server->sendPing($url->getFullPath(), $request);
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
