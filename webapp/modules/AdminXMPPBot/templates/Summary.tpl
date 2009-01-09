{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminXMPPBot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}

<h1>XMPPBotの状態</h1>
<table class="Detail">
	<tr>
		<th width="180">プロセスID</th>
		<td width="300">{$pid|default:'-'}</td>
	</tr>
	<tr>
		<th width="180">ポート</th>
		<td width="300">{$port|default:'-'}</td>
	</tr>
	<tr>
		<th width="180">アプリケーションのJabberID</th>
		<td width="300">{$from|default:'(未設定)'}</td>
	</tr>
	<tr>
		<th width="180">管理者のJabberID</th>
		<td width="300">{$to|default:'(未設定)'}</td>
	</tr>
</table>

{if !$errors}
{if $pid}
<h1>管理者へIM送信</h1>
<form method="post" action="/{$module.name}/Send">
	<input type="text" size="40" name="command" />
	<input type="submit" value="送信" /><br />
	<a href='/{$module.name}/Stop'>XMPPBotを停止</a>
</form>
{else}
<p class='alert'>
	XMPPBotは起動していません。
	<a href='/{$module.name}/Start'>起動</a>
</p>
{/if}
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
