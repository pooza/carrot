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
	const EMPTY_VALIDATOR = '__empty';
	const FILE_VALIDATOR = '__file';

	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$config = $file->getResult();

		$this->setMethods($config['methods']);
		$this->setNames($config['names']);
		$this->setValidators($config);

		$this->putLine('$manager = BSValidateManager::getInstance();');
		foreach (BSWebRequest::getMethodNames() as $method) {
			$this->putMethod($method);
		}

		return $this->getBody();
	}

	private function setMethods ($methods) {
		$this->fields = new BSArray;
		foreach ($methods as $method => $fields) {
			$method = strtoupper($method);
			$this->fields[$method] = new BSArray;
			foreach ($fields as $field) {
				$this->fields[$method][$field] = new BSArray;
				$this->fields[$method][$field]['validators'] = new BSArray;
			}
		}
	}

	private function setNames ($names) {
		$this->validators = new BSArray;
		foreach (BSWebRequest::getMethodNames() as $method) {
			foreach ($names as $name => $value) {
				if ($this->fields[$method][$name]) {
					$this->fields[$method][$name]->setParameters($value);
				}
				if ($validators = $this->fields[$method][$name]['validators']) {
					foreach ($validators as $validator) {
						$this->validators[$validator] = new BSArray;
					}
				}
			}
		}
	}

	private function setValidators ($config) {
		foreach ($this->validators as $name => $values) {
			if (!isset($config)) {
				throw new BSConfigException('バリデータ "%s" が未定義です。', $name);
			}
			$this->validators[$name] = new BSArray($config[$name]);
		}
		$this->validators[self::EMPTY_VALIDATOR] = new BSArray;
		$this->validators[self::EMPTY_VALIDATOR]['class'] = 'BSEmptyValidator';
		$this->validators[self::FILE_VALIDATOR] = new BSArray;
		$this->validators[self::FILE_VALIDATOR]['class'] = 'BSFileValidator';
	}

	private function putMethod ($method) {
		if (!$this->fields[$method]) {
			return;
		}

		$this->putLine(
			sprintf('if (BSRequest::getInstance()->getMethod() == BSRequest::%s) {', $method)
		);
		foreach ($this->fields[$method] as $name => $field) {
			$field = new BSArray($field);
			if ($field['required']) {
				$this->putValidator($name, self::EMPTY_VALIDATOR);
			}
			if ($field['file']) {
				$this->putValidator($name, self::FILE_VALIDATOR);
			}
			foreach ($field['validators'] as $info) {
				$this->putValidator($name, $info);
			}
		}
		$this->putLine('}');
	}

	private function putValidator ($name, $validator) {
		$line = new BSStringFormat('  $manager->register(%s, new %s(%s));');
		$line[] = self::quote($name);
		$line[] = $this->validators[$validator]['class'];
		$line[] = self::parseParameters($this->validators[$validator]['params']);
		$this->putLine($line);
	}
}

/* vim:set tabstop=4: */
