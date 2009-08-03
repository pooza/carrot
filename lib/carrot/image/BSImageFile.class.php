<?php
/**
 * @package org.carrot-framework
 * @subpackage image
 */

/**
 * 画像ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImageFile extends BSFile implements BSImageCacheContainer {
	protected $renderer;
	protected $rendererClass;
	const DEFAULT_ENGINE_CLASS = 'BSImage';

	/**
	 * @access public
	 * @param string $path パス
	 * @param string $class レンダラーのクラス名
	 */
	public function __construct ($path, $class = self::DEFAULT_ENGINE_CLASS) {
		parent::__construct($path);
		$this->rendererClass = $class;
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		return BSUtility::executeMethod($this->getRenderer(), $method, $values);
	}

	/**
	 * リネーム
	 *
	 * @access public
	 * @param string $name 新しい名前
	 */
	public function rename ($name) {
		$name .= BSImage::getSuffixes()->getParameter($this->getEngine()->getType());
		parent::rename($name);
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSImageRenderer レンダラー
	 */
	public function getRenderer () {
		if (!$this->renderer) {
			if (!$this->isExists() || !$this->getSize()) {
				throw new BSImageException('%sの形式が不明です。', $this);
			}

			$info = getimagesize($this->getPath());
			switch ($type = $info['mime']) {
				case 'image/jpeg':
					$image = imagecreatefromjpeg($this->getPath());
					break;
				case 'image/gif':
					$image = imagecreatefromgif($this->getPath());
					break;
				case 'image/png':
					$image = imagecreatefrompng($this->getPath());
					break;
				default:
					throw new BSImageException('%sの形式が不明です。', $this);
			}
			$class = BSClassLoader::getInstance()->getClassName($this->rendererClass);
			$this->renderer = new $class($info[0], $info[1]);
			$this->renderer->setType($type);
			$this->renderer->setImage($image);
		}
		return $this->renderer;
	}

	/**
	 * レンダラーを返す
	 *
	 * getRendererのエイリアス
	 *
	 * @access public
	 * @return BSImageRenderer レンダラー
	 * @final
	 */
	final public function getEngine () {
		return $this->getRenderer();
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSImageRenderer $renderer レンダラー
	 */
	public function setRenderer (BSImageRenderer $renderer) {
		$this->renderer = $renderer;
		$this->rendererClass = get_class($renderer);
	}

	/**
	 * レンダラーを設定
	 *
	 * setRendererのエイリアス
	 *
	 * @access public
	 * @param BSImageRenderer $renderer レンダラー
	 * @final
	 */
	final public function setEngine (BSImageRenderer $renderer) {
		$this->setRenderer($renderer);
	}

	/**
	 * 保存
	 *
	 * @access public
	 */
	public function save () {
		if ($this->isExists() && !$this->isWritable()) {
			throw new BSFileException('%sに書き込むことが出来ません。', $this);
		}

		$types = new BSArray;
		$types[] = BSMIMEType::DEFAULT_TYPE;
		$types[] = $this->getRenderer()->getType();
		if (!$types->isContain($this->getType())) {
			throw new BSImageException('%sのメディアタイプがレンダラーと一致しません。', $this);
		}

		switch ($this->getRenderer()->getType()) {
			case 'image/jpeg':
				imagejpeg($this->getRenderer()->getImage(), $this->getPath());
				break;
			case 'image/gif':
				imagegif($this->getRenderer()->getImage(), $this->getPath());
				break;
			case 'image/png':
				imagepng($this->getRenderer()->getImage(), $this->getPath());
				break;
			default:
				throw new BSImageException('%sのメディアタイプが正しくありません。', $this);
		}
		$this->clearImageCache();
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function clearImageCache ($size = null) {
		BSImageCacheHandler::getInstance()->removeThumbnail($this, $size);
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size = null, $pixel = null, $flags = null) {
		return BSImageCacheHandler::getInstance()->getImageInfo($this, $size, $pixel, $flags);
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size = null) {
		return $this;
	}

	/**
	 * 画像ファイルを設定
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ名
	 */
	public function setImageFile (BSImageFile $file, $size = null) {
		$this->getEngine()->setImage($file);
		$this->save();
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size = null) {
		$this->getBaseName();
	}

	/**
	 * ラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return BSTranslateManager::getInstance()->execute(
			$this->getBaseName(),
			'user_image',
			$language
		);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('画像ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
