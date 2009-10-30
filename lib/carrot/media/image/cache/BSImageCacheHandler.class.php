<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.cache
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
	const WITHOUT_SQUARE = 8;

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
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			$this->setUserAgent(BSRequest::getInstance()->getUserAgent());
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
	 *   self::WITHOUT_BROWSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @return BSURL URL
	 */
	public function getURL (BSImageContainer $record, $size, $pixel = null, $flags = null) {
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}

		if (BSUser::getInstance()->isAdministrator()) {
			$flags += self::WITHOUT_BROWSER_CACHE;
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
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @return BSImage サムネイル
	 */
	public function getThumbnail (BSImageContainer $record, $size, $pixel, $flags = null) {
		if (!$file = $this->getFile($record, $size, $pixel, $flags)) {
			return null;
		}
		try {
			return $file->getEngine();
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
	 * @param BSImage サムネイル
	 */
	public function setThumbnail (BSImageContainer $record, $size, $pixel, $contents, $flags = null) {
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
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo (BSImageContainer $record, $size, $pixel = null, $flags = null) {
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
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @return BSFile サムネイルファイル
	 */
	private function getFile (BSImageContainer $record, $size, $pixel, $flags = null) {
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
	 * サムネイルファイルのファイル名を返す
	 *
	 * @access private
	 * @param BSImageContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @return BSFile サムネイルファイル
	 */
	private function getFileName (BSImageContainer $record, $pixel, $flags = null) {
		$prefix = '';
		if (($useragent = $this->getUserAgent()) && $useragent->isMobile()) {
			$prefix = 'w';
		} else if ($flags & self::WITHOUT_SQUARE) {
			$prefix = 's';
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
	 * @param BSImageContainer $record 対象レコード
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 * @param BSImage サムネイル
	 */
	private function convertImage (BSImageContainer $record, $pixel, $contents, $flags = null) {
		$image = new BSImage;
		$image->setImage($contents);
		$image->setType($this->getType());

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
		} else if (($useragent = $this->getUserAgent()) && $useragent->isMobile()) {
			$info = $useragent->getDisplayInfo();
			$image->resizeWidth($info['width']);
		}
		return $image;
	}

	/**
	 * サムネイル名を生成して返す
	 *
	 * @access private
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	private function getEntryName (BSImageContainer $record, $size) {
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
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @return string サムネイル名
	 */
	private function getEntryDirectory (BSImageContainer $record, $size) {
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
		return BSFileUtility::getDirectory('image_cache');
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
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSImageContainer 画像コンテナ
	 */
	public function getContainer (BSParameterHolder $params) {
		if (!BSString::isBlank($params['src'])) {
			foreach (array('images', 'www', 'root') as $name) {
				$dir = BSFileUtility::getDirectory($name);
				if ($entry = $dir->getEntry($params['src'], 'BSImageFile')) {
					return $entry;
				}
			}
		}
		if (BSString::isBlank($params['size'])) {
			$params['size'] = 'thumbnail';
		}

		return BSController::getInstance()->getModule()->searchRecord($params);
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
	 *   self::WITHOUT_SQUARE 正方形に整形しない
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
