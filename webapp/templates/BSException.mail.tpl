{*
エラー文面テンプレート
 
@package org.carrot-framework
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
Subject: [{'app_name'|translate}] {$exception_name}

クライアントホスト: {$clienthost}
ブラウザ: {$useragent}

{$message}