{*
閲覧画面テンプレート

@package org.carrot-framework
@subpackage AdminLog
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}

<form method="get" action="/{$module.name}/{$action.name}">
	{html_options name='date' options=$dates selected=$params.date}
	<input type="submit" value="表示" />
	<a href="/AdminFeed/Log"><img src="/carrotlib/images/feed.gif" width="16" height="16" alt="FEED" border="0" /></a>
</form>

<h1>管理ログ</h1>
<table>
	<tr>
		<th width="120">日付</th>
		<th width="150">ホスト</th>
		<th width="180">種類</th>
		<th width="360">内容</th>
	</tr>

{foreach from=$entries item=log}
	<tr {if $log.exception}class="alert"{/if}>
		<td width="120">{$log.date}</td>
		<td width="150">{$log.remote_host}</td>
		<td width="180">{$log.priority}</td>
		<td width="360">{$log.message}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="4">該当するエントリーがありません。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
