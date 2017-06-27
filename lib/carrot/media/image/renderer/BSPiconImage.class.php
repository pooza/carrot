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
	protected $service;
	protected $method;

	/**
	 * リサイズ関数を設定
	 *
	 * @access public
	 * @param string $function 関数名
	 */
	public function setResizeMethod ($method) {
		$this->method = $method;
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$this->getService()->resize($this, $width, $height);
	}

	/**
	 * 幅変更
	 *
	 * @access public
	 * @param integer $width 幅
	 */
	public function resizeWidth ($width) {
		if ($this->getWidth() < $width) {
			return;
		}
		$this->getService()->resizeWidth($this, $width, $this->method);
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

	/**
	 * piconサービスを返す
	 *
	 * @access protected
	 * @return BSPiconService
	 */
	protected function getService () {
		if (!$this->service && $this->url) {
			$this->service = new BSPiconService($this->url['host'], $this->url['port']);
		}
		return $this->service;
	}
}

