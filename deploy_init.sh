#!/bin/bash

echo "** Starting deploy"
if [ "$(hostname)" == "aura.ellune.net" ]; then
    echo "** Detected aura.ellune.net, adding deploy key"
    ssh-agent bash -c 'ssh-add /data/web/_resources/korobi/deploy_web; /data/web/dev.korobi.io/current/deploy.sh'
else
    ./deploy.sh
fi

echo "** Deploy has finished"
