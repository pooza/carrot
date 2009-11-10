<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

BSUtility::includeFile('qrcode.php');

/**
 * QRコードレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSQRCode implements BSImageRenderer {
	private $image;
	private $data;
	private $error;
	private $engine;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->engine = new QRCode;
	}

	/**
	 * エンコード対象データを返す
	 *
	 * @access public
	 * @return string エンコード対象データ
	 */
	public function getData () {
		return $this->data;
	}

	/**
	 * エンコード対象データを設定
	 *
	 * @access public
	 * @param string $data エンコード対象データ
	 */
	public function setData ($data) {
		$this->data = $data;
		$this->image = null;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BS_IMAGE_QRCODE_TYPE;
	}

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage () {
		if (!$this->image && !BSString::isBlank($this->getData())) {
			$data = BSString::convertEncoding($this->getData(), 'sjis-win');
			$qr = $this->engine->getMinimumQRCode($data, QR_ERROR_CORRECT_LEVEL_L);
			$this->image = $qr->createImage(BS_IMAGE_QRCODE_SIZE, BS_IMAGE_QRCODE_MARGIN);
		}
		return $this->image;
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return imagesx($this->getImage());
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return imagesy($this->getImage());
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		$image = new BSImage;
		$image->setImage($this->getImage());
		return $image->getContents();
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (BSString::isBlank($this->getData())) {
			$this->error = 'データが未定義です。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}
}

/* vim:set tabstop=4: */
