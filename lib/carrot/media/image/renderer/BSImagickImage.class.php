<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

/**
 * ImageMagick画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImagickImage extends BSImage {

	/**
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function __construct ($width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT) {
		if (!extension_loaded('imagick')) {
			throw new BSCryptException('imagickモジュールがロードされていません。');
		}
		$this->width = BSNumeric::round($width);
		$this->height = BSNumeric::round($height);
	}

	/**
	 * Imagickオブジェクトを返す
	 *
	 * @access public
	 * @return Imagick
	 */
	public function getImagick () {
		if (!$this->imagick) {
			$header = new BSContentTypeMIMEHeader;
			$header->setContents(BS_IMAGE_THUMBNAIL_TYPE);

			$this->imagick = new Imagick;
			$this->imagick->newImage(
				$this->width,
				$this->height,
				new ImagickPixel(BS_IMAGE_THUMBNAIL_BGCOLOR)
			);
			$this->imagick->setImageFormat($header['sub_type']);
		}
		return $this->imagick;
	}

	/**
	 * Imagickオブジェクトを設定
	 *
	 * @access public
	 * @param Imagick $imagick
	 */
	public function setImagick (Imagick $imagick) {
		$this->imagick = $imagick;
		$this->width = $this->imagick->getImageWidth();
		$this->height = $this->imagick->getImageHeight();
	}

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getGDHandle () {
		$header = new BSContentTypeMIMEHeader;
		$header->setContents(BS_IMAGE_THUMBNAIL_TYPE);

		$converted = clone $this->imagick;
		$converted->setImageFormat($header['sub_type']);
		$image = new BSImage;
		$image->setType($header->getContents());
		$image->setImage($converted->__toString());
		return $image->getGDHandle();
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->imagick->getImageMimeType();
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ又は拡張子
	 */
	public function setType ($type) {
		throw new BSImageException('BSImagickImage::setTypeは未実装です。');
	}

	/**
	 * 色IDを生成して返す
	 *
	 * @access protected
	 * @param BSColor $color 色
	 * @return integer 色ID
	 */
	protected function getColorID (BSColor $color) {
		throw new BSImageException('BSImagickImage::getColorImageは未実装です。');
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		return $this->imagick->__toString();
	}

	/**
	 * 塗りつぶす
	 *
	 * @access public
	 * @param BSCoordinate $coord 始点の座標
	 * @param BSColor $color 色
	 */
	public function fill (BSCoordinate $coord, BSColor $color) {
		throw new BSImageException('BSImagickImage::fillは未実装です。');
	}

	/**
	 * 文字を書く
	 *
	 * @access public
	 * @param string 文字
	 * @param BSCoordinate $coord 最初の文字の左下の座標
	 * @param BSColor $color 色
	 */
	public function drawText ($text, BSCoordinate $coord, BSColor $color = null) {
		throw new BSImageException('BSImagickImage::drawTextは未実装です。');
	}

	/**
	 * 多角形を描く
	 *
	 * @access public
	 * @param BSArray $coords 座標の配列
	 * @param BSColor $color 描画色
	 * @param integer $flags フラグのビット列
	 *   self::FILLED 塗りつぶす
	 */
	public function drawPolygon (BSArray $coords, BSColor $color, $flags = null) {
		throw new BSImageException('BSImagickImage::drawPolygonは未実装です。');
	}

	/**
	 * 線を引く
	 *
	 * @access public
	 * @param BSCoordinate $start 始点
	 * @param BSCoordinate $end 終点
	 * @param BSColor $color 描画色
	 */
	public function drawLine (BSCoordinate $start, BSCoordinate $end, BSColor $color) {
		throw new BSImageException('BSImagickImage::drawLineは未実装です。');
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$resizer = new BSImagickImageResizer($this);
		$renderer = $resizer->execute($width, $height);
		$this->setImage($renderer);
		$this->setImagick($renderer->getImagick());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (BSString::isBlank($this->getContents())) {
			$this->error = '画像リソースが正しくありません。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */