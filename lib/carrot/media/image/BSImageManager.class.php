<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image
 */

/**
 * 画像マネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImageManager {
	use BSBasicObject;
	protected $useragent;
	protected $type;
	protected $flags = 0;
	protected $backgroundColor;
	protected $directory;
	const WIDTH_FIXED = 2;
	const HEIGHT_FIXED = 4;
	const WITHOUT_SQUARE = 8;
	const FORCE_GIF = 16;

	/**
	 * @access public
	 * @param mixed $flags フラグのビット列、又は配列
	 */
	public function __construct ($flags = null) {
		$this->directory = BSFileUtility::getDirectory('image_cache');
		$this->setFlags($flags);
		$this->setUserAgent($this->request->getUserAgent());
	}

	/**
	 * 保存可能か？
	 *
	 * @access public
	 * @return boolean 保存可能ならTrue
	 */
	public function isStorable () {
		return !!BS_IMAGE_STORABLE;
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
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
	 * 規定の最大幅を返す
	 *
	 * @access public
	 * @return integer 規定の最大幅
	 */
	public function getDefaultWidth () {
		return $this->getUserAgent()->getDisplayInfo()['width'];
	}

	/**
	 * 規定フラグを返す
	 *
	 * @access public
	 * @return integer フラグのビット列
	 */
	public function getFlags () {
		return $this->flags;
	}

	/**
	 * 規定のフラグを設定
	 *
	 * @access public
	 * @param mixed $flags フラグのビット列、又は配列
	 */
	public function setFlags ($flags) {
		if (BSString::isBlank($flags)) {
			return;
		} else if (is_numeric($flags)) {
			$this->setFlag($flags);
		} else {
			if (is_string($flags)) {
				$flags = BSString::explode(',', $flags);
			}
			foreach ($flags as $flag) {
				$this->setFlag($flag);
			}
		}
	}

	protected function setFlag ($flag) {
		if (!is_numeric($flag)) {
			$constants = new BSConstantHandler;
			$value = BSString::toUpper($flag);
			if (BSString::isBlank($flag = $constants['BSImageManager::' . $value])) {
				$message = new BSStringFormat('BSImageManager::%sが未定義です。');
				$message[] = $value;
				throw new BSImageException($message);
			}
		}
		$this->flags |= $flag;
	}

	/**
	 * 画像のタイプを返す
	 *
	 * @access public
	 * @return string タイプ
	 */
	public function getType () {
		return $this->getUserAgent()->getDefaultImageType();
	}

	/**
	 * サムネイルのURLを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @return BSURL URL
	 */
	public function createURL (BSImageContainer $record, $size, $pixel = null, $flags = 0) {
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}

		$flags |= $this->flags;
		$url = BSFileUtility::createURL('image_cache');
		$url['path'] .= $this->createEntryName($record, $size) . '/' . $file->getName();
		return $url;
	}

	/**
	 * サムネイルのURLを返す
	 *
	 * createURLのエイリアス
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 * @return BSURL URL
	 */
	final public function getURL (BSImageContainer $record, $size, $pixel = null, $flags = 0) {
		return $this->createURL($record, $size, $pixel, $flags);
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @return BSImage サムネイル
	 */
	public function getThumbnail (BSImageContainer $record, $size, $pixel, $flags = 0) {
		$flags |= $this->flags;
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}
		try {
			return $file->getRenderer();
		} catch (BSImageException $e) {
			$file->delete();
			BSLogManager::getInstance()->put($file . 'を削除しました。');
		}
	}

	/**
	 * サムネイルを設定する
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @param BSImage サムネイル
	 */
	public function setThumbnail (BSImageContainer $record, $size, $pixel, $contents, $flags = 0) {
		$flags |= $this->flags;
		$dir = $this->getEntryDirectory($record, $size);
		$name = $this->createFileName($record->getImageFile($size), $pixel, $flags);
		if ($flags & self::FORCE_GIF) {
			$dir->setDefaultSuffix('.gif');
		}
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$file = $dir->createEntry($name, 'BSImageFile');
		}
		$file->setRenderer($this->convert($record, $pixel, $contents, $flags));
		$file->save();
		return $file->getRenderer();
	}

	/**
	 * サムネイルを削除する
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 */
	public function removeThumbnail (BSImageContainer $record, $size) {
		if ($dir = $this->getEntryDirectory($record, $size)) {
			$dir->delete();
		}
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_BWORSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo (BSImageContainer $record, $size, $pixel = null, $flags = 0) {
		$flags |= $this->flags;
		if (!$image = $this->getThumbnail($record, $size, $pixel, $flags)) {
			return;
		}

		$info = new BSArray;
		if ($url = $this->createURL($record, $size, $pixel, $flags)) {
			$info['url'] = $url->getContents();
		}
		$info['width'] = $image->getWidth();
		$info['height'] = $image->getHeight();
		$info['alt'] = $record->getLabel();
		$info['type'] = $image->getType();
		$info['pixel_size'] = $info['width'] . '×' . $info['height'];
		return $info;
	}

	/**
	 * サムネイルファイルを返す
	 *
	 * @access protected
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @return BSFile サムネイルファイル
	 */
	protected function getFile (BSImageContainer $record, $size, $pixel, $flags = 0) {
		if (!$source = $record->getImageFile($size)) {
			return null;
		}

		$flags |= $this->flags;
		$dir = $this->getEntryDirectory($record, $size);
		$name = $this->createFileName($record->getImageFile($size), $pixel, $flags);
		if ($flags & self::FORCE_GIF) {
			$name .= '.gif';
		}
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$this->setThumbnail($record, $size, $pixel, $source, $flags);
			$file = $dir->getEntry($name, 'BSImageFile');
		}
		return $file;
	}

	/**
	 * サムネイルファイルのファイル名を返す
	 *
	 * @access protected
	 * @param BSImageFile $file 対象ファイル
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @return BSFile サムネイルファイル
	 */
	protected function createFileName (BSImageFile $file, $pixel, $flags = 0) {
		$values = new BSArray([
			'id' => $file->getID(),
			'pixel' => $pixel,
		]);
		$flags |= $this->flags;
		if (!$pixel) {
			if ($width = $this->getDefaultWidth()) {
				$values['prefix'] = 'w';
				$values['pixel'] = $width;
			}
		} else if ($flags & self::WITHOUT_SQUARE) {
			$values['prefix'] = 's';
		} else if ($flags & self::WIDTH_FIXED) {
			$values['prefix'] = 'w';
		} else if ($flags & self::HEIGHT_FIXED) {
			$values['prefix'] = 'h';
		}
		return BSCrypt::digest($values);
	}

	/**
	 * 画像を変換して返す
	 *
	 * @access protected
	 * @param BSImageContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @param string $class レンダラーのクラス
	 * @return BSImage サムネイル
	 */
	protected function convert (BSImageContainer $record, $pixel, $contents, $flags = 0) {
		$class = BSImage::getDefaultRendererClass();
		$image = new $class;
		$image->setBackgroundColor($this->getBackgroundColor());
		$image->setImage($contents);
		$flags |= $this->flags;
		if ($flags & self::FORCE_GIF) {
			$image->setType(BSMIMEType::getType('gif'));
		} else {
			$image->setType($this->getType());
		}

		if ($pixel) {
			if ($flags & self::WITHOUT_SQUARE) {
				if ($image->getAspect() < 1) {
					$image->resizeHeight($pixel);
				} else {
					$image->resizeWidth($pixel);
				}
			} else if ($flags & self::WIDTH_FIXED) {
				$image->resizeWidth($pixel);
			} else if ($flags & self::HEIGHT_FIXED) {
				$image->resizeHeight($pixel);
			} else {
				$image->resizeSquare($pixel);
			}
		} else if ($width = $this->getDefaultWidth()) {
			$image->resizeWidth($width);
		}
		return $image;
	}

	/**
	 * サムネイル名を生成して返す
	 *
	 * @access protected
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	protected function createEntryName (BSImageContainer $record, $size) {
		return BSCrypt::digest([
			get_class($record),
			$record->getID(),
			$size,
		]);
	}

	/**
	 * サムネイルエントリーの格納ディレクトリを返す
	 *
	 * @access protected
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	protected function getEntryDirectory (BSImageContainer $record, $size) {
		$name = $this->createEntryName($record, $size);
		if (!$dir = $this->directory->getEntry($name)) {
			$dir = $this->directory->createDirectory($name);
		}
		$suffixes = BSImage::getSuffixes();
		$dir->setDefaultSuffix($suffixes[$this->getType()]);
		return $dir;
	}

	/**
	 * 画像情報から、HTMLのimg要素を返す
	 *
	 * @access public
	 * @param BSArray $info getImageInfoで取得した画像情報
	 * @return BSXMLElement img要素
	 */
	public function createElement (BSArray $info) {
		$element = new BSImageElement(null, $this->getUserAgent());
		$element->setAttributes($info);
		return $element;
	}

	/**
	 * パラメータ配列から画像コンテナを返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSImageContainer 画像コンテナ
	 */
	public function getContainer (BSParameterHolder $params) {
		$params = BSArray::create($params);
		if (!BSString::isBlank($path = $params['src'])) {
			$finder = new BSFileFinder;
			if ($dir = $params['dir']) {
				$finder->registerDirectory($dir);
			}
			$file = $finder->execute($path);
			if ($file->getMainType() == 'image') {
				return new BSImageFile($file->getPath());
			} else if ($file->getMainType() == 'video') {
				return new BSMovieFile($file->getPath());
			}
		}

		$finder = new BSRecordFinder($params);
		if (!($container = $finder->execute()) && ($class = $params['class'])) {
			if (is_subclass_of($class, 'BSRecord')) {
				$container = BSTableHandler::create($class)->getRecord($params['id']);
			} else {
				$container = new $class($params['id']);
			}
		}
		if ($container && ($container instanceof BSImageContainer)) {
			return $container;
		}
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 */
	public function clear () {
		$this->directory->clear();
	}
}

