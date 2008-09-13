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
	protected $reader;
	private $contents;
	private $body;
	private $name;
	private $attributes = array();
	private $elements = array();

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
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
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
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
	 * 属性の名前を全て返す
	 *
	 * @access public
	 * @return string[] 属性の名前
	 */
	public function getAttributeNames () {
		return array_keys($this->getAttributes());
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
	 * 属性を全て削除
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->attributes = array();
		$this->contents = null;
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
		if (($body === 0) || ($body === '0')) {
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
	 * 子要素を全て削除
	 *
	 * @access public
	 */
	public function clearElements () {
		$this->elements = array();
		$this->contents = null;
	}

	/**
	 * 要素を検索して返す
	 *
	 * @access public
	 * @param string $path 絶対ロケーションパス
	 * @return BSXMLElement 最初にマッチした要素
	 */
	public function query ($path) {
		$path = preg_replace('/^\//', '', $path);
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
		if ($namespace) {
			$this->setAttribute('xmlns', $namespace);
		}
	}

	/**
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML要素
	 */
	public function getContents () {
		if (!$this->contents) {
			if ($this->getAttributes()) {
				$attributes = new BSArray;
				foreach ($this->getAttributes() as $key => $value) {
					$attributes[] = sprintf('%s="%s"', $key, BSString::sanitize($value));
				}
				$this->contents = sprintf('<%s %s>', $this->getName(), $attributes->join(' '));
			} else {
				$this->contents = sprintf('<%s>', $this->getName());
			}

			foreach ($this->getElements() as $element) {
				$this->contents .= $element->getContents();
			}

			$this->contents .= BSString::sanitize($this->getBody());
			$this->contents .= sprintf('</%s>', $this->getName());
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
		$this->clearAttributes();
		$this->clearElements();
		$this->setBody();
		$this->contents = $contents;

		$xml = new DOMDocument('1.0', 'utf-8');
		if (@$xml->loadXML($contents) === false) {
			throw new BSXMLException('パースエラーです。');
		}

		$stack = array();
		$this->reader = new XMLReader;
		$this->reader->xml($contents);
		while ($this->reader->read()) {
			switch ($this->reader->nodeType) {
				case XMLReader::ELEMENT:
					if ($stack) {
						$element = new BSXMLElement($this->reader->name);
						end($stack)->addElement($element);
					} else {
						$element = $this;
						$this->setName($this->reader->name);
					}
					if (!$this->reader->isEmptyElement) {
						$stack[] = $element;
					}
					while ($this->reader->moveToNextAttribute()) {
						$element->setAttribute($this->reader->name, $this->reader->value);
					}
					break;
				case XMLReader::END_ELEMENT:
					array_pop($stack);
					break;
				case XMLReader::TEXT:
					end($stack)->setBody($this->reader->value);
					break;
			}
		}
		$this->reader = null;
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

/* vim:set tabstop=4 ai: */
?>