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

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->options = new BSArray;
		$this->addOption(BSController::MODULE_ACCESSOR);
		$this->addOption(BSController::ACTION_ACCESSOR);
		$this->parse();
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
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 */
	public function isCLI () {
		return true;
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
