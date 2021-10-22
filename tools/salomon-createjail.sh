#!/bin/bash
homejail=/home/salomonjail
[ "$1" != "" ] && homejail=$1
#esto puede llevar algo de tiempo
echo "================================================================================="
echo "============ CREANDO $homejail (esto puede llevar algo de tiempo) ==============="
echo "================================================================================="
#tiene que estar todos los comando sino salir exit
for i in setquota ln id chown chmod dirname useradd mkdir cp rm mv apt-get dpkg uname debootstrap schroot; do
	#El comando which de linux nos muestra dónde se encuentra un ejecutable.
	p=`which $i`
	if [ -x "$p" ]; then
		#-n para que la impresion sea en linea
    	echo -n ""
    else
    	echo comando "$i" no encontrado
    	exit 1
  	fi
done
#para saber si esta como root
if [ "`id -u`" != "0" ]; then
  echo "Debe ejecutar como root"
  exit 1
fi
#verifica si existe el archivo de la version nombre y nombreclave
if [ ! -r /etc/lsb-release ]; then
	#No se encontró el archivo / etc / lsb-release. ¿Es esta una distribución similar a Ubuntu o Debian?
	echo "No se encontró el archivo /etc/lsb-release. ¿Es esta una distribución similar a Ubuntu o Debian?"
	#Si es así, ejecute el comando
    echo "Si es así, ejecute el comando"
    echo ""
    #DISTRIB_CODENAME=WXYZ > /etc/lsb-release
    echo "DISTRIB_CODENAME=WXYZ > /etc/lsb-release"
    echo ""
    #para guardar el nombre de la versión en ese archivo (reemplace WXYZ con el nombre en clave de su distribución)
    echo "para guardar el nombre de la versión en ese archivo (reemplace WXYZ con el nombre en clave de su distribución)"
    exit 1
fi
#para incluir
. /etc/lsb-release
#verifica si ya existe el directorio
if [ -d /salomonjail/ ]; then
	#Parece que ya tienes instalado / salomonjail
    echo "Parece que ya tienes instalado /salomonjail"
    #Si desea reinstalarlo, elimínelo primero (por ejemplo, rm / salomonjail) y luego ejecute /etc/icpc/createsalomonjail.sh
    echo "Si desea reinstalarlo, elimínelo primero (por ejemplo, rm /salomonjail) y luego ejecute /etc/icpc/createsalomonjail.sh"
    exit 1
fi
#si ya existe el fichero y empieza desmontar /sys y /proc
if [ -f $homejail/proc/cpuinfo ]; then
	#Parece que ya ha instalado /salomonjail y el /salomonjail/proc parece estar montado
  	echo "Parece que ya ha instalado /salomonjail y el /salomonjail/proc parece estar montado"
  	chroot $homejail umount /sys >/dev/nul 2>/dev/null
  	#comando umount es para desmontar
  	chroot $homejail umount /proc >/dev/nul 2>/dev/null
  	#Reinicie el sistema para eliminar dicho punto montado
  	echo "Reinicie el sistema para eliminar dicho punto montado"
  	exit 1
fi
#muestra el id del usuario
id -u salomonjail >/dev/null 2>/dev/null
if [ $? != 0 ]; then
	#añade un usuario -m -s bash -d directorio actual -g grupo users nombre salomonjail
 	useradd -m -s /bin/bash -d $homejail -g users salomonjail
 	#crea un archivo con informacion

cat <<EOF > /var/lib/AccountsService/users/salomonjail
[User]
SystemAccount=true
EOF

	#dormir por 1 segundo
 	sleep 1
else
	#El usuario salomonjail ya existe
	echo "El usuario salomonjail ya existe"
  	#si desea continuar, primero elimínelo (por ejemplo, userdel salomonjail) y luego ejecute /etc/icpc/createsalomonjail.sh
  	echo "si desea continuar, primero elimínelo (por ejemplo, userdel salomonjail) y luego ejecute /etc/icpc/createsalomonjail.sh"
  	exit 1
fi


