require 'carrot/environment'
require 'carrot/constants'
require 'fileutils'

module Carrot
  class Deployer
    def self.clean
      if carrot?
        puts "delete #{dest}"
        File.unlink(dest)
      end
    end

    def self.create
      if kariyon?
        puts 'kariyonをアンインストールしてください。'
        exit 1
      end
      unless carrot?
        puts "link #{ROOT_DIR} -> #{dest}"
        File.symlink(ROOT_DIR, dest)
      end
    end

    private
    def self.carrot?
      return File.exist?(File.join(dest, 'www/carrotctl.php'))
    end

    def self.kariyon?
      return File.exist?(File.join(dest, '.kariyon'))
    end

    def self.dest
      return File.join(
        Carrot::Constants.new["BS_APP_DEPLOY_DIR"],
        Carrot::Environment.name
      )
    end
  end
end
