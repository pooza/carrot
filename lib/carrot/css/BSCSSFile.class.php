<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * CSSファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCSSFile extends BSFile {
	private $parser;

	/**
	 * 内容を最適化して返す
	 *
	 * @access public
	 * @return string 最適化された内容
	 */
	public function getOptimizedContents () {
		$expire = $this->getUpdateDate();
		if (!$contents = BSController::getInstance()->getAttribute($this, $expire)) {
			$error = $this->getParser()->parseFile($this->getPath(), false);
			if ($error instanceof PEAR_Error) {
				throw new BSCSSException($error->getMessage());
			} else if ($error) {
				throw new BSCSSException('原因不明のエラーが発生。');
			}
			$contents = $this->getParser()->toString();
			BSController::getInstance()->setAttribute($this, $contents);
		}
		return $contents;
	}

	/**
	 * パーサーを返す
	 *
	 * @access public
	 * @return HTML_CSS パーサー
	 */
	public function getParser () {
		if (!$this->parser) {
			BSUtility::includeFile('pear/HTML/CSS.php');
			$this->parser = new HTML_CSS;
		}
		return $this->parser;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return ($this->getOptimizedContents() && !$this->getParser()->isError());
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->getParser()->_lastError;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('css');
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('CSSファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4: */
