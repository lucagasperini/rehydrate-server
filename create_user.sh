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

DATABASE="rehydrate"

USER_NAME=$1
USER_PASS=$(php -r "echo hash(\"sha256\", \"$2\");")
WATER_DAILY="2500"

sudo -u postgres psql -d $DATABASE --command="INSERT INTO account VALUES ('$USER_NAME', '$USER_PASS',$WATER_DAILY);"