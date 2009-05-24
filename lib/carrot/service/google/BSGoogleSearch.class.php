<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google
 */

/**
 * Google検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSGoogleSearch {
	private $engine;
	private $query;
	private $result = array();
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		if (!extension_loaded('soap')) {
			throw new BSGoogleException('SOAPモジュールが利用出来ません。');
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSGoogle インスタンス
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
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 実行
	 *
	 * queryのエイリアス
	 *
	 * @access public
	 * @param string $query 検索文字列
	 * @param integer $limit 件数の上限
	 * @return mixed[][] 検索結果
	 * @final
	 */
	final public function execute ($query, $limit = 5) {
		return $this->query($query, $limit);
	}

	/**
	 * 検索
	 *
	 * @access public
	 * @param string $query 検索文字列
	 * @param integer $limit 件数の上限
	 * @return mixed[][] 検索結果
	 */
	public function query ($query, $limit = 5) {
		$this->result = array();
		$this->query = $query;

		$result = $this->getEngine()->doGoogleSearch(
			BSController::getInstance()->getConstant('GOOGLE_KEY'),
			BSString::convertEncoding($this->query, 'utf-8'),
			0, $limit, false, 'countryJP', false, 'lang_ja',
			'utf-8', 'utf-8'
		);
		if (!$result) {
			throw new BSGoogleException('Google検索に失敗しました。');
		}

		$this->setContents($result);
		return $this->getContents();
	}

	/**
	 * GoogleSearchクライアントを返す
	 *
	 * @access private
	 * @return SoapClient GoogleSearchクライアント
	 */
	private function getEngine () {
		if (!$this->engine) {
			$dir = BSController::getInstance()->getDirectory('googleapi');
			if (!$wsdl = $dir->getEntry('GoogleSearch')) {
				throw new BSGoogleException('%sが見つかりません。', $wsdl->getName());
			}
			$this->engine = new SoapClient($wsdl->getPath());
		}
		return $this->engine;
	}

	/**
	 * 直近の検索文字列を返す
	 *
	 * @access public
	 * @return string 直近の検索文字列
	 */
	public function getQuery () {
		return $this->query;
	}

	/**
	 * 直近の検索結果を返す
	 *
	 * getContentsのエイリアス
	 *
	 * @access public
	 * @return mixed[][] 直近の検索結果
	 * @final
	 */
	final public function getResult () {
		return $this->getContents();
	}

	/**
	 * 直近の検索結果を返す
	 *
	 * @access public
	 * @return mixed[][] 直近の検索結果
	 */
	public function getContents () {
		return $this->result;
	}

	/**
	 * 検索結果をセット
	 *
	 * @access private
	 * @param stdClass $result GoogleSearchクライアントが返した検索結果
	 */
	private function setContents (stdClass $result) {
		$this->result = array();
		foreach ($result->resultElements as $element) {
			try {
				$url = new BSHTTPURL($element->URL);
			} catch (BSNetException $e) {
				continue; //URLのホスト名が逆引きできない可能性がある
			}

			$this->result[] = array(
				'title' => BSString::convertEncoding($element->title),
				'uri' => $url,
				'summary' => BSString::convertEncoding($element->summary),
				'body' => BSString::convertEncoding($element->snippet),
				'size' => $element->cachedSize,
			);
		}
	}
}

/* vim:set tabstop=4: */
