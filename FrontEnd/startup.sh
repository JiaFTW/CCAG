#!/bin/bash
sleep 2

export DISPLAY=:0
export XDG_RUNTIME_DIR=/run/user/$(id -u)

systemctl --user start CCAG.service