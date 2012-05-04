<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

/**
 * ImageMagick画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			$this->imagick = new Imagick;
			$this->imagick->newImage(
				$this->width,
				$this->height,
				new ImagickPixel($this->getBackgroundColor()->getContents())
			);
			$this->imagick->setImageFormat(
				BSMIMEUtility::getSubType(BS_IMAGE_THUMBNAIL_TYPE)
			);
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
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle () {
		$header = BSMIMEHeader::create('Content-Type');
		$header->setContents(BS_IMAGE_THUMBNAIL_TYPE);

		$converted = clone $this->getImagick();
		$converted->setImageFormat($header['sub_type']);
		$image = new BSImage;
		$image->setType($header->getContents());
		$image->setImage((string)$converted);
		return $image->getGDHandle();
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		switch ($type = $this->getImagick()->getImageMimeType()) {
			case 'image/x-ico':
				return BSMIMEType::getType('ico');
		}
		return $type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ又は拡張子
	 */
	public function setType ($type) {
		if (BSString::isBlank($suffix = BSMIMEType::getSuffix($type))) {
			$message = new BSStringFormat('"%s"は正しくないMIMEタイプです。');
			$message[] = $type;
			throw new BSImageException($message);
		}
		$this->getImagick()->setImageFormat(ltrim($suffix, '.'));
		$this->type = $type;
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
		return (string)$this->imagick;
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
	 * 重ね合わせ
	 *
	 * @access public
	 * @param BSImage $image 重ねる画像
	 * @param BSCoordinate $coord 貼り付け先の起点座標
	 */
	public function overlay (BSImage $image, BSCoordinate $coord = null) {
		throw new BSImageException('BSImagickImage::overlayは未実装です。');
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
			$this->error = 'GD画像リソースが正しくありません。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
