{*
要約画面テンプレート

@package jp.co.b-shock.carrot
@subpackage AdminXMPPBot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id: Summary.tpl 364 2007-07-24 14:52:08Z pooza $
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
		<td width="300">{$from}</td>
	</tr>
	<tr>
		<th width="180">管理者のJabberID</th>
		<td width="300">{$to}</td>
	</tr>
</table>

{if $pid}
<h1>管理者へIM送信</h1>
<form method="post" action="/">
	<input type="hidden" name="m" value="{$module}" />
	<input type="hidden" name="a" value="Send" />
	<input type="text" size="40" name="command" />
	<input type="submit" value="送信" /><br />
	<a href='/?m={$module}&a=Stop'>XMPPBotを停止</a>
</form>
{else}
<p class='alert'>
	XMPPBotは起動していません。
	<a href='/?m={$module}&amp;a=Start'>起動</a>
</p>
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
