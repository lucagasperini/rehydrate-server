#!/bin/bash
# =============================================================================
# Informations
# =============================================================================
# Created By : Luca Gasperini <luca.gasperini@xsoftware.it>
# Created At : 2023/03/16
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
: 'Build depends for ReHydrate server'
# =============================================================================

if [ ! -d "src/chart-js" ]; then
    echo "Building chart.js"
    cd 3rd-party/Chart-js
    npm install && npm run build
    cd ../../
    mkdir src/chart-js
    cp -r 3rd-party/Chart-js/dist/* src/chart-js/
fi

if [ ! -d "src/sounds" ]; then
    echo "Adding Notifications OGG"
    cp -r 3rd-party/Notifications/OGG src/sounds
fi