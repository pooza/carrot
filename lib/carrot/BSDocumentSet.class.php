<?php
/**
 * @package org.carrot-framework
 */

/**
 * 書類セット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSDocumentSet implements BSTextRenderer {
	protected $name;
	protected $error;
	protected $contentFragments;

	/**
	 * @access protected
	 * @param string $name 書類セット名
	 */
	public function __construct ($name = 'carrot') {
		if (BSString::isBlank($name)) {
			$name = 'carrot';
		}
		$this->name = $name;
		$this->contentFragments = new BSArray;

		$entries = $this->getEntries();
		if (isset($entries[$name]['files']) && BSArray::isArray($entries[$name]['files'])) {
			foreach ($entries[$name]['files'] as $file) {
				$this->register($file);
			}
		} else {
			if (!BSString::isBlank($prefix = $this->getPrefix())) {
				$this->register($prefix);
			}
			$this->register($name);
		}
	}

	/**
	 * 書類のクラス名を返す
	 *
	 * @access protected
	 * @return string 書類のクラス名
	 * @abstract
	 */
	abstract protected function getDocumentClassName ();

	/**
	 * ディレクトリを返す
	 *
	 * @access protected
	 * @return BSDirectory ディレクトリ
	 * @abstract
	 */
	abstract protected function getDirectory ();

	/**
	 * 設定ファイルの名前を返す
	 *
	 * @access protected
	 * @return BSArray 設定ファイルの名前
	 */
	protected function getConfigFileNames () {
		$prefix = mb_ereg_replace('^' . BSClassLoader::PREFIX, null, get_class($this));
		$prefix = BSString::underscorize($prefix);
		return new BSArray(array(
			$prefix . '/application',
			$prefix . '/carrot',
		));
	}

	/**
	 * スタイルセット名を返す
	 *
	 * @access public
	 * @return string スタイルセット名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * スタイルセット名を返す
	 *
	 * @access public
	 * @return string スタイルセット名
	 */
	public function getPrefix () {
		$name = BSString::explode('.', $this->getName());
		if (1 < $name->count()) {
			return $name[0];
		}
	}

	/**
	 * 登録
	 *
	 * @access public
	 * @param string $name ファイル名
	 */
	public function register ($name) {
		if ($file = $this->getDirectory()->getEntry($name, $this->getDocumentClassName())) {
			if ($file->isReadable()) {
				$this->contentFragments[$name] = $file->getOptimizedContents();
			} else {
				$this->error = $file . 'が読み込めません。';
				return;
			}
		}
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		return $this->contentFragments->join("\n");
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		$file = BSFile::getTemporaryFile(null, $this->getDocumentClassName());
		$type = $file->getType();
		$file->delete();
		return $type;
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return 'utf-8';
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return BSString::isBlank($this->error);
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('%s "%s"', get_class($this), $this->getName());
	}

	/**
	 * 登録内容を返す
	 *
	 * @access protected
	 * @access string $prefix 登録名のプレフィックス
	 * @return BSArray 登録内容
	 */
	protected function getEntries ($prefix = null) {
		$entries = new BSArray;
		foreach ($this->getConfigFileNames() as $configFile) {
			require(BSConfigManager::getInstance()->compile($configFile));
			$entries->setParameters($config);
		}
		foreach ($this->getDirectory() as $file) {
			if (!$entries->hasParameter($file->getBaseName())) {
				$entries[$file->getBaseName()] = array();
			}
		}

		if (!BSString::isBlank($prefix)) {
			$pattern = '^' . $prefix . '\\.?';
			foreach ($entries as $key => $entry) {
				if (!mb_ereg($pattern, $key)) {
					$entries->removeParameter($key);
				}
			}
		}
		return $entries->sort();
	}

	/**
	 * 登録名を返す
	 *
	 * @access public
	 * @access string $prefix 登録名のプレフィックス
	 * @return BSArray 登録名
	 */
	public function getEntryNames ($prefix = null) {
		return $this->getEntries($prefix)->getKeys(BSArray::WITHOUT_KEY);
	}
}

/* vim:set tabstop=4: */
