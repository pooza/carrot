<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer
 */

/**
 * 書類セット
 *
 * BSJavaScriptSet/BSStyleSetの基底クラス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSDocumentSet implements BSTextRenderer, BSHTTPRedirector, IteratorAggregate {
	protected $name;
	protected $error;
	protected $type;
	protected $digest;
	protected $cacheFile;
	protected $documents;
	protected $contents;
	protected $url;
	static protected $entries;

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

		if (($entry = $this->getEntries()->getParameter($name)) && ($files = $entry['files'])) {
			foreach ($files as $file) {
				$this->register($file);
			}
		} else {
			if (!BSString::isBlank($this->getPrefix())) {
				$this->register($this->getPrefix());
			}
			$this->register($name);
		}
		$this->update();
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
	 * ディレクトリ名を返す
	 *
	 * @access protected
	 * @return string ディレクトリ名
	 * @abstract
	 */
	abstract protected function getDirectoryName ();

	/**
	 * ソースディレクトリを返す
	 *
	 * 書類クラスがファイルではないレンダラーなら、nullを返すように
	 *
	 * @access public
	 * @return BSDirectory ソースディレクトリ
	 * @abstract
	 */
	public function getSourceDirectory () {
		return BSFileUtility::getDirectory($this->getDirectoryName());
	}

	/**
	 * キャッシュディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory キャッシュディレクトリ
	 */
	public function getCacheDirectory () {
		return BSFileUtility::getDirectory($this->getDirectoryName() . '_cache');
	}

	/**
	 * キャッシュファイルを返す
	 *
	 * @access public
	 * @return BSFile キャッシュファイル
	 */
	public function getCacheFile () {
		if (!$this->cacheFile) {
			$dir = $this->getCacheDirectory();
			if (!$this->cacheFile = $dir->getEntry($this->digest())) {
				$this->cacheFile = $dir->createEntry($this->digest());
			}
		}
		return $this->cacheFile;
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access protected
	 * @return BSArray 設定ファイルの配列
	 */
	protected function getConfigFiles () {
		$files = new BSArray;
		$prefix = mb_ereg_replace('^' . BSLoader::PREFIX, null, get_class($this));
		$prefix = BSString::underscorize($prefix);
		$host = BSController::getInstance()->getHost();
		foreach ([$host->getName(), 'application', 'carrot'] as $name) {
			if ($file = BSConfigManager::getConfigFile($prefix . '/' . $name)) {
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
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$values = new BSArray;
			$values['class'] = get_class($this);
			$values['name'] = $this->getName();
			foreach ($this as $entry) {
				$values[$entry->getPath()] = $entry->getUpdateDate()->getTimestamp();
			}
			$this->digest = BSCrypt::digest($values);
		}
		return $this->digest;
	}

	/**
	 * 登録
	 *
	 * @access public
	 * @param mixed $entry エントリー
	 */
	public function register ($entry) {
		if (is_string($entry)) {
			$dir = $this->getSourceDirectory();
			if ($file = $dir->getEntry($entry, $this->getDocumentClass())) {
				$entry = $file;
			}
		}
		if ($entry instanceof BSSerializable) {
			$this->documents[] = $entry;
		}
		$this->digest = null;
		$this->cacheFile = null;
		$this->contents = null;
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
	 * @access public
	 */
	public function update () {
		$cache = $this->getCacheFile();
		if (BSString::isBlank($cache->getContents()) && !!$this->documents->count()) {
			$contents = new BSArray;
			foreach ($this as $file) {
				if ($file->getSerialized() === null) {
					$file->serialize();
				}
				$contents[] = $file->getSerialized();
			}
			$cache->setContents($contents->join("\n"));
			BSLogManager::getInstance()->put($this . 'を更新しました。', $this);
		}
		$this->contents = $cache->getContents();
	}

	/**
	 * 登録されている書類セットを配列で返す
	 *
	 * @access protected
	 * @return BSArray 登録内容
	 */
	protected function getEntries () {
		if (!self::$entries) {
			self::$entries = new BSArray;
		}
		if (!self::$entries[get_class($this)]) {
			self::$entries[get_class($this)] = $entries = new BSArray;
			foreach ($this->getSourceDirectory() as $file) {
				$entries[$file->getBaseName()] = new BSArray;
			}
			foreach ($this->getConfigFiles() as $file) {
				foreach (BSConfigManager::getInstance()->compile($file) as $key => $values) {
					$entries[$key] = new BSArray($values);
				}
			}
			$entries->sort();
		}
		return self::$entries[get_class($this)];
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
			$file = BSFileUtility::createTemporaryFile(null, $this->getDocumentClass());
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
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->documents);
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = BSFileUtility::createURL(
				$this->getDirectoryName() . '_cache',
				$this->getCacheFile()->getName()
			);
		}
		return $this->url;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return $this->getURL()->redirect();
	}

	/**
	 * URLをクローンして返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function createURL () {
		return clone $this->getURL();
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
