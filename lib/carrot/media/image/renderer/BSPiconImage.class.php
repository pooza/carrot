<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.renderer
 */

/**
 * Picon画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPiconImage extends BSImage {
	protected $url;

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$service = new BSCurlHTTP($this->url['host'], $this->url['port']);
		$file = BSFileUtility::createTemporaryFile();
		$file->setContents($this->getContents());
		$params = BSArray::create([
			'path' => $file->getPath(),
			'width' => $width,
			'height' => $height,
			'background_color' => BS_IMAGE_THUMBNAIL_BGCOLOR,
		]);
		try {
			$response = $service->sendGET('/convert', $params);
			$this->setImage($response->getRenderer()->getContents());
		} catch (Exception $e) {
			//何もしない。
		}
		$file->delete();
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->url = $url->getURL();
	}
}

