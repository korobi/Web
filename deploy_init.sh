#!/bin/bash
echo "Start deploy"
if [ "$(hostname)" == "iris.solas.io" ]; then
    echo "Detected iris.solas.io, adding deploy key"
    ssh-agent bash -c 'ssh-add /data1/web/deploy_key; /data1/web/dev.korobi.io/deploy.sh'
else
    echo "Just running deploy script"
    ./deploy.sh
fi
echo "End deploy"
