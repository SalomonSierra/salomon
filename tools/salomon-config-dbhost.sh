}#!/bin/bash
#debe ejecutar como root
if ! whoami | grep -q '^root$' ; then
  echo "$0 must be run as root"
  exit 1
fi

if (( $# != 1 )); then
    #uso:
  echo "Usage:"
  #$0 bdserver-ip | localhost
  echo "  $0 bdserver-ip|localhost"
  echo
  #El parámetro debe ser localhost si postgres está ejecutando localhost, o
  echo "Parameter should be localhost if the postgres is running localhost, or"
  #la dirección IP del postgres
  echo "the IP address of the postgres"
  exit 0
fi
#localhost
bdservernew=$1

. /etc/boca.conf

privatedir=$bocadir/src/private

CHANGE=n
if [[ "x$bdserver" == "x" ]]; then
    #añadimos
  echo "bdserver=$bdservernew" >> /etc/boca.conf
else
  CHANGE=y
  VV="$(grep -v '^bdserver=' /etc/boca.conf)"
  printf "bdserver=$bdservernew\n$VV\n" > /etc/boca.conf
fi

bdserver=$bdservernew

#update conf.php
# PASSWD should be environment defined. While installing boca-common package
# this variable will be set
PASS=$PASSWD
if [[ "x$PASS" == "x" ]]; then
  read -p "Enter DB password: " -s PASS
fi
PASSK=`makepasswd --chars 20`
awk -v boca="$bdserver" -v pass="$PASS" -v passk="$PASSK" '{ if(index($0,"[\"dbpass\"]")>0) \
  print "$conf[\"dbpass\"]=\"" pass "\";"; \
  else if(index($0,"[\"dbhost\"]")>0) print "$conf[\"dbhost\"]=\"" boca "\";"; \
  else if(index($0,"[\"dbsuperpass\"]")>0) print "$conf[\"dbsuperpass\"]=\"" pass "\";"; \
  else if(index($0,"[\"key\"]")>0) print "$conf[\"key\"]=\"" passk "\";"; else print $0; }' \
  < $privatedir/conf.php > $privatedir/conf.php1
mv -f $privatedir/conf.php1 $privatedir/conf.php

chown www-data.www-data $privatedir/conf.php
chmod 600 $privatedir/conf.php

exit 0
