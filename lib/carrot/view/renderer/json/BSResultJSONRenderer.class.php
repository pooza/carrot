<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.json
 */

/**
 * API結果文書用 JSONレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSResultJSONRenderer extends BSJSONRenderer {
	private $params;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->params = BSArray::create();
		$this->params['status'] = 200;
		$this->result = BSArray::create();
	}

	/**
	 * パラメータ配列を返す
	 *
	 * @access public
	 */
	public function getParameters () {
		return $this->params;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		$contents = $this->result->decode();
		$contents['api'] = $this->params->decode();
		return $this->getSerializer()->encode($contents);
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		if (!($contents instanceof BSParameterHolder)) {
			throw new BSException(get_class($this) . 'は、配列でない結果文書を返せません。');
		}
		$this->result = BSArray::create($contents);
	}
}

