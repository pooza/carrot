<?php
/**
 * memcachedの死活監視用エントリーポイント
 *
 * carrotの機能は一切使わず、標準関数のみで実装。
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */

header('Content-Type: text/plain');
$memcache = new Memcache;
if (@$memcache->connect('unix:///tmp/memcached.sock', 0)) {
	print 'OK';
} else {
	http_response_code(500);
	print 'NG';
}

