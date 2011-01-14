<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

/**
 * QRコードレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSQRCode implements BSImageRenderer {
	private $gd;
	private $data;
	private $error;
	private $type = BS_IMAGE_QRCODE_TYPE;

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
		$this->gd = null;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ
	 */
	public function setType ($type) {
		$this->type = $type;
	}

	/**
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle () {
		if (!is_resource($this->gd) && !BSString::isBlank($data = $this->getData())) {
			$validator = new BSURLValidator;
			if ($validator->execute($data)) {
				$file = BSURL::getInstance($data)->getImageFile('qr');
				$this->gd = $file->getGDHandle();
				$this->setType($file->getType());
			} else {
				BSUtility::includeFile('qrcode');
				$engine = new QRCode;
				$qr = $engine->getMinimumQRCode(
					BSString::convertEncoding($data, 'sjis-win'),
					QR_ERROR_CORRECT_LEVEL_L
				);
				$this->gd = $qr->createImage(BS_IMAGE_QRCODE_SIZE, BS_IMAGE_QRCODE_MARGIN);
			}
		}
		return $this->gd;
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return imagesx($this->getGDHandle());
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return imagesy($this->getGDHandle());
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		$image = new BSImage;
		$image->setType($this->getType());
		$image->setImage($this->getGDHandle());
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
