#!/bin/bash

dir=$1

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