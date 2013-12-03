#!/bin/bash
for i in {0..4}
do
  curl -s "http://10.32.0.10/hc/?action=weather&controller=svc" > /dev/null
  sleep 10m
done
