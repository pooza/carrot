{*
一覧画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}

{include file='AdminHeader'}
{include file='ErrorMessages' hide_error_code=true}

<h1>テーブル一覧</h1>

<ul>
{foreach from=$tables item=table}
	<li>
		<a href="/{$module}/Detail?table={$table}">{$table}</a>
		<small>{$table|translate}</small>
	</li>
{foreachelse}
	<li>該当するテーブルがありません。</li>
{/foreach}
</ul>

{if !$errors}
<div>
	{if $useragent.is_msie}
	<a href="/{$module}/DetailAll">PDFをダウンロードする</a>
	{else}
	<a href="/{$module}/DetailAll" target="_blank">PDFに出力する</a>
	{/if}
</div>
{/if}


{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
