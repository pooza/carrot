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
	static private $instance;
	const WITHOUT_BROWSER_CACHE = 1;

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
	 * サムネイルのURLを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags オプションのビット列
	 *   self::WITHOUT_BROWSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 * @return BSURL URL
	 */
	public function getURL (BSImageContainer $record, $size, $pixel = null, $flags = null) {
		$url = new BSURL;
		$url['path'] = sprintf(
			'/carrotlib/images/cache/%s/%s',
			$this->getEntryName($record, $size),
			$this->getFile($record, $size, $pixel)->getName()
		);
		if ($flags & self::WITHOUT_BROWSER_CACHE) {
			$url->setParameter('at', BSNumeric::getRandom());
		}
		return $url;
	}

	/**
	 * サムネイルラッパーのURLを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags オプションのビット列
	 *   self::WITHOUT_BROWSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 * @return BSURL URL
	 */
	public function getWrapperURL (BSImageContainer $record, $size, $pixel = null, $flags = null) {
		$url = new BSCarrotURL;
		$url->setModuleName('User' . get_class($record));
		$url->setActionName('Image');
		$url->setRecordID($record);
		if ($pixel) {
			$url->setParameter('pixel', $pixel);
		}
		if ($flags & self::WITHOUT_BROWSER_CACHE) {
			$url->setParameter('at', BSNumeric::getRandom());
		}

		$useragent = BSRequest::getInstance()->getUserAgent();
		if ($useragent->isMobile()) {
			$url->setParameters($useragent->getAttribute('query'));
		}
		return $url;
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags オプションのビット列
	 *   self::WITHOUT_BWORSER_CACHE クエリー末尾に乱数を加え、ブラウザキャッシュを無効にする
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo (BSImageContainer $record, $size, $pixel = null, $flags = null) {
		if (!$file = $record->getImageFile($size)) {
			return;
		}

		$image = $this->getThumbnail($record, $size, $pixel);
		$info = new BSArray;
		$info['is_cache'] = 1;
		$info['url'] = $this->getURL($record, $size, $pixel, $flags)->getContents();
		$info['wrapper_url'] = $this->getWrapperURL($record, $size, $pixel)->getContents();
		$info['width'] = $image->getWidth();
		$info['height'] = $image->getHeight();
		$info['alt'] = $record->getLabel();
		$info['type'] = $image->getType();
		return $info;
	}

	/**
	 * サムネイルファイルを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param string $class クラス名
	 * @return BSFile サムネイルファイル
	 */
	public function getFile (BSImageContainer $record, $size, $pixel, $class = 'BSImageFile') {
		if (!$record->getImageFile($size)) {
			return null;
		}

		$dir = $this->getEntryDirectory($record, $size);
		$name = sprintf('%04d', $pixel);
		if (!$file = $dir->getEntry($name, $class)) {
			$this->setThumbnail($record, $size, $pixel, $record->getImageFile($size));
			$file = $dir->getEntry($name, $class);
		}
		return $file;
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @return BSImage サムネイル
	 */
	public function getThumbnail (BSImageContainer $record, $size, $pixel) {
		if (!$record->getImageFile($size)) {
			return null;
		}
		return $this->getFile($record, $size, $pixel)->getEngine();
	}

	/**
	 * サムネイルを設定する
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param BSImage サムネイル
	 */
	public function setThumbnail (BSImageContainer $record, $size, $pixel, $contents) {
		$dir = $this->getEntryDirectory($record, $size);
		$image = new BSImage;
		$image->setImage($contents);
		$image->setType(BS_IMAGE_THUMBNAIL_TYPE);
		if ($pixel && ($pixel < $image->getWidth() || $pixel < $image->getHeight())) {
			$image = $image->getThumbnail($pixel);
		}

		$name = sprintf('%04d', $pixel);
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$file = $dir->createEntry($name, 'BSImageFile');
			$file->setMode(0666);
		}
		$file->setEngine($image);
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
	 * ディレクトリを返す
	 *
	 * @access private
	 * @param BSDirectory ディレクトリ
	 */
	private function getDirectory () {
		return BSController::getInstance()->getDirectory('image_cache');
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
		return sprintf('%s_%06d_%s', get_class($record), $record->getID(), $size);
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
		$dir->setDefaultSuffix(BSImage::getSuffixes()->getParameter(BS_IMAGE_THUMBNAIL_TYPE));
		return $dir;
	}
}

/* vim:set tabstop=4: */
