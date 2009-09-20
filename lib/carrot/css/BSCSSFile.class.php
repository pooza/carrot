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
class BSCSSFile extends BSFile implements BSDocumentSetEntry {

	/**
	 * 内容を最適化して返す
	 *
	 * @access public
	 * @return string 最適化された内容
	 */
	public function getOptimizedContents () {
		$contents = BSController::getInstance()->getAttribute($this, $this->getUpdateDate());
		if ($contents === null) {
			$renderer = new BSPlainTextRenderer;
			$contents = BSString::convertLineSeparator($contents);
			$renderer->setContents(mb_ereg_replace('/\\*.*?\\*/', null, $this->getContents()));
			foreach ($renderer as $line) {
				$contents .= trim($line) . "\n";
			}
			$contents = mb_ereg_replace('\\n+', "\n", $contents);
			$contents = mb_ereg_replace('^\\n', null, $contents);
			$contents = mb_ereg_replace('\\n$', null, $contents);
			$contents = mb_ereg_replace(' *{ *', ' {', $contents);
			$contents = mb_ereg_replace(' *}', '}', $contents);
			$contents = mb_ereg_replace(' *: *', ':', $contents);
			BSController::getInstance()->setAttribute($this, $contents);
		}
		return $contents;
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
		return sprintf('CSSファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
