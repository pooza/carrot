<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

// MacOSのテキストファイル（CR改行）対応
ini_set('auto_detect_line_endings', true);

/**
 * ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSFile extends BSDirectoryEntry implements BSViewEngine {
	private $basename;
	private $suffix;
	private $mode;
	private $lines;
	private $size;
	private $handle;
	private $error;
	protected $type;
	const LINE_SEPARATOR = "\n";

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path パス
	 */
	public function __construct ($path) {
		$this->setPath($path);
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		if ($this->isOpened()) {
			$this->close();
		}
	}

	/**
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		if (!$this->suffix) {
			$name = explode('.', $this->getName());
			if (1 < count($name)) {
				$this->suffix = '.' . end($name);
			}
		}
		return $this->suffix;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		if (!$this->type) {
			$this->type = BSTypeList::getType($this->getSuffix());
		}
		return $this->type;
	}

	/**
	 * パスを設定する
	 *
	 * @access public
	 * @param string $path パス
	 */
	public function setPath ($path) {
		parent::setPath($path);
		$this->basename = null;
		$this->suffix = null;
	}

	/**
	 * ベース名を返す
	 *
	 * @access public
	 * @return string ベース名
	 */
	public function getBaseName () {
		if (!$this->basename) {
			$this->basename = basename($this->getPath(), $this->getSuffix());
		}
		return $this->basename;
	}

	/**
	 * リネーム
	 *
	 * @access public
	 * @param string $name 新しい名前
	 */
	public function rename ($name) {
		if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}
		parent::rename($name);
	}

	/**
	 * 移動
	 *
	 * @access public
	 * @param BSDirectory $dir 移動先ディレクトリ
	 */
	public function moveTo ($dir) {
		if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}
		parent::moveTo($dir);
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->isWritable($this->getPath())) {
			throw new BSFileException('%sを削除出来ません。', $this);
		} else if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}
		if (!unlink($this->getPath())) {
			throw new BSFileException('%sを削除出来ません。', $this);
		}
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 * @param string $mode モード
	 */
	public function open ($mode = 'r') {
		if (!in_array($mode, array('r', 'a', 'w'))) {
			throw new BSFileException('モード"%s"が正しくありません。', $mode);
		} else if (($mode == 'r') && !$this->isExists()) {
			throw new BSFileException('%sが存在しません。', $this);
		} else if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}

		if (!$this->handle = fopen($this->getPath(), $mode)) {
			$this->handle = null;
			$this->mode = null;
			throw new BSFileException('%sをモード"%s"で開くことが出来ませんでした。',
				$mode,
				$this
			);
		}

		$this->mode = $mode;
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if ($this->isOpened()) {
			fclose($this->handle);
		}
		$this->handle = null;
		$this->mode = null;
	}

	/**
	 * ストリームに1行書き込む
	 *
	 * @access public
	 * @param string $str 書き込む内容
	 */
	public function putLine ($str = '') {
		if (!$this->isOpened() || !in_array($this->mode, array('w', 'a'))) {
			throw new BSFileException('%sはw又はaモードで開かれていません。', $this);
		}

		flock($this->handle, LOCK_EX);
		fputs($this->handle, $str . self::LINE_SEPARATOR);
		flock($this->handle, LOCK_UN);
		$this->lines = null;
	}

	/**
	 * ストリームから1行読み込む
	 *
	 * @access public
	 * @param integer $length 一度に読み込む最大のサイズ
	 * @return string 読み込んだ内容
	 */
	public function getLine ($length = 4096) {
		if ($this->isOpened()) {
			if ($this->mode != 'r') {
				throw new BSFileException('%sはrモードで開かれていません。', $this);
			}
		} else {
			$this->open();
		}

		if ($this->isEof()) {
			return '';
		}
		$line = fgets($this->handle, $length);
		$line = rtrim($line);
		return $line;
	}

	/**
	 * 全ての行を返す
	 *
	 * @access public
	 * @return string[] 読み込んだ内容の配列
	 */
	public function getLines () {
		if (!$this->lines) {
			$this->lines = file($this->getPath());
			foreach ($this->lines as &$line) {
				$line = rtrim($line);
			}
		}
		return $this->lines;
	}

	/**
	 * 全て返す
	 *
	 * @access public
	 * @return string 読み込んだ内容
	 */
	public function getContents () {
		return file_get_contents($this->getPath());
	}

	/**
	 * 書き換える
	 *
	 * @access public
	 * @param string $contents 書き込む内容
	 */
	public function setContents ($contents) {
		$this->open('w');
		$this->putLine($contents);
		$this->close();
	}

	/**
	 * 開かれているか否か
	 *
	 * @access public
	 * @return boolean 開かれていたらtrue
	 */
	public function isOpened () {
		return is_resource($this->handle);
	}

	/**
	 * ポインタがEOFに達しているか
	 *
	 * @access public
	 * @return boolean EOFに達していたらtrue
	 */
	public function isEof () {
		if (!$this->isReadable()) {
			throw new BSFileException('%sを読み込めません。', $this);
		}
		return feof($this->handle);
	}

	/**
	 * ファイルサイズを返す
	 *
	 * @access public
	 * @return integer ファイルサイズ
	 */
	public function getSize () {
		if ($this->size === null) {
			if (!$this->isExists()) {
				throw new BSFileException('%sが存在しません。', $this);
			}
			$this->size = filesize($this->getPath());
		}
		return $this->size;
	}

	/**
	 * 書式化されたファイルサイズを文字列で返す
	 *
	 * @access public
	 * @param string $suffix サフィックス、デフォルトはバイトの略で"B"
	 * @return string 書式化されたファイルサイズ
	 */
	function getFormattedSize ($suffix = 'B') {
		foreach (array('', 'K', 'M', 'G', 'T', 'P', 'E') as $number => $unit) {
			$unitsize = pow(1024, $number);
			if ($this->getSize() < ($unitsize * 1024 * 2)) {
				return sprintf(
					'%s %s%s',
					number_format(floor($this->getSize() / $unitsize)),
					$unit,
					$suffix
				);
			}
		}
	}

	/**
	 * アップロードされたファイルか
	 *
	 * @access public
	 * @return boolean アップロードされたファイルならTrue
	 */
	public function isUploaded () {
		return is_uploaded_file($this->getPath());
	}

	/**
	 * ファイルか
	 *
	 * @access public
	 * @return boolean ファイルならTrue
	 */
	public function isFile () {
		return true;
	}

	/**
	 * ディレクトリか
	 *
	 * @access public
	 * @return boolean ディレクトリならTrue
	 */
	public function isDirectory () {
		return false;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->isReadable()) {
			$this->error = $this . 'が開けません。';
			return false;
		}
		return true;
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
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>