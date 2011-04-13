{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminMemcache
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{include file='AdminHeader'}

<h1>{$action.title}</h1>
<table class="detail">

{foreach from=$server key='key' item='value'}
	<tr>
		<th>{$key}</th>
		<td>{$value}</td>
	</tr>
{foreachelse}
	<tr>
		<th></th>
		<td class="alert">未接続です。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
