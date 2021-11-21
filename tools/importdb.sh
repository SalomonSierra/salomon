#!/bin/bash
#VARIABLES DE CONEXION
localhost='localhost'
puerto='5432'
pass='evelyn123'
user='salomonuser'
DB='salomondb'

#variable sistema
New=0
Pgpass=~/.pgpass
if [ -z $1 ] ; then
    echo "Error en la ejecución! Falta el parámetro.."
    echo "Ejemplo ejecución:"
    echo
    echo "  $ sh importdb.sh archivo.sql"
    echo
    exit
fi

while [ $New -eq 0 ]; do
	if [ -f $Pgpass ];then
		psql -d $DB -h $localhost -p $puerto -U $user < $1
		#psql -U salomonuser -W -h localhost salomondb < $1
                New=1
                echo "SE IMPORTO EL BACKUP"
        else
                echo $localhost:$puerto:$DB:$user:$pass > $Pgpass
                chmod 0600 $Pgpass
                echo " SE GENERO PARAMETRO DE ACCESO "
        fi
done
