#!/bin/bash
#
# VI wrapper script
#
# Checks to see if file already exists and generates template if not
#
# $Author: $
# $Date: $
# $Revision: $

SCRIPT_LOCATION=~/development/templates

FILENAME=$1
echo Running VI wrapper on file $1

#---- check to see if file exists ----
if [ ! -e "$FILENAME" ]; then
	
	# retrieve file extension
	EXT=`echo $FILENAME |sed s/[a-zA-Z0-9]*.//`

	# check to make sure template exists
	if [ -e "$SCRIPT_LOCATION/template.$EXT" ]; then
		echo "Generating new file from tempalte"
		cp $SCRIPT_LOCATION/template.$EXT ./$FILENAME
	else
		echo "Template file non-existent, creating empty file"
	fi;
fi

vim $FILENAME
