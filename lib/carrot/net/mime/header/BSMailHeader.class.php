<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * 基底メールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSMailHeader extends BSParameterHolder {
	protected $part;
	protected $name;
	protected $contents;

	/**
	 * @access public
	 * @param BSMIMEDocument $part メールパート
	 */
	public function __construct (BSMIMEDocument $part = null) {
		if ($part) {
			$this->setPart($part);
		}
	}

	/**
	 * パートを返す
	 *
	 * @access public
	 * @return BSMIMEDocument メールパート
	 */
	public function getPart () {
		return $this->part;
	}

	/**
	 * パートを設定
	 *
	 * @access public
	 * @param BSMIMEDocument $part メールパート
	 */
	public function setPart (BSMIMEDocument $part) {
		$this->part = $part;
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 * @return BSMailHeader ヘッダ
	 */
	static public function getInstance ($name) {
		$name = BSString::stripControlCharacters($name);
		$name = BSString::capitalize($name);

		try {
			$class = str_replace('-', '', $name);
			$header = BSClassLoader::getInstance()->getObject($class, 'MailHeader');
		} catch (Exception $e) {
			$header = new self;
		}

		$header->setName($name);
		return $header;
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string ヘッダ名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前を設定
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 */
	public function setName ($name) {
		$this->name = $name;
	}

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return mixed 実体
	 */
	public function getEntity () {
		return $this->contents;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		$contents = BSString::stripControlCharacters($contents);
		$this->contents = BSMIMEUtility::decode($contents);
		$this->parseParameters();
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		$contents = BSString::stripControlCharacters($contents);
		$contents = BSMIMEUtility::decode($contents);
		if (BSString::getEncoding($this->contents . $contents) == 'ascii') {
			$contents = ' ' . $contents;
		}
		$this->contents .= $contents;
		$this->parseParameters();
	}

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		foreach (BSString::explode(';', $this->contents) as $index => $param) {
			if ($index == 0) {
				$this[0] = trim($param);
			}
			if (preg_match('/^ *([a-z\-]+)="?([^";]+)"?/iu', $param, $matches)) {
				$this[strtolower($matches[1])] = $matches[2];
			}
		}
	}

	/**
	 * ヘッダを整形して返す
	 *
	 * @access public
	 * @param ヘッダ行
	 */
	public function format () {
		if (!$this->isVisible()) {
			return null;
		}

		$contents = BSMIMEUtility::encode($this->getContents());
		$contents = str_replace(
			BSMIMEUtility::ENCODE_PREFIX,
			"\n" . BSMIMEUtility::ENCODE_PREFIX,
			$contents
		);
		$contents = BSString::split($this->name . ': ' . $contents);

		$header = null;
		foreach (BSString::explode("\n", $contents) as $line) {
			if (!BSString::isBlank($header)) {
				$line = "\t" . $line;
			}
			$header .= $line . BSMIMEDocument::LINE_SEPARATOR;
		}

		return $header;
	}

	/**
	 * 可視か？
	 *
	 * @access public
	 * @return boolean 可視ならばTrue
	 */
	public function isVisible () {
		return !BSString::isBlank($this->getContents());
	}

	/**
	 * 複数行を許容するか？
	 *
	 * @access public
	 * @return boolean 許容ならばTrue
	 */
	public function isMultiple () {
		return false;
	}
}

/* vim:set tabstop=4: */
