{*
閲覧画面テンプレート

@package jp.co.b-shock.carrot
@subpackage AdminLog
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}

{include file='AdminHeader'}

<form method="get" action="/">
	<input type="hidden" name="m" value="{$module}" />
	<input type="hidden" name="a" value="{$action}" />
	{html_options name='logfile' options=$logfiles selected=$logfile}
	<input type="submit" value="表示" />
	<a href="/?m=AdminFeed&amp;a=Log"><img src="/images/feed.gif" width="16" height="16" alt="FEED" border="0" /></a>
</form>

<h1>管理ログ</h1>
<table>
	<tr>
		<th width="120">日付</th>
		<th width="150">ホスト</th>
		<th width="120">種類</th>
		<th width="360">内容</th>
	</tr>

{foreach from=$logs item=log}
	<tr>
		<td width="120" {if $log.exception}class="alert"{/if}>{$log.date}</td>
		<td width="150" {if $log.exception}class="alert"{/if}>{$log.host}</td>
		<td width="120" {if $log.exception}class="alert"{/if}>{$log.type}</td>
		<td width="360" {if $log.exception}class="alert"{/if}>{$log.description}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="4">該当するエントリーがありません。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
