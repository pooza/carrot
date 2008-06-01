{*
一覧画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}
{include file='ErrorMessages' hide_error_code=true}

<div id="BreadCrumbs">
	<a href="/{$module}/">データベース一覧</a>
	<a href="#">データベース:{$database.name}</a>
</div>

<h1>データベース:{$database.name}</h1>

{if !$errors}
<div>
	{if $useragent.is_msie}
	[<a href="/{$module}/TableAll?database={$database.name}">PDFをダウンロードする</a>]
	{else}
	[<a href="/{$module}/TableAll?database={$database.name}" target="_blank">PDFに出力する</a>]
	{/if}
</div>
{/if}

<table>
	<tr>
		<th width="150">物理テーブル名</th>
		<th width="150">論理テーブル名</th>
	</tr>

{foreach from=$tables item='table'}
	<tr>
		<td width="150">
			<a href="/{$module}/Table?database={$database.name}&table={$table}">{$table}</a>
		</td>
		<td width="150">{$table|translate}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="2" class="alert">該当するテーブルがありません。</td>
	</tr>
{/foreach}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
