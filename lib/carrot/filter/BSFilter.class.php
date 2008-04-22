<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * Filterのひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSFilter extends ParameterHolder {
	private $context;

	/**
	 * プロパティ取得のオーバライド
	 *
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return $this->getContext()->getController();
			case 'request':
				return $this->getContext()->getRequest();
			case 'user':
				return $this->getContext()->getUser();
			case 'context':
				return $this->getContext();
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize (Context $context, $parameters = null) {
		$this->context = $context;
		if ($parameters) {
			$this->parameters = array_merge($this->parameters, $parameters);
		}
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param FilterChain $filters フィルタチェーン
	 */
	abstract public function execute (FilterChain $filters);

	/**
	 * Contextを返す
	 *
	 * @access public
	 * @return Context Mojaviコンテキスト
	 * @final
	 */
	public final function getContext () {
		return $this->context;
	}
}

/* vim:set tabstop=4 ai: */
?>