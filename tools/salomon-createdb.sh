#!/bin/bash
if ! whoami | grep -q '^root$' ; then
	echo "$0 debe ejecutar como root"
	exit 1
fi

. /etc/salomon.conf
privatedir=$salomondir/src/private
postgresuser=postgres

if [[ "x$dbserver" == "x" ]] ; then
	echo "salomon-config-dbhost primero"
	exit 2
fi

if [[ "x$dbcreated" == "x" || "$1" == "-f" ]] ; then
	OK=n
	if [[ "$dbserver" == "localhost" ]] ; then
		echo "Debe definir una contraseña que se utilizara en la base de datos."
		echo "SI ESTE ES UN SERVIDOR BKP, UTILICE EL MISMO QUE EN EL SERVIDOR PRINCIPAL."
		echo -n "Es posible generar uno aleatorio. Quieres una contraseña aleatoria"
		read -p "[Y/n]? " OK
	fi
	if [ "$OK" = "n" ]; then
  		read -p "Enter DB password: " -s PASS
  	else
    		PASS=`makepasswd --char 10`
    		echo "El password de DB es: $PASS"
  	fi
  	echo "¡Mantenga la contraseña de la base de datos segura!"

	if [[ "$dbserver" == "localhost" ]]; then
    		su - $postgresuser -c "echo drop user salomonuser | psql -d template1 >/dev/null 2>/dev/null"
    		su - $postgresuser -c "echo create user salomonuser createdb password \'$PASS\'| psql -d template1"
    		su - $postgresuser -c "echo alter user salomonuser createdb password \'$PASS\'| psql -d template1"
  	fi
  	if [[ "x$bdcreated" == "x" ]]; then
    		echo 'bdcreated=y' >> /etc/salomon.conf
  	fi

fi
if ! echo "$*" | grep 'nocreate'; then
	php ${salomondir}/src/private/createdb.php
	chown www-data.www-data ${salomondir}/src/private/conf.php
	chmod 600 ${salomondir}/src/private/conf.php
fi
echo "fabian sierra"
exit 0
