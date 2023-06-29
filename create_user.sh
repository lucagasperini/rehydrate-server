#!/bin/bash
# =============================================================================
# Informations
# =============================================================================
# Created By : Luca Gasperini <luca.gasperini@xsoftware.it>
# Created At : 2023/03/14
# Project    : ReHydrate
# Repository :
# Coding     : UTF-8
# =============================================================================
# License
# =============================================================================
: '
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
  
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
'
# =============================================================================
# Description
# =============================================================================
: 'Create user of ReHydrate server'
# =============================================================================

#TODO: Add database as argument
DATABASE="rehydrate"
TIMEZONE="Europe/Rome"
WATER_DAILY="2500"

if [ -z $1 ] || [ -z $2 ]; then
        echo "Cannot add user without username and password!"
        exit 1
fi

# Use values from https://www.php.net/manual/en/timezones.php
if [ -z $3 ]; then
        echo "Not provided a timezone, using default: $TIMEZONE"
else
        TIMEZONE=$3
fi

USER_NAME=$1
#TODO: Check if php and postgresql is installed
USER_PASS=$(php -r "echo hash(\"sha256\", \"$2\");")

sudo -u postgres psql -d $DATABASE --command="INSERT INTO account VALUES ('$USER_NAME', '$USER_PASS',$WATER_DAILY, $TIMEZONE);"
