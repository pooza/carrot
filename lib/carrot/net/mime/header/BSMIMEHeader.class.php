<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header
 */

/**
 * 基底ヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSMIMEHeader extends BSParameterHolder {
	use BSBasicObject;
	protected $part;
	protected $name;
	protected $contents;
	const WITHOUT_CRLF = 1;

	/**
	 * @access protected
	 */
	protected function __construct () {
	}

	/**
	 * パートを返す
	 *
	 * @access public
	 * @return BSMIMEDocument メールパート
	 */
	public function getPart () {
		return $this->part;
	}

	/**
	 * パートを設定
	 *
	 * @access public
	 * @param BSMIMEDocument $part メールパート
	 */
	public function setPart (BSMIMEDocument $part) {
		$this->part = $part;
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 * @return BSMIMEHeader ヘッダ
	 */
	static public function create ($name) {
		$name = self::capitalize($name);
		try {
			$loader = BSLoader::getInstance();
			$class = $loader->getClass(str_replace('-', '', $name), 'MIMEHeader');
		} catch (Exception $e) {
			$class = 'BSMIMEHeader';
		}
		$header = new $class;
		$header->setName($name);
		return $header;
	}

	static private function capitalize ($name) {
		$name = BSString::stripControlCharacters($name);
		$name = BSString::explode('-', $name);
		$name = BSString::capitalize($name);
		return $name->join('-');
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string ヘッダ名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前を設定
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 */
	public function setName ($name) {
		$this->name = $name;
	}

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return mixed 実体
	 */
	public function getEntity () {
		return $this->contents;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		$contents = BSString::stripControlCharacters($contents);
		$this->contents = BSMIMEUtility::decode($contents);
		$this->parse();
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		$contents = BSString::stripControlCharacters($contents);
		$contents = BSMIMEUtility::decode($contents);
		if (BSString::getEncoding($this->contents . $contents) == 'ascii') {
			$contents = ' ' . $contents;
		}
		$this->contents .= $contents;
		$this->parse();
	}

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parse () {
		foreach (BSString::explode(';', $this->contents) as $index => $param) {
			if ($index == 0) {
				$this[0] = trim($param);
			}
			if (mb_ereg('^ *([-[:alpha:]]+)="?([^";]+)"?', $param, $matches)) {
				$this[BSString::toLower($matches[1])] = $matches[2];
			}
		}
	}

	/**
	 * 改行などの整形を行うか？
	 *
	 * @access protected
	 * @return boolean 整形を行うならTrue
	 */
	protected function isFormattable () {
		return true;
	}

	/**
	 * ヘッダを整形して返す
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_CRLF 改行を含まない
	 * @return ヘッダ行
	 */
	public function format ($flags = null) {
		if (!$this->isVisible()) {
			return null;
		}
		if (!$this->isFormattable() || ($flags & self::WITHOUT_CRLF)) {
			return $this->name . ': ' . $this->getContents();
		}

		$contents = $this->name . ': ' . BSMIMEUtility::encode($this->getContents());
		$contents = BSString::explode(BSMIMEUtility::ENCODE_PREFIX, $contents);
		$glue = BSMIMEDocument::LINE_SEPARATOR . ' ' . BSMIMEUtility::ENCODE_PREFIX;
		$contents = $contents->join($glue) . BSMIMEDocument::LINE_SEPARATOR;
		return $contents;
	}

	/**
	 * 可視か？
	 *
	 * @access public
	 * @return boolean 可視ならばTrue
	 */
	public function isVisible () {
		return !BSString::isBlank($this->getContents());
	}

	/**
	 * キャッシュ可能か？
	 *
	 * @access public
	 * @return boolean キャッシュ可能ならばTrue
	 */
	public function isCacheable () {
		return true;
	}

	/**
	 * 複数行を許容するか？
	 *
	 * @access public
	 * @return boolean 許容ならばTrue
	 */
	public function isMultiple () {
		return false;
	}
}

