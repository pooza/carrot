# carrot定数
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'yaml'
require 'carrot/environment'

module Carrot
  class Constants
    def initialize
      @constants = {}
      ['carrot', 'package', 'application', Carrot::Environment.name].each do |name|
        begin
          path = File.join(ROOT_DIR, "webapp/config/constant/#{name}.yaml")
          @constants.update(flatten('BS', YAML.load_file(path), '_'))
        rescue
        end
      end
    end

    def [] (name)
      names = []
      names.push("#{name}_#{Carrot::Environment.os}".upcase)
      names.push("#{name}_DEFAULT".upcase)
      names.push(name.upcase)
      names.each do |name|
        return @constants[name] if @constants[name]
      end
    end

    def flatten (prefix, node, glue)
      contents = {}
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
end
