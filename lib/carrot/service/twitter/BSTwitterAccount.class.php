<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterアカウント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTwitterAccount implements BSImageContainer {
	private $profile;

	/**
	 * @access public
	 * @param mixed[] $profile status要素
	 */
	public function __construct ($profile = null) {
		$this->profile = $profile;
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (mb_ereg('^get([[:upper:]][[:alnum:]]+)$', $method, $matches)) {
			$name = BSString::underscorize($matches[1]);
			if (isset($this->profile[$name])) {
				return $this->profile[$name];
			}
		} 
		$message = new BSStringFormat('仮想メソッド"%s"は未定義です。');
		$message[] = $method;
		throw new BadFunctionCallException($message);
	}

	/**
	 * プロフィールアイコン画像を返す
	 *
	 * @access public
	 * @return BSImage プロフィールアイコン画像
	 */
	public function getIcon () {
		try {
			$url = BSURL::getInstance($this->profile['profile_image_url']);
			$image = new BSImage;
			$image->setImage($url->fetch());
			$image->setType(BSMIMEType::getType('png'));
			return $image;
		} catch (BSHTTPException $e) {
			return null;
		} catch (BSImageException $e) {
			return null;
		}
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
	public function getImageInfo ($size = 'icon', $pixel = null, $flags = null) {
		if ($file = $this->getImageFile()) {
			$caches = BSImageCacheHandler::getInstance();
			$info = $caches->getImageInfo($file, $size, $pixel, $flags);
			$info['alt'] = $this->getLabel();
			return $info;
		}
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size = 'icon') {
		$dir = BSFileUtility::getDirectory('twitter_account');
		$name = $this->getImageFileBaseName();
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			if (!$icon = $this->getIcon()) {
				return null;
			}

			$file = BSFileUtility::getTemporaryFile('png', 'BSImageFile');
			$file->setEngine($icon);
			$file->save();
			$file->setName($name);
			$file->moveTo($dir);
		}
		return $file;
	}

	/**
	 * 画像ファイルを設定する
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ名
	 */
	public function setImageFile (BSImageFile $file, $size = 'icon') {
		throw new BSImageException($this . 'の画像ファイルを設定できません。');
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size = 'icon') {
		return sprintf('%010d_%s', $this->getID(), $size);
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return (int)$this->profile['id'];
	}

	/**
	 * スクリーン名を返す
	 *
	 * @access public
	 * @return string スクリーン名
	 */
	public function getName () {
		return $this->profile['screen_name'];
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->profile['name'];
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterアカウント "%s"', $this->getScreenName());
	}
}

/* vim:set tabstop=4: */
