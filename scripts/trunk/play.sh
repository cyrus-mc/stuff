#!/bin/sh
#
# Media player
#
# This file contains functions necessary for playing media files
#
# $Author: $
# $Date: $
# $Revision: $

# audio player
AUDIO_PLAYER=/usr/bin/mpg123
# video player
VIDEO_PLAYER=/usr/bin/mplayer

PLAYLIST_FILES=

usage() {
	echo "usage: play.sh files|list"
	ERROR=1
}

play_files() {
	local EXTENSION=
	for file in "$@"; do
		# reset program variable
		PROGRAM=
		# determine the extension
		EXTENSION=`echo $file | awk -F . '{ print $2 }'`

		# choose the correct program to run
		case $EXTENSION in
			mp3)
				PROGRAM=$AUDIO_PLAYER
				;;
			mpg|mpeg|avi)
				PROGRAM=$VIDEO_PLAYER
				;;
			pls)
				# recursive call to play files function
				play_files `cat $file`
				;;
			*)
				PROGRAM=
				;;
		esac

		$PROGRAM "$file"
	done
}

case "$@" in
	.)
		play_files `ls .`
		;;
	*)
		play_files "@"
esac
