debug = true

Vagrant.configure(2) do |config|
    config.vm.box = 'ubuntu/trusty64'
    config.vm.hostname = 'korobi'

    config.vm.network :private_network, ip: '10.0.5.6'

    config.vm.synced_folder '.', '/home/vagrant/site',
      id: 'vagrant-root',
      owner: 'vagrant',
      group: 'www-data',
      mount_options: ['dmode=775,fmode=664']

    config.vm.provider :virtualbox do |v|
      v.name = 'korobi_web_vagrant'
      v.memory = 1024
    end

    config.vm.provision :ansible do |ansible|
      if debug
        ansible.verbose = 'vv'
      end

      ansible.playbook = 'playbook.yml'
    end
end
