{*
一覧画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}

{include file='AdminHeader'}

<h1>テーブル一覧</h1>
<ul>
{foreach from=$tables item=table}
	<li>
		<a href="/{$module}/Detail&amp;table={$table}">{$table}</a>
		<small>{$table|translate}</small>
	</li>
{foreachelse}
	<li>該当するテーブルがありません。</li>
{/foreach}
</ul>

{if $useragent.is_msie}
<p><a href="/{$module}/DetailAll">PDFをダウンロードする</a></p>
{else}
<p><a href="/{$module}/DetailAll" target="_blank">PDFに出力する</a></p>
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
