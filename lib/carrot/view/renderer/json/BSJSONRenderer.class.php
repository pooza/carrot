<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.json
 */

/**
 * JSONレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJSONRenderer implements BSRenderer {
	protected $serializer;
	protected $contents;
	protected $result;

	/**
	 * シリアライザーを返す
	 *
	 * @access protected
	 * @return BSJSONSerializer
	 */
	protected function getSerializer () {
		if (!$this->serializer) {
			$this->serializer = new BSJSONSerializer;
		}
		return $this->serializer;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		if (is_array($contents) || ($contents instanceof BSParameterHolder)) {
			$contents = new BSArray($contents);
			$this->result = $contents->decode();
			$contents = $this->getSerializer()->encode($this->result);
		}
		$this->contents = $contents;
	}

	/**
	 * パース結果を返す
	 *
	 * @access public
	 */
	public function getResult () {
		if (!$this->result) {
			$this->result = $this->getSerializer()->decode($this->getContents());
		}
		return $this->result;
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
		return BSMIMEType::getType('json');
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

