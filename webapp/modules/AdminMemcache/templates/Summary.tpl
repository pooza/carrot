{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminMemcache
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}

<h1>memcacheの状態</h1>
<table class="Detail">
	<tr>
		<th width="180">プロセスID</th>
		<td width="300">{$pid|default:'-'}</td>
	</tr>
	<tr>
		<th width="180">ホスト</th>
		<td width="300">{$port|default:'-'}</td>
	</tr>
	<tr>
		<th width="180">ポート</th>
		<td width="300">{$port|default:'-'}</td>
	</tr>
</table>

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
