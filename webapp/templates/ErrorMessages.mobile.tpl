{*
エラーメッセージ表示 テンプレート

@package jp.co.b-shock.carrot
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{if $errors}
  <p>
    <font size="2" color="red">
      {foreach from=$errors key=code item=message}
        {$code|translate:$error_code_dictionary}:
        {$message|url2link|nl2br}<br>
      {/foreach}
    </font>
  </p>
{/if}
