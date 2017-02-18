<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.renderer
 */

/**
 * GraphicsMagick画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGmagickImage extends BSImage {
	protected $gmagick;

	/**
	 * @access public
	 */
	public function __construct () {
		if (!extension_loaded('gmagick')) {
			throw new BSImageException('gmagickモジュールがロードされていません。');
		}
	}

	/**
	 * Gmagickオブジェクトを返す
	 *
	 * @access public
	 * @return Gmagick
	 */
	public function getGmagick () {
		if (!$this->gmagick) {
			$this->gmagick = new Gmagick;
			$this->gmagick->newImage(
				self::DEFAULT_WIDTH,
				self::DEFAULT_HEIGHT,
				$this->getBackgroundColor()->getContents()
			);
			$this->setType(BS_IMAGE_THUMBNAIL_TYPE);
		}
		return $this->gmagick;
	}

	/**
	 * Gmagickオブジェクトを設定
	 *
	 * @access public
	 * @param Gmagick $gmagick
	 */
	public function setGmagick (Gmagick $gmagick) {
		$this->gmagick = $gmagick;
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
			$this->setGmagick($renderer->getGmagick());
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
		return BSMIMEType::getType($this->getGmagick()->getImageFormat());
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
		$this->getGmagick()->setImageFormat(ltrim($suffix, '.'));
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return $this->getGmagick()->getImageWidth();
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return $this->getGmagick()->getImageHeight();
	}

	/**
	 * 塗る
	 *
	 * @access public
	 * @param BSColor $color 塗る色
	 */
	public function fill (BSColor $color) {
		throw new BSImageException(__CLASS__ . '::' . __METHOD__ . 'は未実装です。');
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		$file = BSFileUtility::createTemporaryFile(self::getSuffixes()[$this->getType()]);
		$this->getGmagick()->writeImage($file->getPath());
		ob_start();
		print $file->getContents();
		$contents = ob_get_contents();
		ob_end_clean();
		$file->delete();
		return $contents;
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$dest = new BSGmagickImage;
		$dest->setGmagick(new Gmagick);
		$dest->getGmagick()->newImage(
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

		$resized = clone $this->getGmagick();
		$resized->thumbnailImage(BSNumeric::round($width), BSNumeric::round($height), false);
		$dest->getGmagick()->compositeImage(
			$resized,
			Gmagick::COMPOSITE_DEFAULT,
			$coord->getX(), $coord->getY()
		);
		$this->setGmagick($dest->getGmagick());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (BSString::isBlank($this->getContents())) {
			$this->error = 'Gmagick画像リソースが正しくありません。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
