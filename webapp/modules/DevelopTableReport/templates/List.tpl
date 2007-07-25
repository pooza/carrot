{*
一覧画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopTableReport
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id: List.tpl 217 2006-09-14 06:58:06Z pooza $
*}

{include file='AdminHeader'}

<h1>テーブル一覧</h1>
<ul>
{foreach from=$tables item=table}
	<li>
		<a href="/?m={$module}&amp;a=Detail&amp;table={$table}">{$table}</a>
		<small>{$table|translate}</small>
	</li>
{foreachelse}
	<li>該当するテーブルがありません。</li>
{/foreach}
</ul>

{if $useragent.is_msie}
<p><a href="/?m={$module}&amp;a=DetailAll">PDFをダウンロードする</a></p>
{else}
<p><a href="/?m={$module}&amp;a=DetailAll" target="_blank">PDFに出力する</a></p>
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
