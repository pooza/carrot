<?php
/**
 * @package org.carrot-framework
 * @subpackage console
 */

/**
 * コマンドラインビルダー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCommandLine extends BSParameterHolder {
	private $command;
	private $directory;
	private $result = array();
	private $returnCode = 0;
	private $executed = false;
	private $background = false;
	private $sleepSeconds = 0;
	const WITH_QUOTE = 1;

	/**
	 * @access public
	 * @param string $command prefix以降のコマンドパス。 'bin/mysql'等。
	 */
	public function __construct ($command) {
		if (!$command) {
			throw new BSConsoleException('コマンド名が空です。');
		}
		$this->command = $command;
	}

	/**
	 * ディレクトリプリフィックスを返す
	 *
	 * @access public
	 * @return BSDirectory ディレクトリプリフィックス
	 */
	public function getDirectory () {
		return $this->directory;
	}

	/**
	 * ディレクトリプリフィックスを設定
	 *
	 * @access public
	 * @param BSDirectory $dir ディレクトリプリフィックス
	 */
	public function setDirectory (BSDirectory $dir) {
		if (!$dir->isExists()) {
			throw new BSConsoleException('%sが存在しません。', $dir);
		}
		$this->directory = $dir;
	}

	/**
	 * 値を末尾に加える
	 *
	 * @access public
	 * @param string $value 値
	 * @param string $flag フラグのビット列、現状はself::WITH_QUOTEのみ。
	 */
	public function addValue ($value, $flag = self::WITH_QUOTE) {
		if ($flag & self::WITH_QUOTE) {
			$value =  self::quote($value);
		}
		$this->parameters[] = $value;
	}

	/**
	 * 実行後の待機秒数を設定
	 *
	 * @access public
	 * @param integer $seconds 秒数
	 */
	public function setSleepSeconds ($seconds) {
		$this->sleepSeconds = $seconds;
	}

	/**
	 * バックグラウンド実行か？
	 *
	 * @access public
	 * @return boolean バックグラウンド実行ならTrue
	 */
	public function isBackground () {
		return $this->background;
	}

	/**
	 * バックグラウンド実行を設定
	 *
	 * @access public
	 * @param boolean $mode バックグラウンド実行ならTrue
	 */
	public function setBackground ($mode = true) {
		$this->background = $mode;
	}

	/**
	 * コマンドを実行
	 *
	 * @access public
	 */
	public function execute () {
		exec($this->getContents(), $this->result, $this->returnCode);
		$this->executed = true;

		if ($seconds = $this->sleepSeconds) {
			sleep($seconds);
		}
	}

	/**
	 * コマンドラインを返す
	 *
	 * @access public
	 * @return string コマンドライン
	 */
	public function getContents () {
		if ($this->directory) {
			$contents = $this->directory->getPath() . DIRECTORY_SEPARATOR . $this->command;
		} else {
			$contents = $this->command;
		}
		$contents .= ' ' . implode(' ', $this->getParameters());

		if ($this->isBackground()) {
			$contents .= ' > /dev/null &';
		}

		return $contents;
	}

	/**
	 * 実行後の標準出力を返す
	 *
	 * @access public
	 * @return string 標準出力
	 */
	public function getResult () {
		if (!$this->executed) {
			$this->execute();
		}
		return $this->result;
	}

	/**
	 * 実行後の戻り値を返す
	 *
	 * @access public
	 * @return integer 戻り値
	 */
	public function getReturnCode () {
		if (!$this->executed) {
			$this->execute();
		}
		return $this->returnCode;
	}

	/**
	 * 実行後の戻り値は、エラーを含んでいたか？
	 *
	 * @access public
	 * @return boolean エラーを含んでいたらTrue
	 */
	public function hasError () {
		return ($this->getReturnCode() != 0);
	}

	/**
	 * 引数をクォートして返す
	 *
	 * @access public
	 * @param string $value 引数
	 * @return string クォートされた引数
	 * @static
	 */
	static private function quote ($value) {
		return escapeshellarg($value);
	}
}

/* vim:set tabstop=4 ai: */
?>