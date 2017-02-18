<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.renderer
 */

/**
 * ImageMagick画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImagickImage extends BSImage {
	protected $imagick;

	/**
	 * @access public
	 */
	public function __construct () {
		if (!extension_loaded('imagick')) {
			throw new BSImageException('imagickモジュールがロードされていません。');
		}
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
				self::DEFAULT_WIDTH,
				self::DEFAULT_HEIGHT,
				$this->getBackgroundColor()->getContents()
			);
			$this->setType(BS_IMAGE_THUMBNAIL_TYPE);
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
	}

	/**
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle () {
		$image = new BSImage;
		$image->setType($this->getType());
		$image->setImage($this->getContents());
		return $image->getGDHandle();
	}

	/**
	 * GD画像リソースを設定
	 *
	 * @access public
	 * @param mixed $image GD画像リソース等
	 */
	public function setImage ($image) {
		$renderer = null;
		if ($image instanceof BSImageRenderer) {
			$renderer = $image;
		} else if ($image instanceof BSImageFile) {
			$renderer = $image->getRenderer();
		}
		if ($renderer && ($renderer instanceof self)) {
			$this->setImagick($renderer->getImagick());
			return;
		}
		return parent::setImage($image);
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
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return $this->getImagick()->getImageWidth();
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return $this->getImagick()->getImageHeight();
	}

	/**
	 * 塗る
	 *
	 * @access public
	 * @param BSColor $color 塗る色
	 */
	public function fill (BSColor $color) {
		$this->getImagick()->floodFillPaintImage(
			$color->getContents(),
			0,
			$this->getImagick()->getImagePixelColor(
				$this->getOrigin()->getX(),
				$this->getOrigin()->getY()
			),
			$this->getOrigin()->getX(),
			$this->getOrigin()->getY(),
			false
		);
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		return (string)$this->getImagick();
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$dest = new BSImagickImage;
		$dest->setImagick(new Imagick);
		$dest->getImagick()->newImage(
			BSNumeric::round($width),
			BSNumeric::round($height),
			$this->getBackgroundColor()->getContents()
		);
		$dest->setType($this->getType());
		if ($this->getAspect() < $dest->getAspect()) {
			$width = ceil($dest->getHeight() * $this->getAspect());
			$x = BSNumeric::round(($dest->getWidth() - $width) / 2);
			$coord = $dest->getCoordinate($x, 0);
		} else {
			$height = ceil($dest->getWidth() / $this->getAspect());
			$y = BSNumeric::round(($dest->getHeight() - $height) / 2);
			$coord = $dest->getCoordinate(0, $y);
		}

		$resized = clone $this->getImagick();
		$resized->thumbnailImage(BSNumeric::round($width), BSNumeric::round($height), false);
		$dest->getImagick()->compositeImage(
			$resized,
			Imagick::COMPOSITE_DEFAULT,
			$coord->getX(), $coord->getY()
		);
		$this->setImagick($dest->getImagick());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (BSString::isBlank($this->getContents())) {
			$this->error = 'Imagick画像リソースが正しくありません。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
