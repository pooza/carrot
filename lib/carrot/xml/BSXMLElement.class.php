<?php
/**
 * @package org.carrot-framework
 * @subpackage xml
 */

/**
 * XML要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSXMLElement implements IteratorAggregate {
	private $contents;
	private $raw = false;
	private $body;
	private $name;
	private $attributes;
	private $elements;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->elements = new BSArray;
		if ($name) {
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
	 * @return string[] 属性値
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
		$this->attributes[(string)$name] = $value;
		$this->contents = null;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		$this->attributes->removeAttribute($name);
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
		} else if ($body) {
			$body = trim($body);
			$body = BSString::convertEncoding($body, 'utf-8');
			$this->body = $body;
		} else {
			$this->body = null;
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
		foreach ($this as $child) {
			if ($child->getName() == $name) {
				return $child;
			}
		}
	}

	/**
	 * 子要素を全て返す
	 *
	 * @access public
	 * @return BSXMLElement[] 子要素全て
	 */
	public function getElements () {
		return $this->elements;
	}

	/**
	 * 要素の名前を全て返す
	 *
	 * @access public
	 * @return string[] 要素の名前
	 */
	public function getElementNames () {
		$names = array();
		foreach ($this as $element) {
			$names[] = $element->getName();
		}
		return $names;
	}

	/**
	 * 子要素を追加
	 *
	 * @access public
	 * @param BSXMLElement $element 要素
	 */
	public function addElement (BSXMLElement $element) {
		$this->elements[] = $element;
		$this->contents = null;
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
		$element = new BSXMLElement($name);
		$element->setBody($body);
		$this->addElement($element);
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
		$path = ltrim($path, '/');
		if (!$steps = explode('/', $path)) {
			return;
		} else if ($steps[0] != $this->getName()) {
			return;
		}
		unset($steps[0]);
		$element = $this;
		foreach ($steps as $step) {
			if (!$element = $element->getElement($step)) {
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
			if ($this->getAttributes()->count()) {
				foreach ($this->getAttributes() as $key => $value) {
					$this->contents .= sprintf(' %s="%s"', $key, BSString::sanitize($value));
				}
			}
			if ($this->getElements()->count()) {
				$this->contents .= '>';
				foreach ($this->getElements() as $element) {
					$this->contents .= $element->getContents();
				}
				if ($this->raw) {
					$this->contents .= $this->getBody();
				} else {
					$this->contents .= BSString::sanitize($this->getBody());
				}
				$this->contents .= '</' . $this->getName() . '>';
			} else {
				$this->contents .= ' />';
			}
		}
		return $this->contents;
	}

	/**
	 * XMLをパースして要素と属性を抽出
	 *
	 * @access public
	 * @param $string $contents XML文書
	 */
	public function setContents ($contents) {
		$this->attributes->clear();
		$this->elements->clear();
		$this->setBody();
		$this->contents = $contents;

		$xml = new DOMDocument('1.0', 'utf-8');
		if (@$xml->loadXML($contents) === false) {
			throw new BSXMLException('パースエラーです。');
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
	 * RAWモードを返す
	 *
	 * @access public
	 * @return boolean RAWモード
	 */
	public function getRawMode () {
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
		$this->raw = $mode;
		$this->body = null;
		$this->contents = null;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->getElements());
	}
}

/* vim:set tabstop=4: */
