Vagrant::Config.run do |config|
  cookbook_location = 'remote'
  cookbooks = {
    'apt'=>'git@github.com:seagoj/cookbook-apt.git',
    'php5-fpm'=>'git@github.com:seagoj/cookbook-php5-fpm.git',
    'nginx'=>'git@github.com:seagoj/cookbook-nginx.git',
    'nginx::bootstrap'=>'git@github.com:seagoj/cookbook-nginx.git',
    'redis::php'=>'git@github.com:seagoj/cookbook-redis.git',
    'ruby'=>'git@github.com:seagoj/cookbook-ruby.git',
    'sass'=>'git@github.com:seagoj/cookbook-sass.git'
  }
  
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.network :bridged
  config.vm.forward_port 80, 8080
  config.vm.forward_port 6379, 6379

  config.vm.provision :chef_solo do |chef|
     chef.cookbooks_path = 'cookbooks'  

    case cookbook_location
    when 'local'
      
    when 'remote'
      unless File.exists?('cookbooks')
        Dir.mkdir('cookbooks')
      end
     cookbooks.each do |k,v|
        command = 'git clone '+v+' cookbooks/'
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
