<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml
 */

/**
 * XML要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXMLElement implements IteratorAggregate {
	protected $contents;
	protected $body;
	protected $name;
	protected $attributes;
	protected $elements;
	protected $raw = false;
	protected $empty = false;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->elements = new BSArray;
		if (!BSString::isBlank($name)) {
			$this->setName($name);
		}
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$value = trim($value);
		$value = BSString::convertEncoding($value, 'utf-8');
		$this->attributes[$name] = $value;
		$this->contents = null;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		$this->attributes->removeParameter($name);
		$this->contents = null;
	}

	/**
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param string[] $values 属性の配列
	 */
	public function setAttributes ($values) {
		foreach ($values as $key => $value) {
			$this->setAttribute($key, $value);
		}
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前を設定
	 *
	 * @access public
	 * @param string $name 名前
	 */
	public function setName ($name) {
		$this->name = $name;
		$this->contents = null;
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @return string 本文
	 */
	public function getBody () {
		return $this->body;
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function setBody ($body = null) {
		if (BSNumeric::isZero($body)) {
			$this->body = 0;
		} else if (BSString::isBlank($body)) {
			$this->body = null;
		} else {
			if ($body instanceof BSStringFormat) {
				$body = $body->getContents();
			}
			$body = trim($body);
			$body = BSString::convertEncoding($body, 'utf-8');
			$this->body = $body;
		}
		$this->contents = null;
	}

	/**
	 * 指定した名前に一致する要素を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSXMLElement 名前に一致する最初の要素
	 */
	public function getElement ($name) {
		foreach ($this->getElements() as $child) {
			if ($child->getName() == $name) {
				return $child;
			}
		}
	}

	/**
	 * 子要素を全て返す
	 *
	 * @access public
	 * @return BSArray 子要素全て
	 */
	public function getElements () {
		return $this->elements;
	}

	/**
	 * 空要素か？
	 *
	 * @access public
	 * @return boolean 空要素ならTrue
	 */
	public function isEmptyElement () {
		return $this->empty;
	}

	/**
	 * 空要素かを設定
	 *
	 * @access public
	 * @param boolean $flag 空要素ならTrue
	 */
	public function setEmptyElement ($flag) {
		$this->empty = $flag;
	}

	/**
	 * 子要素を追加
	 *
	 * @access public
	 * @param BSXMLElement $element 要素
	 * @return BSXMLElement 追加した要素
	 */
	public function addElement (BSXMLElement $element) {
		$this->elements[] = $element;
		$this->contents = null;
		return $element;
	}

	/**
	 * 子要素を生成して返す
	 *
	 * @access public
	 * @param string $name 要素名
	 * @param string $body 要素の本文
	 * @return BSXMLElement 要素
	 */
	public function createElement ($name, $body = null) {
		$element = $this->addElement(new BSXMLElement($name));
		$element->setBody($body);
		return $element;
	}

	/**
	 * 要素を検索して返す
	 *
	 * @access public
	 * @param string $path 絶対ロケーションパス
	 * @return BSXMLElement 最初にマッチした要素
	 */
	public function query ($path) {
		$path = BSString::explode('/', $path);
		$path->shift();
		if (!$path->count() || ($path->shift() != $this->getName())) {
			return;
		}

		$element = $this;
		foreach ($path as $name) {
			if (!$element = $element->getElement($name)) {
				return;
			}
		}
		return $element;
	}

	/**
	 * ネームスペースを返す
	 *
	 * @access public
	 * @return string ネームスペース
	 */
	public function getNamespace () {
		return $this->getAttribute('xmlns');
	}

	/**
	 * ネームスペースを設定
	 *
	 * @access public
	 * @param string $namespace ネームスペース
	 */
	public function setNamespace ($namespace) {
		$this->setAttribute('xmlns', $namespace);
	}

	/**
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML要素
	 */
	public function getContents () {
		if (!$this->contents) {
			$this->contents = '<' . $this->getName();
			foreach ($this->attributes as $key => $value) {
				if (!BSString::isBlank($value)) {
					$this->contents .= sprintf(' %s="%s"', $key, BSString::sanitize($value));
				}
			}

			if ($this->isEmptyElement()) {
				return $this->contents .= ' />';
			}

			$this->contents .= '>';
			foreach ($this->elements as $element) {
				$this->contents .= $element->getContents();
			}
			if ($this->raw) {
				$this->contents .= $this->getBody();
			} else {
				$this->contents .= BSString::sanitize($this->getBody());
			}
			$this->contents .= '</' . $this->getName() . '>';
		}
		return $this->contents;
	}

	/**
	 * XMLをパースして要素と属性を抽出
	 *
	 * @access public
	 * @param string $contents XML文書
	 */
	public function setContents ($contents) {
		$this->attributes->clear();
		$this->elements->clear();
		$this->body = null;
		$this->contents = $contents;

		$xml = new DOMDocument('1.0', 'utf-8');
		try {
			$xml->loadXML($contents);
		} catch (Exception $e) {
			$message = new BSStringFormat('パースエラーです。 (%s)');
			$message[] = BSString::stripTags($e->getMessage());
			throw new BSXMLException($message);
		}

		$stack = new BSArray;
		$reader = new XMLReader;
		$reader->xml($contents);
		while ($reader->read()) {
			switch ($reader->nodeType) {
				case XMLReader::ELEMENT:
					if ($stack->count()) {
						$element = $stack->getIterator()->getLast()->createElement($reader->name);
					} else {
						$element = $this;
						$this->setName($reader->name);
					}
					if (!$reader->isEmptyElement) {
						$stack[] = $element;
					}
					while ($reader->moveToNextAttribute()) {
						$element->setAttribute($reader->name, $reader->value);
					}
					break;
				case XMLReader::END_ELEMENT:
					$stack->pop();
					break;
				case XMLReader::TEXT:
					$stack->getIterator()->getLast()->setBody($reader->value);
					break;
			}
		}
	}

	/**
	 * 上位のタグでくくって返す
	 *
	 * @access public
	 * @param BSXMLElement $parent 上位の要素
	 * @return BSXMLElement 上位の要素
	 */
	public function wrap (BSXMLElement $parent) {
		$parent->addElement($this);
		return $parent;
	}

	/**
	 * RAWモードを返す
	 *
	 * @access public
	 * @return boolean RAWモード
	 */
	public function isRawMode () {
		return $this->raw;
	}

	/**
	 * RAWモードを設定
	 *
	 * RAWモード時は、本文のHTMLエスケープを行わない
	 *
	 * @access public
	 * @param boolean $mode RAWモード
	 */
	public function setRawMode ($mode) {
		$this->raw = !!$mode;
		$this->body = null;
		$this->contents = null;
	}

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->elements);
	}
}

/* vim:set tabstop=4: */
