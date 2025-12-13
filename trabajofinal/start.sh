#! /bin/bash
echo $RANDOM | md5sum  | awk '{print "ssi{"$1"}"}' > machine/flag/user.txt
echo $RANDOM | md5sum  | awk '{print "ssi{"$1"}"}' > machine/flag/root.txt
echo $RANDOM | md5sum  | awk '{print "ssi{"$1"}"}' > machine/flag/flag.txt

docker compose -p web-lab up -d --build