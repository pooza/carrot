<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google
 */

/**
 * Google Chartクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleChartService extends BSCurlHTTP {
	const DEFAULT_HOST = 'chart.apis.google.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		parent::__construct($host, $port);
	}

	/**
	 * QRコードの画像ファイルを返す
	 *
	 * @access public
	 * @param string $data 対象データ
	 * @return BSImageFile 画像ファイル
	 */
	public function getQRCodeImageFile ($data, $size = 0, $encoding = 'sjis-win') {
		if (!$size) {
			$size = BS_IMAGE_QRCODE_SIZE;
		}
		$params = new BSArray(array(
			'chl' => BSString::convertEncoding($data, $encoding),
			'chld' => 'l|0',
		));
		return $this->getImageFile('qr', $size, $size, $params);
	} 

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $type 種類
	 * @param integer $witdh 幅
	 * @param integer $height 高さ
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($type, $width, $height, BSParameterHolder $params) {
		$key = $this->createKey($type, $width, $height, $params);
		$dir = BSFileUtility::getDirectory('chart');
		if (!$file = $dir->getEntry($key, 'BSImageFile')) {
			try {
				$url = $this->createRequestURL('/chart');
				$url->setParameter('cht', $type);
				$url->setParameter('chs', $width . 'x' . $height);
				$response = $this->sendGET($url->getFullPath());
	
				$image = new BSImage;
				$image->setType(BSMIMEType::getType('.png'));
				$image->setImage($response->getRenderer()->getContents());
				$file = BSFileUtility::getTemporaryFile('.png', 'BSImageFile');
				$file->setRenderer($image);
				$file->save();
				$file->setMode(0666);
				$file->rename($key);
				$file->moveTo($dir);
			} catch (Exception $e) {
			}
		}
		return $file;
	}

	/**
	 * 画像ファイル名を生成して返す
	 *
	 * @access private
	 * @param string $type 種類
	 * @param integer $witdh 幅
	 * @param integer $height 高さ
	 * @param BSParameterHolder $params パラメータ配列
	 * @return string 画像ファイル名
	 */
	private function createKey ($type, $width, $height, BSParameterHolder $params) {
		$values = new BSArray;
		$values['type'] = $type;
		$values['width'] = $width;
		$values['height'] = $height;
		$values['params'] = new BSArray($params->getParameters());
		$serializer = new BSPHPSerializer;
		return BSCrypt::getDigest(new BSArray(array(
			$serializer->encode($values->decode()),
		)));
	}
}

/* vim:set tabstop=4: */
