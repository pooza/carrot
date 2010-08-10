<?php
/**
 * @package org.carrot-framework
 * @subpackage test
 */

/**
 * テストマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTestManager implements IteratorAggregate {
	private $tests;
	private $errors;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->tests = new BSArray;
		$this->errors = new BSArray;

		$dirs = new BSArray(array(
			BSFileUtility::getDirectory('tests'),
			BSFileUtility::getDirectory('local_tests'),
		));
		foreach ($dirs as $dir) {
			$this->tests->merge($this->load($dir));
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTestManager インスタンス
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
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	private function load (BSDirectory $dir) {
		$tests = new BSArray;
		foreach ($dir as $entry) {
			if ($entry->isDirectory()) {
				$tests->merge($this->load($entry));
			} else {
				require_once($entry->getPath());
				$class = BSClassLoader::extractClass($entry->getPath());
				$tests[] = new $class;
			}
		}
		return $tests;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @return boolean 成功ならTrue
	 */
	public function execute () {
		foreach ($this as $test) {
			$test->execute();
			$this->errors->merge($test->getErrors());
		}
		return !$this->errors->count();
	}

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->tests->getIterator();
	}
}

/* vim:set tabstop=4: */
