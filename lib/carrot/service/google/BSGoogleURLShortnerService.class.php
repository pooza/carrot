<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google
 */

/**
 * Google URL Shortnerクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSGoogleURLShortnerService extends BSCurlHTTP implements BSURLShorter {
	const DEFAULT_HOST = 'www.googleapis.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
			$this->setSSL(true);
		}
		parent::__construct($host, $port);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPOST ($path = '/', BSParameterHolder $params = null) {
		$header = BSMIMEHeader::getInstance('Content-Type');
		$header->setContents('application/json');
		$this->setHeader($header);

		$renderer = new BSJSONRenderer;
		$renderer->setContents(new BSArray($params));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $renderer->getContents());
		return $this->execute($path);
	}

	/**
	 * 短縮URLを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @return BSHTTPURL 短縮URL
	 */
	public function getShortURL (BSHTTPRedirector $url) {
		$params = new BSArray(array(
			'longUrl' => $url->getURL()->getContents(),
		));
		$url = BSURL::getInstance();
		$url['host'] = $this->getHost();
		$url['path'] = '/urlshortener/v1/url';
		$url->setParameter('key', BS_SERVICE_GOOGLE_URL_SHORTENER_API_KEY);
		$response = $this->sendPOST($url->getFullPath(), $params);

		$json = new BSJSONSerializer;
		$result = $json->decode($response->getRenderer()->getContents());
		return BSURL::getInstance($result['id']);
	}

	/**
	 * QRコードの画像ファイルを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @return BSImageFile 画像ファイル
	 */
	public function getQRCodeImageFile (BSHTTPRedirector $url) {
		$dir = BSFileUtility::getDirectory('qrcode');
		$name = BSCrypt::getDigest($url->getContents());
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			try {
				$url = $this->getShortURL($url);
				$url['path'] .= '.qr';
				$image = new BSImage;
				$image->setType(BSMIMEType::getType('.png'));
				$image->setImage(file_get_contents($url->getContents()));
				$file = BSFileUtility::getTemporaryFile('.png', 'BSImageFile');
				$file->setRenderer($image);
				$file->save();
				$file->setMode(0666);
				$file->rename($name);
				$file->moveTo($dir);
			} catch (Exception $e) {
				return null;
			}
		}
		return $file;
	} 
}

/* vim:set tabstop=4: */
