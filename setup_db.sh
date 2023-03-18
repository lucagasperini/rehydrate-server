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
: 'Generate locale for ReHydrate server'
# =============================================================================

USER=$1
PASS=$2
DATABASE="rehydrate"
FILE_SETUP="setup.sql"

echo "Setup postgresql database"
sudo -u postgres createuser $USER
sudo -u postgres createdb $DATABASE
sudo -u postgres psql --command="alter user $USER with encrypted password '$PASS';"
sudo -u postgres psql --command="grant all privileges on database $DATABASE to $USER;"
sudo -u postgres psql -U $USER -d $DATABASE -a -f $FILE_SETUP