#asignamos a usuario salomonjail 0 de 500MB de almacenamiento 10000 de crear directorios -a para todos
setquota -u salomonjail 0 500000 0 10000 -a
#eliminamos el directorio salomonjail si existe
rm -rf /salomonjail
#creamos un directorio padre -p
mkdir -p $homejail/tmp
#es para asignar todos de lo permisos y da a los usuario escritura y no pueden eliminar de otros usuarios
chmod 1777 $homejail/tmp
#creamos un enlaces simbolicos target nombre
ln -s $homejail /salomonjail
#for i in usr lib var bin sbin etc dev; do
#  [ -d $homejail/$i ] && rm -rf $homejail/$i
#  cp -ar /$i $homejail
#done
#rm -rf $homejail/var/lib/postgres*
#rm -rf $homejail/var/www/*
#mkdir -p $homejail/proc
#mkdir -p $homejail/sys
#es para saber que tipo de arquitectura es 64 0 32 exito 64
uname -m | grep -q 64
if [ $? == 0 ]; then
  	archt=amd64
else
  	archt=i386
fi
#crea un archivo en la ruta dada con los datos establecidos
cat <<FIM > /etc/schroot/chroot.d/salomonjail.conf
[salomonjail]
description=Jail
directory=$homejail
root-users=root
type=directory
users=salomonjail,nobody,root
FIM
#debootstrap es una herramienta que instala un sistema basado en debian
#debootstrap --arch $archt $DISTRIB_CODENAME $homejail
debootstrap $DISTRIB_CODENAME $homejail
if [ $? != 0 ]; then
	#salomonjail fallado no se pudo debootstrap
  	echo "salomonjail fallado no se pudo debootstrap"
  	exit 1
else
	#schroot -l lista los chroot que existen y buscamos que exista salomonjail
	schroot -l | grep -q salomonjail
	if [ $? == 0 ]; then
		#salomonjail instalado con éxito en $ homejail
  		echo "salomonjail instalado con éxito en $homejail"
	else
		#*** algún error ha causado que salomonjail no se instale correctamente - lo intentaré de nuevo con diferentes parámetros
  		echo "*** algún error ha causado que salomonjail no se instale correctamente  -- lo intentaré de nuevo con diferentes parámetros"
  		echo "location=$homejail" >> /etc/schroot/chroot.d/salomonjail.conf
  		debootstrap $DISTRIB_CODENAME $homejail
  		schroot -l | grep -q salomonjail
  		if [ $? == 0 ]; then
  			#*** salomonjail instalado con éxito en $ homejail
    		echo "*** salomonjail instalado con éxito en $homejail"
  		else
  			#*** salomonjail no pudo instalar
    		echo "*** salomonjail no pudo instalar"
    		exit 1
  		fi
	fi
fi


#Poblando
echo "*** Poblando $homejail"
#toda la instruccion escribe en en la ruta dada
cat <<EOF > /home/salomonjail/tmp/populate.sh
#!/bin/bash
# especifica el tipo de sistema de archivos, por lo general, no tiene que ser especificado.
#montaje seleccionara automaticamente los patrones correctos
#sistema de archivos proc
#divice /dev/sda3 como proc
#punto de montaje es /proc para desmontar un umount
mount -t proc proc /proc
#para lenguaje o idioma español
echo "LC_ALL=en_US.UTF-8" > /etc/default/locale
echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen
#ejecutamos los script para actualizar
/usr/sbin/locale-gen
/usr/sbin/update-locale
#actulizamos nuestro repositorio jail
apt-get -y update
#para repositorios
apt-get -y install software-properties-common
add-apt-repository -y ppa:icpc-latam/maratona-linux
apt-get -y update
apt-get -y upgrade
apt-get -y install maratona-linguagens --no-install-recommends --allow-unauthenticated
apt-get -y clean
#desmontamos
umount /proc
EOF
mkdir -p /salomonjail/usr/bin
#copiamos el archivo safeexec a nuestro directorio creado bin
[ -x /usr/bin/safeexec ] && cp -a /usr/bin/safeexec /salomonjail/usr/bin/
cp -f /etc/apt/sources.list $homejail/etc/apt/
chmod 755 /home/salomonjail/tmp/populate.sh

export LC_ALL=en_US.UTF-8
#cambiamos el root a homejail y ejecutamos el script
cd / ; chroot $homejail /tmp/populate.sh
