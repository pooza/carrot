<?php
/**
 * @package org.carrot-framework
 * @subpackage image
 */

/**
 * GD画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImage implements BSImageRenderer {
	private $type;
	private $image;
	private $height;
	private $width;
	private $origin;
	private $antialias = false;
	private $font;
	private $fontsize;
	protected $error;
	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;
	const FILLED = 1;

	/**
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function __construct ($width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT) {
		$this->width = BSNumeric::round($width);
		$this->height = BSNumeric::round($height);
		$this->setType('image/gif');
		$this->setImage(imagecreatetruecolor($this->getWidth(), $this->getHeight()));
		$this->setAntialias(false);
		$this->setFont(BSFontManager::getInstance()->getFont());
		$this->setFontSize(BSFontManager::DEFAULT_FONT_SIZE);
	}

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage () {
		return $this->image;
	}

	/**
	 * GDイメージリソースを設定
	 *
	 * @access public
	 * @param mixed $image 画像リソース
	 */
	public function setImage ($image) {
		if (is_resource($image)) {
			$this->image = $image;
		} else if ($image instanceof BSImageRenderer) {
			$this->image = $image->getImage();
		} else if ($image instanceof BSImageFile) {
			$this->image = $image->getEngine()->getImage();
		} else if ($image = imagecreatefromstring($image)) {
			$this->image = $image;
		} else {
			throw new BSImageException('GDイメージリソースが正しくありません。');
		}
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
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
		if (!self::getTypes()->isContain($type)) {
			throw new BSImageException('メディアタイプ"%s"が正しくありません。', $type);
		}
		$this->type = $type;
	}

	/**
	 * 縦横比を返す
	 *
	 * @access public
	 * @return float 縦横比
	 */
	public function getAspect () {
		return $this->getWidth() / $this->getHeight();
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return $this->width;
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return $this->height;
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param integer $pixel サイズ
	 * @return BSImage サムネイル
	 */
	public function getThumbnail ($pixel) {
		$image = clone $this;
		$image->resize($pixel, $pixel);
		return $image;
	}

	/**
	 * アンチエイリアス状態を返す
	 *
	 * @access public
	 * @return boolean アンチエイリアスの有無
	 */
	public function getAntialias () {
		return $this->antialias;
	}

	/**
	 * アンチエイリアス状態を設定
	 *
	 * @access public
	 * @param boolean $mode アンチエイリアスの有無
	 */
	public function setAntialias ($mode) {
		if (function_exists('imageantialias')) {
			imageantialias($this->getImage(), $mode);
		} else {
			$mode = false;
		}
		$this->antialias = $mode;
	}

	/**
	 * 色IDを生成して返す
	 *
	 * @access protected
	 * @param BSColor $color 色
	 * @return integer 色ID
	 */
	protected function getColorID (BSColor $color) {
		return imagecolorallocatealpha(
			$this->getImage(),
			$color['red'],
			$color['green'],
			$color['blue'],
			$color['alpha']
		);
	}

	/**
	 * 座標を生成して返す
	 *
	 * @access public
	 * @param integer $x X座標
	 * @param integer $y Y座標
	 * @return BSCoordinate 座標
	 */
	public function getCoordinate ($x, $y) {
		return new BSCoordinate($this, $x, $y);
	}

	/**
	 * 原点座標を返す
	 *
	 * @access public
	 * @return BSCoordinate 原点座標
	 */
	public function getOrigin () {
		if (!$this->origin) {
			$this->origin = $this->getCoordinate(0, 0);
		}
		return $this->origin;
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		ob_start();
		switch ($this->getType()) {
			case 'image/jpeg':
				imageinterlace($this->getImage(), 1);
				imagejpeg($this->getImage(), null, 100);
				break;
			case 'image/gif':
				imagegif($this->getImage());
				break;
			case 'image/png':
				imagepng($this->getImage());
				break;
		}
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
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
	 * 塗りつぶす
	 *
	 * @access public
	 * @param BSCoordinate $coord 始点の座標
	 * @param BSColor $color 色
	 */
	public function fill (BSCoordinate $coord, BSColor $color) {
		imagefill(
			$this->getImage(),
			$coord->getX(),
			$coord->getY(),
			$this->getColorID($color)
		);
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
		if (!$color) {
			$color = new BSColor('black');
		}
		imagettftext(
			$this->getImage(),
			$this->getFontSize(),
			0, //角度
			$coord->getX(), $coord->getY(),
			$this->getColorID($color),
			$this->getFont()->getFile()->getPath(),
			$text
		);
	}

	/**
	 * 多角形を描く
	 *
	 * @access public
	 * @param BSArray $coords 座標の配列
	 * @param BSColor $color 描画色
	 * @param integer $flag 各種フラグ、現状はself::FILLEDのみ。
	 */
	public function drawPolygon (BSArray $coords, BSColor $color, $flag = null) {
		$polygon = array();
		foreach ($coords as $coord) {
			$polygon[] = $coord->getX();
			$polygon[] = $coord->getY();
		}

		if ($flag & self::FILLED) {
			$function = 'imagefilledpolygon';
		} else {
			$function = 'imagepolygon';
		}
		$function($this->getImage(), $polygon, $coords->count(), $this->getColorID($color));
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
		imageline(
			$this->getImage(),
			$start->getX(), $start->getY(),
			$end->getX(), $end->getY(),
			$this->getColorID($color)
		);
	}

	/**
	 * フォントを返す
	 *
	 * @access public
	 * @return string フォント
	 */
	public function getFont () {
		if (!$this->font) {
			throw new BSImageException('フォントが未定義です。');
		}
		return $this->font;
	}

	/**
	 * フォントを設定
	 *
	 * @access public
	 * @param BSFont $font フォント
	 */
	public function setFont ($font) {
		$this->font = $font;
	}

	/**
	 * フォントサイズを返す
	 *
	 * @access public
	 * @return integer フォントサイズ
	 */
	public function getFontSize () {
		return $this->fontsize;
	}

	/**
	 * フォントサイズを設定
	 *
	 * @access public
	 * @param integer $size フォントサイズ
	 */
	public function setFontSize ($size) {
		$this->fontsize = $size;
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$dest = new BSImage($width, $height);
		$dest->fill($this->getCoordinate(0, 0), new BSColor(BS_IMAGE_THUMBNAIL_BGCOLOR));
		if ($this->getAspect() < $dest->getAspect()) {
			$width = $dest->getHeight() * $this->getAspect();
			$x = BSNumeric::round(($dest->getWidth() - $width) / 2);
			$coord = $dest->getCoordinate($x, 0);
		} else {
			$height = $dest->getWidth() / $this->getAspect();
			$y = BSNumeric::round(($dest->getHeight() - $height) / 2);
			$coord = $dest->getCoordinate(0, $y);
		}

		imagecopyresampled(
			$dest->getImage(), //コピー先
			$this->getImage(), //コピー元
			$coord->getX(), $coord->getY(),
			$this->getOrigin()->getX(), $this->getOrigin()->getY(),
			$width, $height, //コピー先サイズ
			$this->getWidth(), $this->getHeight() //コピー元サイズ
		);
		$this->setImage($dest->getImage());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!is_resource($this->getImage())) {
			$this->error = '画像リソースが正しくありません。';
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

	/**
	 * 利用可能なメディアタイプを返す
	 *
	 * @access public
	 * @return BSArray メディアタイプ
	 */
	static public function getTypes () {
		$types = new BSArray;
		foreach (array('.gif', '.jpg', '.png') as $suffix) {
			$types[$suffix] = BSMIMEType::getType($suffix);
		}
		return $types;
	}

	/**
	 * 利用可能な拡張子を返す
	 *
	 * @access public
	 * @return BSArray 拡張子
	 */
	static public function getSuffixes () {
		$suffixes = new BSArray;
		foreach (self::getTypes() as $suffix => $type) {
			$suffixes[$type] = $suffix;
		}
		return $suffixes;
	}

	/**
	 * メディアタイプに適切な拡張子を返す
	 *
	 * @access public
	 * $params string $type メディアタイプ
	 * @return string 拡張子
	 * @static
	 */
	static public function getSuffix ($type) {
		return self::getSuffixes()->getParameter($type);
	}
}

/* vim:set tabstop=4: */
