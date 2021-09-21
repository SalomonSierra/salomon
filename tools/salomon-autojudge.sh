#!/bin/bash
#para saber si estoy ejecutando como root

if ! whoami |grep -q root; then

    #$ 0 debe ejecutarse como root

    echo "$0 must be run as root"

    exit 1

fi

#salomonjail no encontrado. Ejecute salomon-createjail

if [[ ! -e /salomonjail ]]; then

    echo "Salomonjail not found. Please run salomon-createjail"

    exit 1

fi



. /etc/salomon.conf

if [[ "x$dbserver" == "x" && "x$dbcreated" == "x" ]];then
    if grep dbhost $salomondir/src/private/conf.php|grep -q localhost;then

        #No se encontró evidencia de que esta máquina esté ejecutando un SALOMON BD

        echo "It was found no evidence that this machine is running a SALOMON BD"

        #Considere ejecutar 'salomon-config-dbhost' antes

        echo "Please consider running 'salomon-config-dbhost' before"

        exit 2

    fi

fi


echo "termino";
cd $salomondir/src/private

php autojudging.php
