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
	 * ディレクトリを返す
	 *
	 * @access private
	 * @param BSDirectory ディレクトリ
	 */
	private function getDirectory () {
		return BSController::getInstance()->getDirectory('image_cache');
	}

	/**
	 * サムネイルファイルを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @param integer $pixel ピクセル数
	 * @param string $class クラス名
	 * @return BSFile サムネイルファイル
	 */
	public function getFile (BSImageContainer $record, $size, $pixel, $class = 'BSImageFile') {
		$name = $this->getEntryName($record, $size);
		if (!$dir = $this->getDirectory()->getEntry($name)) {
			return null;
		}
		foreach (BSImage::getSuffixes() as $suffix) {
			$filename = sprintf('%04d', $pixel) . $suffix;
			if ($file = $dir->getEntry($filename, $class)) {
				return $file;
			}
		}
	}

	/**
	 * サムネイルを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @param integer $pixel ピクセル数
	 * @return BSImage サムネイル
	 */
	public function getThumbnail (BSImageContainer $record, $size, $pixel) {
		if ($file = $this->getFile($record, $size, $pixel)) {
			return $file->getEngine();
		}
	}

	/**
	 * サムネイルを設定する
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @param integer $pixel ピクセル数
	 * @param mixed $contents サムネイルの内容
	 * @param BSImage サムネイル
	 */
	public function setThumbnail (BSImageContainer $record, $size, $pixel, $contents) {
		$name = $this->getEntryName($record, $size);
		if (!$dir = $this->getDirectory()->getEntry($name)) {
			$dir = $this->getDirectory()->createDirectory($name);
			$dir->setMode(0777);
		}

		$image = new BSImage;
		$image->setImage($contents);
		$image->setType(BS_IMAGE_THUMBNAIL_TYPE);
		if ($pixel < $image->getWidth() || $pixel < $image->getHeight()) {
			$image = $image->getThumbnail($pixel);
		}

		$suffixes = BSImage::getSuffixes();
		$filename = sprintf('%04d%s', $pixel, $suffixes[$image->getType()]);
		if (!$file = $dir->getEntry($filename, 'BSImageFile')) {
			$file = $dir->createEntry($filename, 'BSImageFile');
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
	 * @param string $size サイズ
	 */
	public function removeThumbnail (BSImageContainer $record, $size) {
		$name = $this->getEntryName($record, $size);
		if ($dir = $this->getDirectory()->getEntry($name)) {
			$dir->delete();
		}
	}

	/**
	 * サムネイルのURLを返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @param integer $pixel ピクセル数
	 * @return BSURL URL
	 */
	public function getURL (BSImageContainer $record, $size, $pixel = null) {
		if ($file = $this->getFile($record, $size, $pixel)) {
			$url = new BSURL;
			$url['path'] = sprintf(
				'/carrotlib/images/cache/%s/%s',
				$this->getEntryName($record, $size),
				$file->getName()
			);
		} else {
			if (!$module = BSModule::getInstance('User' . get_class($record))) {
				throw new BSModuleException('User%sモジュールが見つかりません。');
			}
			$url = $module->getURL();
			$url->setActionName('Image');
			$url->setRecordID($record);
			$url->setParameter('size', $size);
			if ($pixel) {
				$url->setParameter('pixel', $pixel);
			}
		}
		return $url;
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @param integer $pixel ピクセル数
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo (BSImageContainer $record, $size, $pixel = null) {
		$info = new BSArray;
		if (!$file = $record->getImageFile($size)) {
			return;
		} else if ($pixel) {
			if (!$image = $this->getThumbnail($record, $size, $pixel)) {
				$image = $this->setThumbnail($record, $size, $pixel, $file->getEngine());
			}
			$info['is_cache'] = 1;
		} else {
			$image = $file->getEngine();
		}

		$info['url'] = $this->getURL($record, $size, $pixel)->getContents();
		$info['width'] = $image->getWidth();
		$info['height'] = $image->getHeight();
		$info['alt'] = $record->getLabel();
		$info['type'] = $image->getType();
		return $info;
	}

	/**
	 * サムネイル名を生成して返す
	 *
	 * @access private
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ
	 * @return string サムネイル名
	 */
	private function getEntryName (BSImageContainer $record, $size) {
		return sprintf('%s_%06d_%s', get_class($record), $record->getID(), $size);
	}
}

/* vim:set tabstop=4: */
