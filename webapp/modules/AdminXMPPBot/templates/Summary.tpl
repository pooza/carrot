{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminXMPPBot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}

<h1>{$action.title}</h1>
<table class="Detail">
	<tr>
		<th>プロセスID</th>
		<td>{$pid|default:'-'}</td>
	</tr>
	<tr>
		<th>ポート</th>
		<td>{$port|default:'-'}</td>
	</tr>
	<tr>
		<th>アプリケーションのJabberID</th>
		<td>{$from|default:'(未設定)'}</td>
	</tr>
	<tr>
		<th>管理者のJabberID</th>
		<td>{$to|default:'(未設定)'}</td>
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
