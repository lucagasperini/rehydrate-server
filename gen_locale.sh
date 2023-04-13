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


#TODO: Get all *.php from dir automatically
SOURCES=(
        "index.php"
        "form_login.php"
        "form_home.php"
)

#TODO: Add as argument language/languages
LANGUAGES=(
        "it_IT"
        "en_US"
)

AUTHOR="Luca Gasperini <luca.gasperini@xsoftware.it>"
PROJECT="ReHydrate"
CREATION_DATE="2023-03-14 04:34+0100"

#TODO: Add --help
if [ -z $1 ]; then
        BASE_SOURCE_DIR="$(dirname $(realpath $0))/src"
else
        BASE_SOURCE_DIR="$1"
fi

if [ -z $2 ]; then
        BASE_LOCALE_DIR="$(dirname $(realpath $0))/locale"
else
        BASE_LOCALE_DIR="$2"
fi


LOCALE_DOMAIN="messages"

#TODO: Check if those program are installed
XGETTEXT_BIN="/usr/bin/xgettext"
MSGFMT_BIN="/usr/bin/msgfmt"



for source in "${SOURCES[@]}"
do
        for lang in "${LANGUAGES[@]}"
        do
                output_dir="${BASE_LOCALE_DIR}/${lang}/LC_MESSAGES"
                source_path="${BASE_SOURCE_DIR}/${source}"
                po_path="${output_dir}/${LOCALE_DOMAIN}.po"
                po_tmp_path="${output_dir}/${LOCALE_DOMAIN}.po.tmp"
                mo_path="${output_dir}/${LOCALE_DOMAIN}.mo"

                echo "Extracting strings from ${source_path} to ${po_path} "
                $XGETTEXT_BIN -n $source_path --output-dir="$output_dir/" -j --no-location

                echo "Updating copyright to ${po_path} "
                printed_datetime="$(date +'%F %H:%M%z')"
                printed_year="$(date +'%Y')"
                echo "# ReHydrate translations.
# Copyright (C) $printed_year $PROJECT's $AUTHOR
# This file is distributed under the same license as the $PROJECT package.
# $AUTHOR, $printed_year.
#
#, fuzzy
msgid \"\"
msgstr \"\"
\"Project-Id-Version: $PROJECT\\n\"
\"Report-Msgid-Bugs-To: \\n\"
\"POT-Creation-Date: $CREATION_DATE\\n\"
\"PO-Revision-Date: $printed_datetime\\n\"
\"Last-Translator: $AUTHOR\\n\"
\"Language-Team: $AUTHOR\\n\"
\"Language: $lang\\n\"
\"MIME-Version: 1.0\\n\"
\"Content-Type: text/plain; charset=UTF-8\\n\"
\"Content-Transfer-Encoding: 8bit\\n\"
" > $po_tmp_path
                tail -n+20 $po_path >> $po_tmp_path
                mv $po_tmp_path $po_path

                echo "Building binary from $po_path to $mo_path"
                $MSGFMT_BIN $po_path -o $mo_path
        done
done