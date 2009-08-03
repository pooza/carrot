<?php
/**
 * @package org.carrot-framework
 * @subpackage image.cache
 */

/**
 * 画像キャッシュ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImageCacheHandler {
	private $useragent;
	private $type;
	static private $instance;
	const WITHOUT_BROWSER_CACHE = 1;
	const WIDTH_FIXED = 2;
	const HEIGHT_FIXED = 4;
	const NO_RESIZE = 8;

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return ImageCacheHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			$this->useragent = BSRequest::getInstance()->getUserAgent();
		}
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
	 * 画像のタイプを返す
	 *
	 * @access public
	 * @return string タイプ
	 */
	public function getType () {
		if ($this->getUserAgent()->isMobile()) {
			return $this->getUserAgent()->getDefaultImageType();
		}
		return BS_IMAGE_THUMBNAIL_TYPE;
	}

	/**
	 * サムネイルのURLを返す
	 *
	 * @access public
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_BROWSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @return BSURL URL
	 */
	public function getURL (BSImageCacheContainer $record, $size, $pixel = null, $flags = null) {
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}

		$url = BSURL::getInstance();
		$url['path'] = sprintf(
			'/carrotlib/images/cache/%s/%s',
			$this->getEntryName($record, $size),
			$file->getName()
		);
		if ($flags & self::WITHOUT_BROWSER_CACHE) {
			$url->setParameter('at', BSNumeric::getRandom());
		}
		return $url;
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @return BSImage サムネイル
	 */
	public function getThumbnail (BSImageCacheContainer $record, $size, $pixel, $flags = null) {
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}
		try {
			return $file->getEngine();
		} catch (BSImageException $e) {
			$file->delete();
			BSController::getInstance()->putLog($file . 'を削除しました。');
		}
	}

	/**
	 * サムネイルを設定する
	 *
	 * @access public
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @param BSImage サムネイル
	 */
	public function setThumbnail (BSImageCacheContainer $record, $size, $pixel, $contents, $flags = null) {
		$dir = $this->getEntryDirectory($record, $size);
		$name = $this->getFileName($record, $pixel, $flags);
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$file = $dir->createEntry($name, 'BSImageFile');
			$file->setMode(0666);
		}
		$file->setEngine($this->convertImage($record, $pixel, $contents, $flags));
		$file->save();
		return $file->getEngine();
	}

	/**
	 * サムネイルを削除する
	 *
	 * @access public
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 */
	public function removeThumbnail (BSImageCacheContainer $record, $size) {
		if ($dir = $this->getEntryDirectory($record, $size)) {
			$dir->delete();
		}
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_BWORSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo (BSImageCacheContainer $record, $size, $pixel = null, $flags = null) {
		try {
			if (!$image = $this->getThumbnail($record, $size, $pixel, $flags)) {
				return null;
			}
			$info = new BSArray;
			$info['is_cache'] = 1;
			$info['url'] = $this->getURL($record, $size, $pixel, $flags)->getContents();
			$info['width'] = $image->getWidth();
			$info['height'] = $image->getHeight();
			$info['alt'] = $record->getLabel();
			$info['type'] = $image->getType();
			$info['pixel_size'] = $info['width'] . '×' . $string[] = $info['height'];
			return $info;
		} catch (BSImageException $e) {
		}
	}

	/**
	 * サムネイルファイルを返す
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @return BSFile サムネイルファイル
	 */
	private function getFile (BSImageCacheContainer $record, $size, $pixel, $flags = null) {
		if (!$source = $record->getImageFile($size)) {
			return null;
		}

		$dir = $this->getEntryDirectory($record, $size);
		$name = $this->getFileName($record, $pixel, $flags);
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$this->setThumbnail($record, $size, $pixel, $source, $flags);
			$file = $dir->getEntry($name, 'BSImageFile');
		}
		return $file;
	}

	/**
	 * ケータイ向けに全画面表示にすべきか
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::NO_RESIZE リサイズしない
	 * @return boolean 全画面表示にすべきならTrue
	 */
	private function isFullScreen (BSImageCacheContainer $record, $pixel, $flags = null) {
		return (($pixel == 0)
			&& !($flags & self::NO_RESIZE)
			&& $this->getUserAgent()
			&& $this->getUserAgent()->isMobile()
		);
	}

	/**
	 * サムネイルファイルのファイル名を返す
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @return BSFile サムネイルファイル
	 */
	private function getFileName (BSImageCacheContainer $record, $pixel, $flags = null) {
		$prefix = '';
		if ($this->isFullScreen($record, $pixel, $flags)) {
			$info = $this->getUserAgent()->getDisplayInfo();
			$pixel = $info['width'];
			$prefix = 'mw';
		} else if ($flags & self::WIDTH_FIXED) {
			$prefix = 'w';
		} else if ($flags & self::HEIGHT_FIXED) {
			$prefix = 'h';
		}
		return $prefix . sprintf('%04d', $pixel);
	}

	/**
	 * 画像を変換して返す
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 * @param BSImage サムネイル
	 */
	private function convertImage (BSImageCacheContainer $record, $pixel, $contents, $flags = null) {
		$image = new BSImage;
		$image->setImage($contents);
		$image->setType($this->getType());

		if ($this->isFullScreen($record, $pixel, $flags)) {
			$image = $this->getUserAgent()->convertImage($image);
		} else if ($pixel) {
			if ($flags & self::WIDTH_FIXED) {
				if ($pixel <= $image->getWidth()) {
					$image->resize($pixel, 0);
				}
			} else if ($flags & self::HEIGHT_FIXED) {
				if ($pixel <= $image->getHeight()) {
					$image->resize(0, $pixel);
				}
			} else {
				if (($pixel <= $image->getWidth()) || ($pixel <= $image->getHeight())) {
					$image->resize($pixel, $pixel);
				}
			}
		}
		return $image;
	}

	/**
	 * サムネイル名を生成して返す
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	private function getEntryName (BSImageCacheContainer $record, $size) {
		$name = new BSStringFormat('%s_%06d_%s');
		$name[] = get_class($record);
		$name[] = $record->getID();
		$name[] = $size;
		$name = $name->getContents();

		if (!BS_DEBUG) {
			$name = BSCrypt::getSHA1($name . BS_CRYPT_SALT);
		}
		return $name;
	}

	/**
	 * サムネイルエントリーの格納ディレクトリを返す
	 *
	 * @access private
	 * @param BSImageCacheContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	private function getEntryDirectory (BSImageCacheContainer $record, $size) {
		$name = $this->getEntryName($record, $size);
		if (!$dir = $this->getDirectory()->getEntry($name)) {
			$dir = $this->getDirectory()->createDirectory($name);
			$dir->setMode(0777);
		}

		$suffixes = BSImage::getSuffixes();
		$dir->setDefaultSuffix($suffixes[$this->getType()]);
		return $dir;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access private
	 * @param BSDirectory ディレクトリ
	 */
	private function getDirectory () {
		return BSController::getInstance()->getDirectory('image_cache');
	}

	/**
	 * 画像情報から、HTMLのimg要素を返す
	 *
	 * @access public
	 * @param BSArray $info getImageInfoで取得した画像情報
	 * @return BSXMLElement img要素
	 */
	public function getImageElement (BSArray $info) {
		$element = new BSXMLElement('img');
		$element->setAttribute('src', $info['url']);
		$element->setAttribute('width', $info['width']);
		$element->setAttribute('height', $info['height']);
		if (!$this->getUserAgent()->isMobile()) {
			$element->setAttribute('alt', $info['alt']);
		}
		return $element;
	}

	/**
	 * パラメータ配列から画像コンテナを返す
	 *
	 * @access private
	 * @param BSArray $params パラメータ配列
	 * @return BSImageCacheContainer 画像キャッシュコンテナ
	 */
	public function getContainer (BSArray $params) {
		if (!BSString::isBlank($params['src'])) {
			foreach (array('images', 'www', 'root') as $name) {
				$dir = BSController::getInstance()->getDirectory($name);
				if ($entry = $dir->getEntry($params['src'], 'BSImageFile')) {
					return $entry;
				}
			}
		}

		if (BSString::isBlank($params['class'])) {
			$module = BSController::getInstance()->getModule();
			$params['class'] = $module->getRecordClassName();
			if (BSString::isBlank($params['id']) && ($record = $module->getRecord())) {
				$params['id'] = $record->getID();
			}
		}
		if (BSString::isBlank($params['size'])) {
			$params['size'] = 'thumbnail';
		}
		if ($table = BSTableHandler::getInstance($params['class'])) {
			return $table->getRecord($params['id']);
		}
	}

	/**
	 * 文字列、又は配列のフラグをビット列に変換
	 *
	 * @access private
	 * @param mixed $values カンマ区切り文字列、又は配列
	 * @return $flags フラグのビット列
	 *   self::WITHOUT_BWORSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::NO_RESIZE リサイズしない
	 */
	public function convertFlags ($values) {
		if (!BSArray::isArray($values)) {
			if (BSString::isBlank($values)) {
				return 0;
			}
			$values = BSString::explode(',', $values);
		}
		$values = BSString::toUpper($values);

		$constants = BSConstantHandler::getInstance();
		$flags = 0;
		foreach ($values as $value) {
			if (BSString::isBlank($flag = $constants['BSImageCacheHandler::' . $value])) {
				throw new BSImageException('BSImageCacheHandler::%sが未定義です。', $value);
			}
			$flags += $flag;
		}
		return $flags;
	}
}

/* vim:set tabstop=4: */
