<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer
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
	private $cacheFile;
	private $updateDate;
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
		if ($files = $entries[$name]['files']) {
			foreach ($files as $file) {
				$this->register($file);
			}
		} else {
			if (!BSString::isBlank($this->getPrefix())) {
				$this->register($this->getPrefix());
			}
			$this->register($name);
		}

		$this->updateContents();
	}

	/**
	 * 書類クラスを返す
	 *
	 * @access protected
	 * @return string 書類クラス
	 * @abstract
	 */
	abstract protected function getDocumentClass ();

	/**
	 * ソースディレクトリを返す
	 *
	 * 書類クラスがファイルではないレンダラーなら、nullを返すように
	 *
	 * @access protected
	 * @return BSDirectory ソースディレクトリ
	 */
	protected function getSourceDirectory () {
	}

	/**
	 * キャッシュディレクトリを返す
	 *
	 * @access protected
	 * @return BSDirectory キャッシュディレクトリ
	 * @abstract
	 */
	abstract protected function getCacheDirectory ();

	/**
	 * キャッシュファイルを返す
	 *
	 * @access public
	 * @return BSFile キャッシュファイル
	 */
	public function getCacheFile () {
		if (!$this->cacheFile) {
			$name = $this->getName();
			if (!BS_DEBUG) {
				$name = BSCrypt::getDigest($name);
			}

			$dir = $this->getCacheDirectory();
			if (!$this->cacheFile = $dir->getEntry($name)) {
				$this->cacheFile = $dir->createEntry($name);
			}
		}
		return $this->cacheFile;
	}

	/**
	 * 更新日付を返す
	 *
	 * @access public
	 * @return BSate 更新日付
	 */
	public function getUpdateDate () {
		if (!$this->updateDate) {
			if (!!$this->documents->count()) {
				$dates = new BSArray;
				foreach ($this as $file) {
					$dates[] = $file->getUpdateDate();
				}
				foreach ($this->getConfigFiles() as $file) {
					$dates[] = $file->getUpdateDate();
				}
				$this->updateDate = BSDate::getNewest($dates);
			} else {
				$this->updateDate = BSDate::getNow();
			}
		}
		return $this->updateDate;
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access protected
	 * @return BSArray 設定ファイルの配列
	 */
	protected function getConfigFiles () {
		$files = new BSArray;
		$prefix = mb_ereg_replace('^' . BSClassLoader::PREFIX, null, get_class($this));
		$prefix = BSString::underscorize($prefix);
		$host = BSController::getInstance()->getHost();
		foreach (array($host->getName(), 'application', 'carrot') as $name) {
			if ($file = BSConfigManager::getConfigFile($prefix . DIRECTORY_SEPARATOR . $name)) {
				$files[] = $file;
			}
		}
		return $files;
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
	 * 書類セットのプレフィックスを返す
	 *
	 * @access public
	 * @return string プレフィックス
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
	 * @access protected
	 * @param mixed $entry エントリー
	 */
	protected function register ($entry) {
		if (is_string($entry)) {
			if (!$dir = $this->getSourceDirectory()) {
				throw new BSConfigException($this . 'のソースディレクトリが未定義です。');
			}
			if (!$entry = $dir->getEntry($entry, $this->getDocumentClass())) {
				return;
			}
		}
		if ($entry instanceof BSSerializable) {
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
		return $this->contents;
	}

	/**
	 * 送信内容を更新
	 *
	 * @access protected
	 */
	protected function updateContents () {
		$cache = $this->getCacheFile();
		if ((BSString::isBlank($cache->getContents()) && !!$this->documents->count())
			|| $cache->getUpdateDate()->isPast($this->getUpdateDate())) {

			$contents = new BSArray;
			foreach ($this as $file) {
				if ($this->isOptimized()) {
					if ($file->getSerialized() === null) {
						$file->serialize();
					}
					$contents[] = $file->getSerialized();
				} else {
					$contents[] = $file->getContents();
				}
			}
			$cache->setContents($contents->join("\n"));
			BSLogManager::getInstance()->put($this . 'を更新しました。', $this);
		}
		$this->contents = $cache->getContents();
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
			$file = BSFileUtility::getTemporaryFile(null, $this->getDocumentClass());
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
		foreach ($this->getSourceDirectory() as $file) {
			$entries[$file->getBaseName()] = new BSArray;
		}
		foreach ($this->getConfigFiles() as $file) {
			foreach (BSConfigManager::getInstance()->compile($file) as $key => $values) {
				$entries[$key] = new BSArray($values);
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
		return $this->getEntries($prefix)->getKeys();
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
