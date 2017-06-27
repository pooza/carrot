<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service
 */

/**
 * piconクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPiconService extends BSCurlHTTP {
	const DEFAULT_HOST = 'localhost';
	const DEFAULT_PORT = 3000;

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		if (!$port) {
			$port = self::DEFAULT_PORT;
		}
		parent::__construct($host, $port);
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param BSImageContainer $image 対象画像
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize (BSImageRenderer $image, $width, $height) {
		$params = new BSWWWFormRenderer;
		$params['width'] = $width;
		$params['height'] = $height;
		$params['background_color'] = BS_IMAGE_THUMBNAIL_BGCOLOR;
		try {
			$response = $this->sendPOST('/resize', $params, $this->createFile($image));
			$image->setImage($response->getRenderer()->getContents());
		} catch (Exception $e) {
			//何もしない。
		}
	}

	/**
	 * 幅変更
	 *
	 * @access public
	 * @param BSImageContainer $image 対象画像
	 * @param integer $width 幅
	 * @param string $method リサイズ関数
	 */
	public function resizeWidth (BSImageRenderer $image, $width, $method = 'thumbnail') {
		$params = new BSWWWFormRenderer;
		$params['width'] = $width;
		$params['method'] = $method;
		try {
			$response = $this->sendPOST('/resize_width', $params, $this->createFile($image));
			$image->setImage($response->getRenderer()->getContents());
		} catch (Exception $e) {
			//何もしない。
		}
	}

	/**
	 * アップロードすべきファイルを生成して返す
	 *
	 * @access protected
	 * @return BSImageFile
	 */
	protected function createFile (BSImageRenderer $image) {
		if ($image instanceof BSImageFile) {
			return clone $image;
		} else {
			$file = BSFileUtility::createTemporaryFile(
				BSMIMEType::getSuffix($image->getType())
			);
			$file->setContents($image->getContents());
			return new BSImageFile($file->getPath());
		}
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('picon "%s"', $this->getName());
	}
}

