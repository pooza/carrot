{*
一覧画面テンプレート

@package org.carrot-framework
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}
{include file='ErrorMessages' hide_error_code=true}

<div id="BreadCrumbs">
	<a href="/{$module.name}/">データベース一覧</a>
	<a href="#">データベース:{$database.name}</a>
</div>

<h1>データベース:{$database.name}</h1>

<h2>基本情報</h2>
<table class="Detail">

{foreach from=$database.info key=key item=value}
	<tr>
		<th>{$key}</th>
		<td>{$value}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="2">基本情報がありません。</td>
	</tr>
{/foreach}

</table>

<h2>テーブル</h2>
<table>
	<tr>
		<th width="120">物理テーブル名</th>
		<th width="120">論理テーブル名</th>
	</tr>

{foreach from=$database.tables item='table'}
	<tr>
		<td width="120">
			<a href="/{$module.name}/Table?database={$database.name}&table={$table}">{$table}</a>
		</td>
		<td width="120">{$table|translate}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="2" class="alert">該当するテーブルがありません。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
