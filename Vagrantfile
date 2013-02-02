require 'rubygems'
require 'json'
require 'fileutils'

Vagrant::Config.run do |config|
  cookbook_location = 'remote'
  os = 'ubuntu'
  force_update = true
  gui = false

  cookbooks = JSON.parse(File.open('ingredients.json').read);

  if os == 'arch'
    config.vm.box = "arch64"
    config.vm.box_url = "http://vagrant.pouss.in/archlinux_2012-07-02.box"
  else
    config.vm.box = "precise64"
    config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  end

  if gui
    config.vm.boot_mode = :gui
  end

  config.vm.network :bridged

  config.vm.forward_port 80, 8080
  config.vm.forward_port 6379, 6379

  config.vm.provision :chef_solo do |chef|
     chef.cookbooks_path = 'cookbooks'

    case cookbook_location
    when 'local'
      
    when 'remote'

      if force_update
        FileUtils.rm_rf(chef.cookbooks_path)
      end
      
      unless File.exists?(chef.cookbooks_path)
        Dir.mkdir(chef.cookbooks_path)
      end
     cookbooks.each do |k,v|
        command = "git clone "+v+" #{chef.cookbooks_path}/"
        if k.index(':')
          command += k[0,k.index(':')]
        else
          command += k
        end
          system(command)
          chef.add_recipe(k)
      end
    end

    chef.json = {
        :nginx => {
            :install_method => 'package',
            :default_site_enabled => true,
            :default_site_template => "php-site.erb"
        },
        :mysql => {
            :server_root_password => "1qaz2wsx3edc",
            :server_debian_password => "1qaz2wsx3edc",
            :server_repl_password => "1qaz2wsx3edc"
        }
     }
  end
end
