<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.blog
 */

/**
 * Blog更新Pingサービス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		$request = $this->createRequest();
		$request->setMethod('POST');
		$request->setRenderer($xml);
		$request->setURL($this->createRequestURL($href));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $request->getRenderer()->getContents());

		try {
			$response = $this->send($request);
			$xml = new SimpleXMLElement($response->getRenderer()->getContents());
			$element = $xml->params->param->value->struct;
			if ((string)$element->member[0]->value->boolean !== '0') {
				throw new BSBlogException((string)$element->member[1]->value->string);
			}
			$message = new BSStringFormat('%sへ更新Pingを送信しました。');
			$message[] = $request->getURL()->getContents();
			BSLogManager::getInstance()->put($message, $this);
		} catch (Exception $e) {
			$message = new BSStringFormat('%sへの更新Ping送信に失敗しました。 (%s)');
			$message[] = $request->getURL()->getContents();
			$message[] = $e->getMessage();
			throw new BSBlogException($message);
		}
	}

	/**
	 * 更新Pingをまとめて送る
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列。以下の要素を含むこと。
	 *   weblogname   ブログ名
	 *   weblogurl    ブログURL
	 *   changeurl    更新されるURL（通常はweblogurlと同じ）
	 *   categoryname フィードのURL ("categoryname"なのにURLを指定するのは、仕様原文ママ)
	 * @static
	 * @link http://www.xmlrpc.com/weblogsCom 仕様...だが情報量少なすぎて参考にならず
	 * @link http://tech.ppmz.com/2006/08/phpweblogupdateping_2_ping.html 参考
	 * @Link http://isnot.jp/?p=XML-RPC%A1%F8%B9%B9%BF%B7Ping%A4%CE%C1%F7%BF%AE 参考
	 */
	static public function sendPings (BSParameterHolder $params) {
		$config = BSConfigManager::getInstance()->compile('blog');
		if (!isset($config['ping']['urls'])) {
			throw new BSBlogException('更新Pingの送信先を取得できません。');
		}
		$urls = new BSArray($config['ping']['urls']);

		$request = new BSBlogUpdatePingRequest;
		foreach (['weblogname', 'weblogurl'] as $field) {
			if (BSString::isBlank($value = $params[$field])) {
				$message = new BSStringFormat('更新Pingの%sパラメータが空欄です。');
				$message[] = $field;
				throw new BSBlogException($message);
			}
			$request->registerParameter($value);
		}

		foreach ($urls as $url) {
			try {
				$url = BSURL::create($url);
				$server = new self($url['host']);
				$server->sendPing($url->getFullPath(), $request);
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
