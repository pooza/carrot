{*
データベース一覧画面テンプレート

@package org.carrot-framework
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}
{include file='ErrorMessages' hide_error_code=true}

<div id="BreadCrumbs">
	<a href="#">データベース一覧</a>
</div>

<h1>データベース一覧</h1>

<table>
	<tr>
		<th width="90">接続名</th>
		<th width="60">DBMS</th>
		<th width="300">DSN</th>
	</tr>

{foreach from=$databases item='database'}
	<tr>
		<td width="90">
			<a href="/{$module.name}/Database?database={$database.name}">{$database.name}</a>
		</td>
		<td width="60">{$database.dbms}</td>
		<td width="300">{$database.dsn}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="3" class="alert">該当するデータベースがありません。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
