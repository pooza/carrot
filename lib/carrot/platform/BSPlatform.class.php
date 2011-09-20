<?php
/**
 * @package org.carrot-framework
 * @subpackage platform
 */

/**
 * 抽象プラットフォーム
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSPlatform extends BSParameterHolder {

	/**
	 * @access protected
	 * @param string[] $params パラメータ配列
	 * @param string $uname uname文字列
	 */
	protected function __construct ($params) {
		$this->setParameters($params);
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $name プラットフォーム名
	 * @return BSPlatform インスタンス
	 * @static
	 */
	static public function create ($name) {
		try {
			$class = BSClassLoader::getInstance()->getClass($name, 'Platform');
		} catch (Exception $e) {
			$class = 'BSDefaultPlatform';
		}
		return new $class(array(
			'name' => $name,
			'version' => php_uname('r'),
		));
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string プラットフォーム名
	 */
	public function getName () {
		return $this['name'];
	}

	/**
	 * ファイルをリネーム
	 *
	 * @access public
	 * @param BSFile $file 対象ファイル
	 * @param string $path リネーム後のパス
	 */
	public function renameFile (BSFile $file, $path) {
		if (!rename($file->getPath(), $path)) {
			throw new BSFileException($this . 'を移動できません。');
		}
	}

	/**
	 * ファイルの内容から、メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function analyzeFile (BSFile $file) {
		return rtrim(exec('file -b --mime-type ' . $file->getPath()));
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリ名
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		$constants = new BSConstantHandler($name);
		foreach (array($this->getName(), 'default') as $suffix) {
			if (!BSString::isBlank($path = $constants['dir_' . $suffix])) {
				return new BSDirectory($path);
			}
		}
	}

	/**
	 * プロセスのオーナーを返す
	 *
	 * @access public
	 * @return string プロセスオーナーのユーザー名
	 */
	public function getProcessOwner () {
		$constants = new BSConstantHandler('app_process');
		foreach (array($this->getName(), 'default') as $suffix) {
			if (!BSString::isBlank($owner = $constants['uid_' . $suffix])) {
				return $owner;
			}
		}
	}

	/**
	 * メディアコンバータ定数を返す
	 *
	 * @param string $name 定数名
	 * @param BSPlatform $platform 対象プラットフォーム
	 * @return string 定数値
	 */
	public function getMediaConverterConstant ($name) {
		foreach (array($this->getName(), 'default') as $prefix) {
			$constants = new BSConstantHandler('ffmpeg_convert_' . $prefix);
			if (!BSString::isBlank($value = $constants[$name])) {
				return $value;
			}
		}
	}
}

/* vim:set tabstop=4: */
