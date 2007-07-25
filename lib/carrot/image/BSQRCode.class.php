<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

define('QRCODE_DATA_PATH', BS_SHARE_DIR . '/qrcode');
BSController::includeLegacy('/qrcode/qrcode_img.php');

/**
 * QRCode_imageのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSQRCode extends QRCode_image implements BSImageViewEngine {
	private $type;
	private $image;
	private $data;
	private $error;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		$this->setType('image/gif');
		parent::QRCode_image();
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
		$this->image = $this->mkimage($this->cal_qrcode($this->getData()));
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
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage () {
		if (!$this->image) {
			throw new BSImageException('有効な画像リソースがありません。');
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
		ob_start();
		switch ($type = $this->getType()) {
			case 'image/png':
				imagepng($this->getImage());
				break;
			case 'image/jpeg':
				imagejpeg($this->getImage());
				break;
			case 'image/gif':
				imagegif($this->getImage());
				break;
			default:
				ob_end_clean();
				throw new BSImageException('メディアタイプ"%s"が正しくありません。', $type);
		}
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->getData()) {
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

/* vim:set tabstop=4 ai: */
?>