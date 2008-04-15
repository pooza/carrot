<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

BSController::includeLegacy('/spyc/spyc.php5');

/**
 * YAML
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSYAML extends Spyc implements BSRenderer {
	private $contents;
	private $result;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSFile $file YAMLファイル
	 */
	public function __construct (BSFile $file = null) {
		if ($file) {
			$this->setFile($file);
		}
	}

	/**
	 * YAMLを返す
	 *
	 * @access public
	 * @return string YAML
	 */
	public function getContents () {
		if (!$this->contents && $this->result) {
			$this->contents = $this->dump($this->result);
		}
		return $this->contents;
	}

	/**
	 * YAMLを設定する
	 *
	 * @access public
	 * @param mixed $yaml YAML文字列又はファイル
	 */
	public function setContents ($yaml) {
		if ($yaml instanceof BSFile) {
			$this->setFile($yaml);
		} else {
			$this->contents = $yaml;
			$this->result = null;
		}
	}

	/**
	 * 結果配列を返す
	 *
	 * @access public
	 * @return mixed[] 対象配列
	 */
	public function getResult () {
		if (!$this->result && $this->contents) {
			$this->result = parent::YAMLLoad($this->contents);
		}
		return $this->result;
	}

	/**
	 * 結果配列を設定する
	 *
	 * @access public
	 * @param mixed $result 結果配列又はファイル
	 */
	public function setResult ($result) {
		if ($result instanceof BSFile) {
			$this->setFile($result);
		} else if ($yaml instanceof BSArray) {
			$this->result = $result->getParameters();
			$this->contents = null;
		} else {
			$this->result = $result;
			$this->contents = null;
		}
	}

	/**
	 * YAMLファイルを読み込む
	 *
	 * @access private
	 * @param BSFile $file ファイル
	 */
	private function setFile (BSFile $file) {
		if (!$file->isReadable()) {
			throw new BSFileException('%sを読み込めません。', $yaml);
		}
		$this->contents = $file->getContents();

		$name = get_class($this) . '.' . BSCrypt::getSHA1($file->getPath());
		$expire = $file->getUpdateDate();
		if (!$this->result = BSController::getInstance()->getAttribute($name, $expire)) {
			$this->result = parent::YAMLLoad($this->getContents());
			BSController::getInstance()->setAttribute($name, $this->getResult());
		}
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
		return 'text/plain; charset=utf-8'; // とりあえず無難な型を返しておく
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return ($this->getResult() != null);
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return '要素が含まれていません。';
	}
}

/* vim:set tabstop=4 ai: */
?>