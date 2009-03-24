<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

BSUtility::includeFile('mpc/MobilePictogramConverter.php');

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent {
	private $mpc;
	const CARROT_INTERNAL = BS_CARROT_NAME;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['id'] = $this->getID();
		$this->attributes['query'] = new BSArray;
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 * @abstract
	 */
	abstract public function getID ();

	/**
	 * Smartyを初期化する
	 *
	 * @access public
	 * @param BSSmarty
	 */
	public function initializeSmarty (BSSmarty $smarty) {
		$smarty->setAttribute('useragent', $this->getAttributes());
		$smarty->setEncoding('sjis-win');
		$smarty->addOutputFilter('mobile');
		$smarty->addOutputFilter('encoding');
		$smarty->addOutputFilter('trim');
	}

	/**
	 * セッションハンドラを設定
	 *
	 * @access public
	 * @param BSSessionHandler
	 */
	public function setSession (BSSessionHandler $session) {
		parent::setSession($session);

		$url = new BSURL;
		$url->setParameters($this->attributes['query']);
		$url->setParameter($session->getName(), $session->getID());
		if (BS_DEBUG) {
			$url->setParameter(BSRequest::USER_AGENT_ACCESSOR, $this->getName());
		}
		$this->attributes['query'] = $url->getParameters();
		$this->attributes['query_params'] = $url['query'];
	}

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return true;
	}

	/**
	 * 絵文字変換器を返す
	 *
	 * @access protected
	 * @return MPC_Common 絵文字変換器
	 */
	protected function getMPC () {
		if (!$this->mpc) {
			$carrier = $this->getMPCCarrierCode();
			BSUtility::includeFile('MPC/Carrier/' . strtolower($carrier) . '.php');
			$class = 'MPC_' . $carrier;
			$this->mpc = new $class;
			$this->mpc->setFromCharset(MPC_FROM_CHARSET_UTF8);
			$this->mpc->setFrom(strtoupper($carrier));
			$this->mpc->setStringType(MPC_FROM_OPTION_RAW);
			$this->mpc->setImagePath('/carrotlib/images/mpc');
		}
		return $this->mpc;
	}

	/**
	 * MPC向けキャリア名を返す
	 *
	 * @access protected
	 * @return string キャリア名
	 * @abstract
	 */
	abstract protected function getMPCCarrierCode ();

	/**
	 * 絵文字を含んだ文字列を変換する
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return string 変換後文字列
	 * @abstract
	 */
	public function convertPictogram ($body) {
		$this->getMPC()->setString($body);
		return $this->getMPC()->convert($this->getMPCCarrierCode(), self::CARROT_INTERNAL);
	}

	/**
	 * 文字列から絵文字を削除する
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return string 変換後文字列
	 */
	public function trimPictogram ($body) {
		$this->getMPC()->setString($body);
		return $this->getMPC()->except();
	}

	/**
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			$this->attributes['platform'] = $this->getType();
		}
		return $this->attributes['platform'];
	}

	/**
	 * 全キャリアのドメインサフィックスを返す
	 *
	 * @access public
	 * @return string[] ドメインサフィックスの配列
	 * @static
	 */
	static public function getDomainSuffixes () {
		$patterns = array();
		foreach (array('Docomo', 'Au', 'SoftBank') as $carrier) {
			$useragent = BSClassLoader::getInstance()->getObject($carrier, 'UserAgent');
			$patterns[$useragent->getType()] = $useragent->getDomainSuffix();
		}
		return $patterns;
	}
}

/* vim:set tabstop=4: */
