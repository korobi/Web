#!/bin/bash

echo "** Starting deploy"
if [ "$(hostname)" == "chaos.ellune.net" ]; then
    echo "** Detected chaos.ellune.net, adding deploy key"
    ssh-agent bash -c 'ssh-add /data/a/web/keys/korobi_web; /data/a/capifony/dev.korobi.io/current/deploy.sh'
else
    ./deploy.sh
fi

echo "** Deploy has finished"
