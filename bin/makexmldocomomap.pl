#!/usr/bin/env perl -w
#
# docomoの機種一覧XMLを生成
# CPANモジュール HTTP-MobileAgent から拝借したものを改造
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @link http://search.cpan.org/~kurihara/HTTP-MobileAgent-0.27/

use strict;
use warnings;
use utf8;
use XML::Simple ();
use Encode;
use WWW::MobileCarrierJP::DoCoMo::Display;

do_task(@ARGV);

sub do_task {
    my $dat = WWW::MobileCarrierJP::DoCoMo::Display->scrape;
    my %map;
    for my $phone (@$dat) {
        $phone->{model} =~ s/[+&]//g; # 2011.07.12 tkoishi@b-shock.co.jp 要素名として正規化。
        $map{ uc $phone->{model} } = +{
            width  => $phone->{width},
            height => $phone->{height},
            color  => $phone->{is_color},
            depth  => $phone->{depth},
        };
    }
    output_code( \%map );
}

sub output_code {
    my ($map) = @_;
    my $xml = XML::Simple->new;
    printf <<'TEMPLATE', $xml->XMLout($map);
<?xml version="1.0" ?>
%s
TEMPLATE
}

