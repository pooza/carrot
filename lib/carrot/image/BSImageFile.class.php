<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * 画像ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSImageFile.class.php 334 2007-06-08 11:59:26Z pooza $
 */
class BSImageFile extends BSFile {
	private $engine;
	const LINE_SEPARATOR = null;
	const DEFAULT_ENGINE_CLASS = 'BSImage';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path パス
	 * @param string $class 画像エンジンのクラス名
	 */
	public function __construct ($path, $class = self::DEFAULT_ENGINE_CLASS) {
		parent::__construct($path);
		if ($this->isExists() && $this->getSize()) {
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
			$this->setEngine(new $class($info[0], $info[1]));
			$this->getEngine()->setType($type);
			$this->getEngine()->setImage($image);
		}
	}

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->getEngine(), $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->getEngine()->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * 画像エンジンを返す
	 *
	 * @access public
	 * @return BSImageViewEngine 画像エンジン
	 */
	public function & getEngine () {
		if (!$this->engine) {
			throw new BSFileException('画像エンジンが未設定です。');
		}
		return $this->engine;
	}

	/**
	 * 画像エンジンを設定
	 *
	 * @access public
	 * @param BSImageViewEngine $engine 画像エンジン
	 */
	public function setEngine (BSImageViewEngine $engine) {
		$this->engine = $engine;
	}

	/**
	 * 各種情報を返す
	 *
	 * @access public
	 * @return mixed[] 各種情報
	 */
	public function getInfo () {
		return array(
			'width' => $this->getEngine()->getWidth(),
			'height' => $this->getEngine()->getHeight(),
			'alt' => $this->getName(),
			'type' => $this->getEngine()->getType(),
		);
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

		$types = array('application/octet-stream', $this->getEngine()->getType());
		if (!in_array($this->getType(), $types)) {
			throw new BSImageException('%sのメディアタイプが画像エンジンと一致しません。', $this);
		}

		switch ($this->getEngine()->getType()) {
			case 'image/jpeg':
				imagejpeg($this->getEngine()->getImage(), $this->getPath());
				break;
			case 'image/gif':
				imagegif($this->getEngine()->getImage(), $this->getPath());
				break;
			case 'image/png':
				imagepng($this->getEngine()->getImage(), $this->getPath());
				break;
			default:
				throw new BSImageException('%sのメディアタイプが正しくありません。', $this);
		}
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('画像ファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>