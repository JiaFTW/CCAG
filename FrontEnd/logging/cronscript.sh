#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

while true; do
scp "$SCRIPT_DIR/logs/"*.txt deploy@192.168.193.69:~/Logs/
sleep 5
done