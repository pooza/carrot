<?php
/**
 * @package org.carrot-framework
 * @subpackage document_set
 */

/**
 * 書類セット
 *
 * BSJavaScriptSet/BSStyleSetの基底クラス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSDocumentSet implements BSTextRenderer, IteratorAggregate {
	private $name;
	private $error;
	private $type;
	private $documents;
	private $contents;
	private $optimized = true;

	/**
	 * @access protected
	 * @param string $name 書類セット名
	 */
	public function __construct ($name = 'carrot') {
		if (BSString::isBlank($name)) {
			$name = 'carrot';
		}
		$this->name = $name;
		$this->documents = new BSArray;

		$entries = $this->getEntries();
		if (isset($entries[$name]['files']) && BSArray::isArray($entries[$name]['files'])) {
			foreach ($entries[$name]['files'] as $file) {
				$this->register($file);
			}
		} else {
			if (!BSString::isBlank($this->getPrefix())) {
				$this->register($this->getPrefix());
			}
			$this->register($name);
		}
	}

	/**
	 * 書類クラスを返す
	 *
	 * @access protected
	 * @return string 書類クラス
	 * @abstract
	 */
	abstract protected function getDocumentClassName ();

	/**
	 * ディレクトリを返す
	 *
	 * 書類クラスがファイルではないレンダラーなら、nullを返すように
	 *
	 * @access protected
	 * @return BSDirectory ディレクトリ
	 */
	protected function getDirectory () {
	}

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
	 * 書類セット名を返す
	 *
	 * @access public
	 * @return string 書類セット名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 書類セットのプリフィックスを返す
	 *
	 * @access public
	 * @return string プリフィックス
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
	 * @param mixed $entry エントリー
	 */
	public function register ($entry) {
		if (is_string($entry)) {
			if (!$dir = $this->getDirectory()) {
				throw new BSInitializationException($this . 'のディレクトリが未定義です。');
			}
			if (!$entry = $dir->getEntry($entry, $this->getDocumentClassName())) {
				return;
			}
		}
		if (($entry instanceof BSDocumentSetEntry) && $entry->validate()) {
			$this->documents[] = $entry;
		} else {
			$this->error = $entry . 'が読み込めません。' . $entry->getError();
		}
	}

	/**
	 * 最適化するか
	 *
	 * @access public
	 * @return boolean 最適化するならTrue
	 */
	public function isOptimized () {
		return $this->optimized;
	}

	/**
	 * 最適化するかを設定
	 *
	 * @access public
	 * @param boolean $flag 最適化するならTrue
	 */
	public function setOptimized ($flag) {
		$this->optimized = $flag;
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		if (!$this->contents) {
			$contents = new BSArray;
			foreach ($this as $file) {
				if ($this->isOptimized()) {
					$contents[] = $file->getOptimizedContents();
				} else {
					$contents[] = $file->getContents();
				}
			}
			$this->contents = $contents->join("\n");
		}
		return $this->contents;
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
		if (!$this->type) {
			$file = BSFile::getTemporaryFile(null, $this->getDocumentClassName());
			$this->type = $file->getType();
			$file->delete();
		}
		return $this->type;
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
	 * 登録内容を返す
	 *
	 * @access protected
	 * @access string $prefix 登録名のプレフィックス
	 * @return BSArray 登録内容
	 */
	protected function getEntries ($prefix = null) {
		$entries = new BSArray;
		foreach ($this->getDirectory() as $file) {
			$entries[$file->getBaseName()] = array();
		}
		foreach ($this->getConfigFileNames() as $configFile) {
			require(BSConfigManager::getInstance()->compile($configFile));
			$entries->setParameters($config);
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

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->documents);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('%s "%s"', get_class($this), $this->getName());
	}
}

/* vim:set tabstop=4: */
