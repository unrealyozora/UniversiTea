#!/bin/bash

ssh -N -T \
  -o ServerAliveInterval=60 \
  -o ServerAliveCountMax=3 \
  tceron@sshpaolotti.studenti.math.unipd.it \
  -L8022:tecweb:22 \
  -L8080:tecweb:80