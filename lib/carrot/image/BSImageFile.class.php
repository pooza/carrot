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
class BSImageFile extends BSFile {
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
		if (!method_exists($this->getRenderer(), $method)) {
			throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
		}
		return call_user_func_array(array($this->getRenderer(), $method), $values);
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
		$types[] = 'application/octet-stream';
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
