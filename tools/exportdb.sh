#!/bin/bash
# exportdb.sh
# chmod 777 backupsDB.sh
##########################

#VARIABLES DE CONEXION
localhost='localhost'
puerto='5432'
pass='evelyn123'
user='salomonuser'
DB='salomondb'


#variable sistema
New=0
Pgpass=~/.pgpass
Dia=$(date +%Y%m%d)
Hora=$(date +%H%M%S)
Dir='/var/www/salomon/tools'
File=DB_backup_$Dia$Hora.sql



while [ $New -eq 0 ]; do
# verificamos si existe la carpeta de backups

if [ -d $Dir ]; then
        #echo "RUTA DE RESPALDO DB "$Dir
# Verificamos si existe el archivo clave
        if [ -f $Pgpass ];then
                # metodos .SQL Linux
                        pg_dump -d $DB -h $localhost -p $puerto -U $user > $Dir/$File
                        New=1
                echo $File
                #echo "SE GENERO EL ARCHIVO " $Dir/$File " CON EL RESPALDO DE LA BASE DE DATOS"
        else
# generamos el archivo con la clave pgpass
#host:puerto:basededatos:usuario:contraseña
# metodo linux
#echo "192.168.0.1:5432:mibase:miusuario:micontraseña" >> ~/.pgpass
# metodo win
# 192.168.0.1:5432:mibase:miusuario:micontraseña > c:\documents and settings\(usuario que correrá la tarea)\datos de programa\postgresq\pgpass.conf

                echo $localhost:$puerto:$DB:$user:$pass > $Pgpass
                chmod 0600 $Pgpass
                #echo " SE GENERO PARAMETRO DE ACCESO  "
        fi
else

#Creamos la carpeta de backups
        sudo mkdir $Dir
        sudo chmod 777 -R $Dir
        #echo "SE CREA CARPETA DE BACKUPS"
fi
done
