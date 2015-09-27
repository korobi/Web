# options for Vagrant provisioning; do NOT commit personal changes to these
provisioning = {

  # if enabled, the base Ubuntu box will be used and Ansible will provision it;
  # when disabled, the custom Korobi box will be used and no provisioning will
  # happen; this is useful it you find yourself using `vagrant destroy -f;
  # vagrant up` very often, or if you're on Windows, which doesn't support
  # Ansible
  enabled: true,

  # if true, Ansible's verbose mode (level 'vv') will be used; does nothing if
  # provisioning is disabled
  debug: false
  
}

Vagrant.configure(2) do |config|
  if provisioning[:enabled]
    config.vm.box = 'ubuntu/trusty64'
  else
    config.vm.box = 'bionicrm/korobi-web'
  end

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

  if provisioning[:enabled]
    config.vm.provision :ansible do |ansible|
      if provisioning[:debug]
        ansible.verbose = 'vv'
      end

      ansible.playbook = 'playbook.yml'
    end
  end
end
