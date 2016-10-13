#!/usr/bin/env ruby
# encoding: utf-8

# carrot定数
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'yaml'
require 'carrot/environment'

class Constants
  def initialize
    @constants = Hash.new
    ['carrot', 'package', 'application', Environment.name].each do |name|
      begin
        path = ROOT_DIR + '/webapp/config/constant/' + name + '.yaml';
        @constants.update(flatten('BS', YAML.load_file(path), '_'))
      rescue
      end
    end
  end

  def [] (name)
    names = []
    names.push((name + '_' + Environment.os).upcase)
    names.push((name + '_DEFAULT').upcase)
    names.push(name.upcase)
    names.each do |name|
      if @constants[name] != nil
        return @constants[name]
      end
    end
  end

  def flatten (prefix, node, glue)
    contents = Hash.new
    if node.instance_of?(Hash)
      node.each do |key, value|
        key = prefix + glue + key
        contents.update(flatten(key, value, glue))
      end
    else
      contents[prefix.upcase] = node
    end
    return contents
  end
end
