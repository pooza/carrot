<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * GD画像
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
	private $fontname;
	private $fontsize;
	protected $error;
	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;
	const DEFAULT_FONT = 'wlmaru2004p';
	const DEFAULT_FONT_SIZE = 9;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function __construct ($width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT) {
		$this->width = ceil($width);
		$this->height = ceil($height);
		$this->setType('image/gif');
		$this->setImage(imagecreatetruecolor($this->getWidth(), $this->getHeight()));
		$this->setFontName(self::DEFAULT_FONT);
		$this->setFontSize(self::DEFAULT_FONT_SIZE);
		$this->setColor('black', 0, 0, 0);
		$this->setColor('white', 255, 255, 255);
		imagefill($this->getImage(), 0, 0, $this->getColor('white'));
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
		if (!in_array($type, array('image/jpeg', 'image/gif', 'image/png'))) {
			throw new BSImageException('メディアタイプ"%s"が正しくありません。', $type);
		}
		$this->type = $type;
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
	 * 文字を書く
	 *
	 * @access public
	 * @param string 文字
	 * @param integer $x 最初の文字の左下のX座標
	 * @param integer $y 最初の文字の左下のY座標
	 * @param string $color 色の名前
	 */
	public function drawText ($text, $x, $y, $color = 'black') {
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
			$x, $y,
			$this->getColor($color),
			$fontfile->getPath(),
			BSString::convertEncoding($text, 'SJIS')
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
		$image = new BSImage($width, $height);
		$aspectSrc = $this->getWidth() / $this->getHeight();
		$aspectDst = $image->getWidth() / $image->getHeight();

		if ($aspectSrc < $aspectDst) {
			$widthDst = $image->getHeight() * $aspectSrc;
			$heightDst = $image->getHeight();
			$x = floor(($image->getWidth() - $widthDst) / 2);
			$y = 0;
		} else {
			$widthDst = $image->getWidth();
			$heightDst = $image->getWidth() / $aspectSrc;
			$x = 0;
			$y = floor(($image->getHeight() - $heightDst) / 2);
		}

		$resource = $image->getImage();
		ImageCopyResampled(
			$resource, //コピー先
			$this->getImage(), //コピー元
			$x, $y, //コピー先始点座標
			0, 0, //コピー元始点座標
			$widthDst, $heightDst, //コピー先サイズ
			$this->getWidth(), $this->getHeight() //コピー元サイズ
		);
		$this->setImage($resource);
		$this->width = $image->getWidth();
		$this->height = $image->getHeight();
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
}

/* vim:set tabstop=4 ai: */
?>