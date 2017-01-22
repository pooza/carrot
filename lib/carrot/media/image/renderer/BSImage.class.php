<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.renderer
 */

/**
 * GD画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImage implements BSImageRenderer {
	protected $type;
	protected $gd;
	protected $imagick;
	protected $height;
	protected $width;
	protected $origin;
	protected $backgroundColor;
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
		$this->setType(BSMIMEType::getType('gif'));
		$this->setImage(imagecreatetruecolor($this->getWidth(), $this->getHeight()));
		$this->fill($this->getOrigin(), $this->getBackgroundColor());
	}

	/**
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle () {
		return $this->gd;
	}

	/**
	 * GD画像リソースを設定
	 *
	 * @access public
	 * @param mixed $image GD画像リソース等
	 */
	public function setImage ($image) {
		if (is_resource($image)) {
			$this->gd = $image;
		} else if ($image instanceof BSImageRenderer) {
			$this->gd = $image->getGDHandle();
		} else if ($image instanceof BSImageFile) {
			$this->gd = $image->getEngine()->getGDHandle();
		} else if ($image = imagecreatefromstring($image)) {
			$this->gd = $image;
		} else {
			throw new BSImageException('GD画像リソースが正しくありません。');
		}
		$this->width = imagesx($this->gd);
		$this->height = imagesy($this->gd);
	}

	/**
	 * Imagickオブジェクトを返す
	 *
	 * @access public
	 * @return Imagick
	 */
	public function getImagick () {
		if (!$this->imagick) {
			if (!extension_loaded('imagick')) {
				throw new BSCryptException('imagickモジュールがロードされていません。');
			}

			$file = BSFileUtility::createTemporaryFile();
			$file->setContents($this->getContents());
			$this->imagick = new Imagick($file->getPath());
			$file->delete();
		}
		return $this->imagick;
	}

	/**
	 * 背景色を返す
	 *
	 * @access public
	 * @return BSColor 背景色
	 */
	public function getBackgroundColor () {
		if (!$this->backgroundColor) {
			$this->backgroundColor = new BSColor(BS_IMAGE_THUMBNAIL_BGCOLOR);
		}
		return $this->backgroundColor;
	}

	/**
	 * 背景色を設定
	 *
	 * @access public
	 * @param BSColor $color 背景色
	 */
	public function setBackgroundColor (BSColor $color) {
		$this->backgroundColor = $color;
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
	 * @param string $type メディアタイプ又は拡張子
	 */
	public function setType ($type) {
		if (!BSString::isBlank($suggested = BSMIMEType::getType($type, null))) {
			$type = $suggested;
		}
		if (!self::getTypes()->isContain($type)) {
			$message = new BSStringFormat('メディアタイプ"%s"が正しくありません。');
			$message[] = $type;
			throw new BSImageException($message);
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
	 * 色IDを生成して返す
	 *
	 * @access protected
	 * @param BSColor $color 色
	 * @return integer 色ID
	 */
	protected function getColorID (BSColor $color) {
		return imagecolorallocatealpha(
			$this->getGDHandle(),
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
				imageinterlace($this->getGDHandle(), 1);
				imagejpeg($this->getGDHandle(), null, 100);
				break;
			case 'image/gif':
				imagegif($this->getGDHandle());
				break;
			case 'image/png':
				imagepng($this->getGDHandle());
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
			$this->getGDHandle(),
			$coord->getX(),
			$coord->getY(),
			$this->getColorID($color)
		);
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
		$polygon = [];
		foreach ($coords as $coord) {
			$polygon[] = $coord->getX();
			$polygon[] = $coord->getY();
		}

		if ($flags & self::FILLED) {
			$function = 'imagefilledpolygon';
		} else {
			$function = 'imagepolygon';
		}
		$function($this->getGDHandle(), $polygon, $coords->count(), $this->getColorID($color));
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
			$this->getGDHandle(),
			$start->getX(), $start->getY(),
			$end->getX(), $end->getY(),
			$this->getColorID($color)
		);
	}

	/**
	 * 重ね合わせ
	 *
	 * @access public
	 * @param BSImage $image 重ねる画像
	 * @param BSCoordinate $coord 貼り付け先の起点座標
	 */
	public function overlay (BSImage $image, BSCoordinate $coord = null) {
		if (!$coord) {
			$coord = $this->getOrigin();
		}
		imagecopy(
			$this->getGDHandle(),
			$image->getGDHandle(),
			$coord->getX(), $coord->getY(),
			0, 0,
			$image->getWidth(), $image->getHeight()
		);
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		foreach (['imagick', 'gd'] as $name) {
			if (extension_loaded($name)) {
				$class = BSLoader::getInstance()->getClass($name, 'ImageResizer');
				$resizer = new $class($this);
				$resizer->setBackgroundColor($this->getBackgroundColor());
				$this->setImage($resizer->execute($width, $height));
				return;
			}
		}
		throw new BSImageException('画像リサイズ機能を利用できません。');
	}

	/**
	 * 幅変更
	 *
	 * @access public
	 * @param integer $width 幅
	 */
	public function resizeWidth ($width) {
		if ($this->getWidth() < $width) {
			return;
		}
		$height = BSNumeric::round($this->getHeight() * ($width / $this->getWidth()));
		$this->resize($width, $height);
	}

	/**
	 * 高さ変更
	 *
	 * @access public
	 * @param integer $height 高さ
	 */
	public function resizeHeight ($height) {
		if ($this->getHeight() < $height) {
			return;
		}
		$width = BSNumeric::round($this->getWidth() * ($height / $this->getHeight()));
		$this->resize($width, $height);
	}

	/**
	 * 長辺を変更
	 *
	 * @access public
	 * @param integer $pixel 長辺
	 */
	public function resizeSquare ($pixel) {
		if (($this->getWidth() < $pixel) && ($this->getHeight() < $pixel)) {
			return;
		}
		$this->resize($pixel, $pixel);
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!is_resource($this->getGDHandle())) {
			$this->error = 'GD画像リソースが正しくありません。';
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
		foreach (['.gif', '.jpg', '.png'] as $suffix) {
			$types[$suffix] = BSMIMEType::getType($suffix);
		}
		if (extension_loaded('imagick')) {
			foreach (['.tiff', '.tif', '.eps', '.ico', '.pdf'] as $suffix) {
				$types[$suffix] = BSMIMEType::getType($suffix);
			}
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
		return self::getTypes()->createFlipped();
	}
}

/* vim:set tabstop=4: */
