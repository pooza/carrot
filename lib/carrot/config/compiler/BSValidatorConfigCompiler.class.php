<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * バリデータ設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSValidatorConfigCompiler extends BSConfigCompiler {
	private $fields;
	private $validators;

	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$this->parse($file);

		$this->putLine('$manager = BSValidateManager::getInstance();');
		$this->putLine('$request = BSRequest::getInstance();');
		foreach ($this->fields->getKeys(BSArray::WITHOUT_KEY) as $method) {
			$this->putMethod($method);
		}

		return $this->getBody();
	}

	private function parse (BSConfigFile $file) {
		$this->fields = new BSArray;
		$this->validators = new BSArray;

		require(BSConfigManager::getInstance()->compile('validator/carrot'));
		$this->validators->setParameters($config);
		require(BSConfigManager::getInstance()->compile('validator/application'));
		$this->validators->setParameters($config);

		$config = new BSArray($file->getResult());
		$this->parseMethods(new BSArray($config['methods']));
		$this->parseNames(new BSArray($config['names']));

		if ($validators = $config['validators']) {
			$this->parseValidators(new BSArray($validators));
		} else {
			//旧形式対応
			$message = new BSStringFormat('%sにvalidatorsエントリーがありません。');
			$message[] = $file;
			BSController::getInstance()->putLog($message);
			$this->parseValidators($config);
		}
	}

	private function parseMethods (BSArray $methods) {
		foreach ($methods as $method => $fields) {
			$method = strtoupper($method);
			if (!BSRequest::getMethodNames()->isContain($method)) {
				throw new BSConfigException('"%s"は正しくないメソッドです。', $method);
			}
			$this->fields[$method] = new BSArray;
			foreach ($fields as $field) {
				$this->fields[$method][$field] = new BSArray;
				$this->fields[$method][$field]['validators'] = new BSArray;
			}
		}
	}

	private function parseNames (BSArray $names) {
		foreach (BSWebRequest::getMethodNames() as $method) {
			foreach ($names as $name => $value) {
				if ($this->fields[$method][$name]) {
					$this->fields[$method][$name]->setParameters($value);
				}
				if ($validators = $this->fields[$method][$name]['validators']) {
					foreach ($validators as $validator) {
						if (!$this->validators->hasParameter($validator)) {
							$this->validators[$validator] = null;
						}
					}
				}
			}
		}
	}

	private function parseValidators (BSArray $config) {
		foreach ($this->validators as $name => $values) {
			if ($values === null) {
				if (!$values = $config[$name]) {
					throw new BSConfigException('バリデータ "%s" が未定義です。', $name);
				}
			}
			$this->validators[$name] = new BSArray($values);
		}
		$this->validators->sort();
	}

	private function putMethod ($method) {
		$line = new BSStringFormat('if ($request->getMethod() == BSRequest::%s) {');
		$line[] = $method;
		$this->putLine($line);
		foreach ($this->fields[$method] as $field => $info) {
			if ($info['file']) {
				$validators = new BSArray('file');
			} else {
				$validators = new BSArray('string');
			}
			if ($info['required']) {
				$validators[] = 'empty';
			}
			$validators->merge($info['validators']);
			$validators->uniquize();
			foreach ($validators as $validator) {
				$line = new BSStringFormat('  $manager->register(%s, new %s(%s));');
				$line[] = self::quote($field);
				$line[] = $this->validators[$validator]['class'];
				$line[] = self::quote((array)$this->validators[$validator]['params']);
				$this->putLine($line);
			}
		}
		$this->putLine('}');
	}
}

/* vim:set tabstop=4: */
