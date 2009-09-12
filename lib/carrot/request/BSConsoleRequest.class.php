<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsoleRequest extends BSRequest {
	private $options;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->options = new BSArray;
		$this->addOption(BSController::MODULE_ACCESSOR);
		$this->addOption(BSController::ACTION_ACCESSOR);
		$this->parse();
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleRequest インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピーできません。', __CLASS__);
	}

	/**
	 * コマンドラインパーサオプションを追加
	 *
	 * @access public
	 * @param string $name オプション名
	 */
	public function addOption ($name) {
		$this->options[$name] = array(
			'name' => $name,
		);
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
		$config = $config->join('');

		$this->clear();
		$this->setParameters(getopt($config));
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
}

/* vim:set tabstop=4: */
