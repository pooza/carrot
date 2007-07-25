<?php

function protector_sanitize( $arr )
{
    if ( is_array( $arr ) ) {
        // 変数汚染攻撃対策
        if ( ! empty( $arr['_SESSION'] ) || ! empty( $arr['_COOKIE'] ) || ! empty( $arr['_SERVER'] ) || ! empty( $arr['_ENV'] ) || ! empty( $arr['_FILES'] ) || ! empty( $arr['GLOBALS'] ) ) exit ;
        return array_map( 'protector_sanitize', $arr ) ;
    }

    // ヌルバイト攻撃対策
    return str_replace( "\0", "", $arr ) ;
}

$_GET    = protector_sanitize( $_GET ) ;
$_POST   = protector_sanitize( $_POST ) ;
$_COOKIE = protector_sanitize( $_COOKIE ) ;

// セッション鍵を利用したXSS, HTTPレスポンス分割対策
// POST や URIに埋め込まれたセッション鍵はあえて無視する
ini_set( 'session.use_only_cookies' , 1 ) ;
// セッション固定攻撃を避けるためには、ここより下の３行をコメントアウトする。
if ( ! empty( $_GET[ session_name() ] ) && empty( $_COOKIE[ session_name() ] ) && ! preg_match( '/[^0-9A-Za-z,-]/' , $_GET[ session_name() ] ) ) {
    $_COOKIE[ session_name() ] = $_GET[ session_name() ] ;
}

// PHP_SELFを利用したXSSおよびHTTPレスポンス分割攻撃への対策
$_SERVER['PHP_SELF'] = strtr( @$_SERVER['PHP_SELF'] , array('<'=>'%3C','>'=>'%3E',"'"=>'%27','"'=>'%22' , "\r" => '' , "\n" => '' ) ) ;
$_SERVER['PATH_INFO'] = strtr( @$_SERVER['PATH_INFO'] , array('<'=>'%3C','>'=>'%3E',"'"=>'%27','"'=>'%22' , "\r" => '' , "\n" => '' ) ) ;

?>
