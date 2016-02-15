#!/bin/bash

dir=$1
host=$2

apt-get -y install apcupsd

#création de la configuration USB
if [ -f "/etc/apcupsd/apcupsd.ori" ]
then
    echo "Fichier de conf déjà modifier"
else
    cp /etc/apcupsd/apcupsd.conf /etc/apcupsd/apcupsd.ori
    sed -i -e "s/^#UPSNAME/UPSNAME\ jeedom/g" /etc/apcupsd/apcupsd.conf
    sed -i -e "s/^UPSCABLE\ smart/UPSCABLE\ usb/g" /etc/apcupsd/apcupsd.conf
    sed -i -e "s/^UPSTYPE\ apcsmart/UPSTYPE\ usb/g" /etc/apcupsd/apcupsd.conf
    sed -i -e "s/^DEVICE\ \/dev\/ttyS0/DEVICE/g" /etc/apcupsd/apcupsd.conf
    echo "Fichier de conf modifié"
fi

#configuration de demarrage d'apcupsd
sed -i -e "s/^ISCONFIGURED=no/ISCONFIGURED=yes/g" /etc/default/apcupsd

#ajout de l'appel Jeedom dans le script d'event
cp /etc/apcupsd/apccontrol /etc/apcupsd/apccontrol.ori
cp $dir/apccontrol /etc/apcupsd/apccontrol


    escaped="$2"

    # escape all backslashes first
    escaped="${escaped//\\/\\\\}"

    # escape slashes
    escaped="${escaped//\//\\/}"

    # escape asterisks
    escaped="${escaped//\*/\\*}"

    # escape full stops
    escaped="${escaped//./\\.}"    

    # escape [ and ]
    escaped="${escaped//\[/\\[}"
    escaped="${escaped//\[/\\]}"

    # escape ^ and $
    escaped="${escaped//^/\\^}"
    escaped="${escaped//\$/\\\$}"

    # remove newlines
    escaped="${escaped//[$'\n']/}"

sed -i -e 's/#URL#/'${escaped}'/g' /etc/apcupsd/apccontrol

service apcupsd restart