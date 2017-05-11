<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image
 */

/**
 * 画像ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImageFile extends BSMediaFile implements BSImageContainer, BSAssignable {
	protected $renderer;
	protected $rendererClass;

	/**
	 * @access public
	 * @param string $path パス
	 * @param string $class レンダラーのクラス名
	 */
	public function __construct ($path, $class = null) {
		if (!$class) {
			$class = BSImage::getDefaultRendererClass();
		}
		$this->setPath($path);
		$this->attributes = new BSArray;
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
		$name .= BSImage::getSuffixes()[$this->getEngine()->getType()];
		parent::rename($name);
	}

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analyze () {
		$this->attributes['path'] = $this->getPath();
		$this->attributes['type'] = $this->getRenderer()->getType();
		$this->attributes['width'] = $this->getRenderer()->getWidth();
		$this->attributes['height'] = $this->getRenderer()->getHeight();
		$this->attributes['height_full'] = $this->getRenderer()->getHeight();
		$this->attributes['pixel_size'] = $this['width'] . '×' . $this['height'];
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		$this->removeImageCache('image');
		parent::delete();
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
				throw new BSImageException($this . 'の形式が不明です。');
			}
			$this->renderer = new $this->rendererClass;
			switch ($this->rendererClass) {
				case 'BSGmagickImage':
					$this->renderer->setGmagick(new Gmagick($this->getPath()));
					break;
				case 'BSImagickImage':
					$this->renderer->setImagick(new Imagick($this->getPath()));
					break;
				case 'BSImagemagick7Image':
				case 'BSImage':
					$this->renderer->setImage($this->getContents());
					break;
				default:
					throw new BSImageException($this . 'の形式が不明です。');
			}
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
		$this->attributes = null;
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
			throw new BSFileException($this . 'に書き込むことができません。');
		}

		$types = new BSArray;
		$types[] = BSMIMEType::DEFAULT_TYPE;
		$types[] = $this->getRenderer()->getType();
		if (!$types->isContain($this->getType())) {
			throw new BSImageException($this . 'のメディアタイプがレンダラーと一致しません。');
		}

		$this->removeImageCache('image');
		$this->setContents($this->getRenderer()->getContents());

		if (!BS_IMAGE_STORABLE) {
			$command = new BSCommandLine('bin/mogrify');
			$command->setDirectory(BSFileUtility::getDirectory('image_magick'));
			if ($command->isExists()) {
				$command->addValue('-comment', true);
				$command->addValue('kddi_copyright=on,copy="NO"');
				$command->addValue($this->getPath());
				$command->execute();
			}
		}
	}

	/**
	 * 表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function createElement (BSParameterHolder $params, BSUserAgent $useragent = null) {
		$params = BSArray::create($params);
		$this->resizeByWidth($params, $useragent);

		$element = new BSImageElement;
		$element->setURL($this->createURL($params));
		$element->registerStyleClass($params['style_class']);
		$element->setAttribute('width', $this['width']);
		$element->setAttribute('height', $this['height']);
		$element->setAttributes($params);
		return $element;
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function removeImageCache ($size) {
		(new BSImageManager)->removeThumbnail($this, $size);
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
	public function getImageInfo ($size, $pixel = null, $flags = null) {
		return (new BSImageManager)->getImageInfo($this, $size, $pixel, $flags);
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size) {
		return $this;
	}

	/**
	 * 画像ファイルを設定
	 *
	 * @access public
	 * @param string $name 画像名
	 * @param BSImageFile $file 画像ファイル
	 */
	public function setImageFile ($name, BSImageFile $file) {
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
	public function getImageFileBaseName ($size) {
		return $this->getBaseName();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!parent::validate()) {
			return false;
		}
		return (BSMIMEUtility::getMainType($this->analyzeType()) == 'image');
	}

	/**
	 * ラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		try {
			return BSTranslateManager::getInstance()->execute(
				$this->getBaseName(),
				'user_image',
				$language
			);
		} catch (BSTranslateException $e) {
			return $this->getBaseName();
		}
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function assign () {
		$values = $this->getImageInfo('image');
		$values['path'] = $this->getPath();
		return $values;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('画像ファイル "%s"', $this->getShortPath());
	}

	/**
	 * 探す
	 *
	 * @access public
	 * @param mixed $file パラメータ配列、BSFile、ファイルパス文字列
	 * @param string $class クラス名
	 * @return BSFile ファイル
	 * @static
	 */
	static public function search ($file, $class = 'BSImageFile') {
		if (!$file = parent::search($file, $class)) {
			return;
		}
		switch ($file->getType()) {
			case BSMIMEType::getType('jpg'):
			case BSMIMEType::getType('png'):
			case BSMIMEType::getType('gif'):
				return parent::search($file, 'BSImageFile');
		}
		return $file;
	}
}

/* vim:set tabstop=4: */
