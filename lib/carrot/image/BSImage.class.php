<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * GD画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSImage implements BSImageRenderer {
	private $colors = array();
	private $type;
	private $image;
	private $height;
	private $width;
	private $antialias = false;
	private $fontname;
	private $fontsize;
	protected $error;
	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;
	const DEFAULT_FONT = 'VL-PGothic-Regular';
	const DEFAULT_FONT_SIZE = 9;

	/**
	 * コンストラクタ
	 *
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
		$this->setFontName(self::DEFAULT_FONT);
		$this->setFontSize(self::DEFAULT_FONT_SIZE);
		$this->setColor('black', 0, 0, 0);
		$this->setColor('white', 255, 255, 255);
		$this->fill($this->getCoordinate(0, 0), 'white');
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
	 * GDイメージリソースを設定する
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
		if (!in_array($type, self::getTypes())) {
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
	 * サムネイルのサイズ情報を返す
	 *
	 * @access public
	 * @param integer $pixel サイズ上限
	 * @return integer[] サムネイルのサイズ情報
	 */
	public function getThumbnailSize ($pixel) {
		if (($pixel < $this->getWidth()) || ($pixel < $this->getHeight())) {
			return array('width' => $pixel, 'height' => $pixel);
		} else {
			return array('width' => $this->getWidth(), 'height' => $this->getHeight());
		}
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param integer $pixel サイズ上限
	 * @return BSImage サムネイル
	 */
	public function getThumbnail ($pixel) {
		$info = $this->getThumbnailSize($pixel);
		$image = clone $this;
		$image->resize($info['width'], $info['height']);
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
	 * アンチエイリアス状態を設定する
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
	 * 色を返す
	 *
	 * @access public
	 * @param string $key 色の名前
	 * @return integer 色番号
	 */
	public function getColor ($key) {
		return $this->colors[$key];
	}

	/**
	 * 色を登録する
	 *
	 * @access public
	 * @param string $key 色の名前
	 * @param integer $red 赤
	 * @param integer $green 緑
	 * @param integer $blue 青
	 * @param integer $alpha 透明度
	 */
	public function setColor ($key, $red, $green, $blue, $alpha = 0) {
		if ($alpha) {
			$this->colors[$key] = imagecolorallocatealpha(
				$this->getImage(),
				$red, $green, $blue, $alpha
			);
		} else {
			$this->colors[$key] = imagecolorallocate(
				$this->getImage(),
				$red, $green, $blue
			);
		}
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
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		ob_start();
		switch ($this->getType()) {
			case 'image/jpeg':
				imagejpeg($this->getImage());
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
	 * @param string $color 色の名前
	 */
	public function fill (BSCoordinate $coord, $color) {
		imagefill(
			$this->getImage(),
			$coord->getX(),
			$coord->getY(),
			$this->getColor($color)
		);
	}

	/**
	 * 文字を書く
	 *
	 * @access public
	 * @param string 文字
	 * @param BSCoordinate $coord 最初の文字の左下の座標
	 * @param string $color 色の名前
	 */
	public function drawText ($text, BSCoordinate $coord, $color = 'black') {
		$dir = BSController::getInstance()->getDirectory('font');
		if (!$fontfile = $dir->getEntry($this->getFontName())) {
			throw new BSImageException('フォントファイルが見つかりません。');
		} else if (!$fontfile->isReadable()) {
			throw new BSImageException('フォント"%s"が読めません。', $this->getFontName());
		}

		imagettftext(
			$this->getImage(),
			$this->getFontSize(),
			0, //角度
			$coord->getX(), $coord->getY(),
			$this->getColor($color),
			$fontfile->getPath(),
			$text
		);
	}

	/**
	 * フォント名を返す
	 *
	 * @access public
	 * @return string フォント名
	 */
	function getFontName () {
		return $this->fontname;
	}

	/**
	 * フォント名を設定する
	 *
	 * @access public
	 * @param integer $fontname フォント名
	 */
	function setFontName ($fontname) {
		if (!BSController::getInstance()->getDirectory('font')->getEntry($fontname)) {
			throw new BSImageException('フォント名"%s"が正しくありません。', $fontname);
		}
		$this->fontname = $fontname;
	}

	/**
	 * フォントサイズを返す
	 *
	 * @access public
	 * @return integer フォントサイズ
	 */
	function getFontSize () {
		return $this->fontsize;
	}

	/**
	 * フォントサイズを設定する
	 *
	 * @access public
	 * @param integer $size フォントサイズ
	 */
	function setFontSize ($size) {
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
		if ($this->getAspect() < $dest->getAspect()) {
			$width = $dest->getHeight() * $this->getAspect();
			$x = BSNumeric::round(($dest->getWidth() - $width) / 2);
			$coord = $dest->getCoordinate($x, 0);
		} else {
			$height = $dest->getWidth() / $this->getAspect();
			$y = BSNumeric::round(($dest->getHeight() - $height) / 2);
			$coord = $dest->getCoordinate(0, $y);
		}

		$coordSrc = $this->getCoordinate(0, 0);
		imagecopyresampled(
			$dest->getImage(), //コピー先
			$this->getImage(), //コピー元
			$coord->getX(), $coord->getY(),
			$coordSrc->getX(), $coordSrc->getY(),
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
	 * @return string[] メディアタイプ
	 */
	public static function getTypes () {
		$types = array();
		foreach (array('.gif', '.jpg', '.png') as $suffix) {
			$types[$suffix] = BSTypeList::getType($suffix);
		}
		return $types;
	}
}

/* vim:set tabstop=4 ai: */
?>