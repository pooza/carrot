{*
絵文字パレットテンプレート
 
@package jp.co.b-shock.carrot
@subpackage AdminUtility
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{const name='app_name_ja'} {$title}</title>
{js_cache name='carrot'}
{css_cache name='carrot'}
</head>
<body>
  <h1>{$action.title}</h1>
  <table>
    {foreach from=$pictograms item='picto'}
      <tr>
        <td width="15" align="center">
          <img src="{$picto.image.url}" width="{$picto.image.width}" height="{$picto.image.height}" alt="{$picto.image.alt}">
        </td>
        <td width="180">
          <a href="javascript:void(CarrotLib.putSmartTag('picto',window.opener.$('{$params.field|default:'body'}'),'{$picto.name}'))">{$picto.name}</a>
        </td>
      </tr>
    {/foreach}
  </table>
</body>
</html>

{* vim: set tabstop=4: *}
