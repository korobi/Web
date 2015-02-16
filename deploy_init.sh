#!/bin/bash

echo "** Starting deploy"
if [ "$(hostname)" == "iris.solas.io" ]; then
    echo "** Detected iris, adding deploy key"
    ssh-agent bash -c 'ssh-add /data1/web/deploy_key; /data1/web/dev.korobi.io/deploy.sh'
else
    ./deploy.sh
fi

echo "** Deploy has finished"
