Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty64"

    config.vm.hostname = "korobi"

    config.vm.synced_folder ".", "/home/vagrant/site"

    config.vm.network "private_network", ip: "10.0.5.1"
    # config.vm.network "forwarded_port", guest: 80,    host: 8080  # http
    # config.vm.network "forwarded_port", guest: 443,   host: 4443  # https
    # config.vm.network "forwarded_port", guest: 27017, host: 27717 # mongodb
    # config.vm.network "forwarded_port", guest: 9300,  host: 9301  # ES
    # config.vm.network "forwarded_port", guest: 8080,  host: 8081  # nodejs

    config.vm.synced_folder ".", "/vagrant",
      id: "vagrant-root",
      owner: "vagrant",
      group: "www-data",
      mount_options: ["dmode=775,fmode=664"]

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "playbook.yml"
    end
end
