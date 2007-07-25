{*
詳細画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id: Detail.tpl 217 2006-09-14 06:58:06Z pooza $
*}

{include file='AdminHeader'}

<h1>テーブル &quot;{$tablename}&quot;</h1>

<h2>基本情報</h2>
<table>
	<tr>
		<th width="120">属性名</th>
		<th width="330">値</th>
	</tr>

{foreach from=$attributes key=key item=value}
	<tr>
		<td width="120">{$key}</td>
		<td width="330">{$value}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="2">属性がありません。</td>
	</tr>
{/foreach}

</table>

<h2>フィールド</h2>
<table>
	<tr>
		<th width="120">フィールド名</th>
		<th width="180">フィールド型</th>
		<th width="30">NULL</th>
		<th width="120">既定値</th>
		<th width="120">その他制約</th>
	</tr>

{foreach from=$fields item=field}
	<tr>
		<td width="120">
	{if $field.primarykey}
			<strong>{$field.name}</strong><br />
	{else}
			{$field.name}<br />
	{/if}
			<small>{$field.name|translate}</small>
		</td>
		<td width="180">{$field.type}</td>
		<td width="30">{if !$field.notnull}可{/if}</td>
		<td width="120">{$field.default}</td>
		<td width="120">{$field.extra}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="5">フィールド情報がありません。</td>
	</tr>
{/foreach}

</table>

<h2>キー</h2>
<table>
	<tr>
		<th width="120">キー名</th>
		<th width="180">対象フィールド名</th>
		<th width="30">一意</th>
	</tr>

{foreach from=$keys item=key}
	<tr>
		<td width="120">{$key.name}</td>
		<td width="180">

	{foreach from=$key.fields item=field}
		{$field}<br />
	{/foreach}

		</td>
		<td width="30">{if $key.unique}YES{/if}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="3">キー情報がありません。</td>
	</tr>
{/foreach}

</table>

<p><a href="/?m={$module}">テーブル一覧に戻る</a></p>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
