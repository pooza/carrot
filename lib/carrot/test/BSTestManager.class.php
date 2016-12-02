<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage test
 */

/**
 * テストマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTestManager implements IteratorAggregate {
	use BSSingleton;
	private $tests;
	private $errors;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->tests = new BSArray;
		$this->errors = new BSArray;

		$dirs = new BSArray([
			BSFileUtility::getDirectory('tests'),
			BSFileUtility::getDirectory('local_tests'),
		]);
		foreach ($dirs as $dir) {
			$this->tests->merge($this->load($dir));
		}
	}

	private function load (BSDirectory $dir) {
		$tests = new BSArray;
		foreach ($dir as $entry) {
			if (!$entry->isIgnore()) {
				if ($entry instanceof BSDirectory) {
					$tests->merge($this->load($entry));
				} else if ($entry instanceof BSFile) {
					require $entry->getPath();
					$class = BSLoader::extractClass($entry->getPath());
					$tests[] = new $class;
				}
			}
		}
		return $tests;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $name テスト名
	 * @return boolean 成功ならTrue
	 */
	public function execute ($name = null) {
		foreach ($this as $test) {
			if (BSString::isBlank($name) || $test->isMatched($name)) {
				$this->put('---');
				$message = new BSStringFormat('%s:');
				$message[] = get_class($test);
				$this->put($message);
				$test->execute();
				$this->errors->merge($test->getErrors());
			}
		}

		$this->put('===');
		$message = new BSStringFormat('%d errors');
		$message[] = $this->errors->count();
		$this->put($message);
		return !$this->errors->count();
	}

	/**
	 * 標準出力にメッセージを出力
	 *
	 * @access public
	 * @param mixed $message メッセージ
	 */
	public function put ($message) {
		if ($message instanceof BSStringFormat) {
			$message = $message->getContents();
		}
		print $message . "\n";
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
