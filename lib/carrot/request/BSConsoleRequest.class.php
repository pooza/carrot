<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsoleRequest extends BSRequest {
	private $options;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->options = new BSArray;
		$this->addOption(BSModule::ACCESSOR);
		$this->addOption(BSAction::ACCESSOR);
		$this->addOption(BSRecord::ACCESSOR);
		$this->parse();

		if (BSString::isBlank($this[BSModule::ACCESSOR])) {
			$this[BSModule::ACCESSOR] = 'Console';
		}
	}

	/**
	 * コマンドラインパーサオプションを追加
	 *
	 * @access public
	 * @param string $name オプション名
	 */
	public function addOption ($name) {
		$this->options[$name] = [
			'name' => $name,
		];
	}

	/**
	 * コマンドラインをパース
	 *
	 * @access public
	 */
	public function parse () {
		$config = new BSArray;
		foreach ($this->options as $option) {
			$config[] = $option['name'] . ':';
		}

		$this->clear();
		$this->setParameters(getopt($config->join('')));
		$this['id'] = $this[BSRecord::ACCESSOR];
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		return null;
	}

	/**
	 * ヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] ヘッダ一式
	 */
	public function getHeaders () {
		return null;
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		return null;
	}

	/**
	 * UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			$this->setUserAgent(BSUserAgent::create('Console'));
		}
		return $this->useragent;
	}

	/**
	 * 実際のUserAgentを返す
	 *
	 * エミュレート環境でも、実際のUserAgentを返す。
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getRealUserAgent () {
		return $this->getUserAgent();
	}
}

/* vim:set tabstop=4: */
