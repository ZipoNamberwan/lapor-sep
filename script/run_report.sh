#!/bin/bash
{
    echo "Script started at $(date)"
    cd /home/bps3500/lapor-sep || { echo "Failed to cd to /home/bps3500/path/to/lapor-sep"; exit 1; }
    echo "Current directory: $(pwd)"
    echo "Running docker command..."
    sudo /usr/bin/docker exec -i lapor-sep-app-1 php artisan report:generate
    sudo /usr/bin/docker exec -i lapor-sep-app-1 php artisan report-edcod:generate
    if [ $? -ne 0 ]; then
        echo "Docker command failed"
    else
        echo "Docker command succeeded"
    fi
    echo "Script finished at $(date)"
} >> /home/bps3500/script/cron.log 2>&1
