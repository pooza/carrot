<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * WWWフォームレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWWWFormRenderer extends BSParameterHolder implements BSRenderer {
	protected $separator = '&';

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		if (BSString::isBlank($value)) {
			$this->removeParameter($name);
		} else {
			parent::setParameter($name, $value);
		}
	}

	/**
	 * パラメータをまとめて設定
	 *
	 * @access public
	 * @param mixed[] $params パラメータの配列、又はクエリー文字列
	 */
	public function setParameters ($params) {
		if (!is_array($params) && !($params instanceof BSParameterHolder)) {
			parse_str($params, $parsed);
			$params = BSArray::create($parsed);
		}
		parent::setParameters($params);
	}

	/**
	 * セパレータを設定
	 *
	 * @param string $separator セパレータ
	 * @access public
	 */
	public function setSeparator ($separator) {
		$this->separator = $separator;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		return http_build_query($this->getParameters(), '', $this->separator);
	}

	/**
	 * 出力内容を設定
	 *
	 * @param mixed $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		$this->clear();
		$this->setParameters($contents);
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
		return 'application/x-www-form-urlencoded';
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}
}

