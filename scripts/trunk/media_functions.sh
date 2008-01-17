#!/bin/sh
#
# Audio conversion and burning script
#
# This file contains functions necessary to convert, modify and burn audio
# files (wav or mp3)
#
# $Author: $
# $Date: $
# $Revision: $

# temporary directory to store ISO in
TMP_DIR=~/.tmp
LOCAL_TMP_DIR=
MEDIA_DIR=~/mystuff/media

MODE= 			# variable used to hold action
OUTPUT=			# variable used to hold output mode
INPUT=			# variable used to hold input mode
FILES=			# variable used to hold files to operate on FUNCTION=		# variable used to hold function pointer
ERROR=0

# parameters for decoding, encoding, burning, etc
LAME_DOPTS="--decode"
LAME_EOPTS=
CDPARANOIA_OPTS="-B"
CDRECORD_AOPTS="dev=/dev/hdd -eject speed=8 -pad -audio"
CDRECORD_DOPTS="dev=/dev/hdd -eject speed=8"
MKISOFS_OPTS="-R"

usage() {
	echo 'usage: media_functions.sh -a [convert||burn||rip||play] -i [wav||mp3] -o [wav||mp3]'
	ERROR=1
}

norm() {
	echo "normalize: set ($*)"
	normalize -m $*
	echo "normalize: done"
	echo
}

convertmp3wav() {
	
	for file in $*; do
		mp3_file=$file
		wav_file=`echo $mp3_file | sed 's/\.mp3$/\.wav/'`

		file -b $mp3_file | grep MP3

		if [ $? -eq 0 ]; then
			echo "mp3towav: $mp3_file --> $wav_file (using lame)"
			lame $LAME_DOPTS $mp3_file $wav_file
		else
			echo "mp3towav: $mp3_file not in MP3 format, skipping"
		fi
		echo "mp3towav: done"
		echo
	done

}

convertwavmp3() {

	for file in $*; do
		wav_file=$file
		mp3_file=`echo $wav_file | sed 's/\.wav$/\.mp3/'`

		file -b $wav_file | grep WAV

		if [ $? -eq 0 ]; then
			echo "wavtomp3: $wav_file --> $mp3_file (using lame)"
			lame $LAME_EOPTS $wav_file $mp3_file
		else
			echo "wavtomp3: $wav_file not in WAV format, skippig"
		fi
		echo "wavtomp3: done"
		echo
	done

}

# burn mp3 files into a audio CD (convert from MP3 to WAV)
burnmp3wav() {
local wav_files= 
	# convert files first
	convertmp3wav $*

	# replace file extensions on filelist with mp3
	wav_files=`echo $* | sed -e 's/\.mp3$/\.wav/g'`

	# now burn (must modify file extensions first)
	cdrecord $CDRECORD_AOPTS $wav_files 

}

# burn wav files into a MP3 CD (convert from WAV to MP3)
burnwavmp3() {

	local mp3_files=

	# convert files first
	convertwavmp3 $*

	# delete old tmp.iso if it exists
	[ -e $TMP_DIR/tmp.iso ] && rm -rf $TMP_DIR/tmp.iso
	# clear iso directory
	rm -rf $TMP_DIR/iso/*

	# replace file extensions on filelist with wav
	mp3_files=`echo $* | sed -e 's/\.wav$/\.mp3/g'`

	# copy files to iso directory
	cp $mp3_files $TMP_DIR/iso

	# create iso file
	mkisofs $MKISOFS_OPTS -o $TMP_DIR/tmp.iso $TMP_DIR/iso
	echo "mp3burn: ISO image created"

	echo "mp3burn: Starting burn process"
	# now burn the iso to the CD
	cdrecord $CDRECORD_DOPTS $TMP_DIR/tmp.iso

	echo "mp3burn: done"
	echo

}

# burn wav files into a audio CD (no conversion necessary)
burnwavwav() {

	echo "wavburn: Starting burn process"
	# burn the files
	cdrecord $CDRECORD_AOPTS $*
	echo "wavburn: done"
	
}

# burn mp3 files into a MP3 CD (no conversion necessary)
burnmp3mp3() {

	echo "mp3burn: creating ISO image ($TMP_DIR/tmp.iso)"
	# delete old tmp.iso if it exists
	[ -e $TMP_DIR/tmp.iso ] && rm -rf $TMP_DIR/tmp.iso
	# clear iso directory
	rm -rf $TMP_DIR/iso/*

	# copy files to iso directory
	cp $* $TMP_DIR/iso

	# create iso file
	mkisofs $MKISOFS_OPTS -o $TMP_DIR/tmp.iso $TMP_DIR/iso 
	echo "mp3burn: ISO image created"
	
	echo "mp3burn: Starting burn process"
	# now burn the iso to the CD
	cdrecord $CDRECORD_DOPTS $TMP_DIR/tmp.iso

	echo "mp3burn: done"
	echo

}

rip() {
	LOCAL_TMP_DIR=`date +%d%H%M`
	echo "rip: ripping tracks ($TMP_DIR/$LOCAL_TMP_DIR)"
	mkdir $TMP_DIR/$LOCAL_TMP_DIR
	pushd $TMP_DIR/$LOCAL_TMP_DIR
	echo cdparanoia $CDPARANOIA_OPTS
	cdparanoia $CDPARANOIA_OPTS 
	popd
}

playmp3 () {
	mpg123 $*
}

# sets FILES to playlist order or directory listing
checkdir () {

	local PLAYLIST=
	# check for a playlist file (used for file ordering)
	PLAYLIST=`ls $1/*.pls 2> /dev/null | head -n 1`	# grab only one

	# check to make sure file is not of zero length
	if [ -z $PLAYLIST ]; then
		# sort directory listing 
		FILES=`ls $1/*.$INPUT | sort`
	else
		# parse playlist file
		FILES=`grep 'File' $PLAYLIST | sed -e 's/File[0-9]*=//'`
	fi

}

# verify that some parameter options were specified
if [ $# -eq 0 ]; then
	usage
fi

# loop through parameters and decide what to do
while getopts a:i:o:f: option
do
	case $option in
		a)
			[ $OPTARG = "convert" ] || [ $OPTARG = "burn" ] || \
			[ $OPTARG = "play" ] || [ $OPTARG = "rip" ] && MODE=$OPTARG

			if [ -z $MODE ]; then
				echo "invalid mode specified (valid: convert, burn, play or rip)"
				usage
			fi
			FUNCTION=$MODE
			;;
		i)
			[ $OPTARG = "wav" ] || [ $OPTARG = "mp3" ] && INPUT=$OPTARG

			if [ -z $INPUT ]; then
				echo "invalid input mode specified (valid: wav, mp3)"
				usage
			fi
			FUNCTION=${FUNCTION}${INPUT}
			;;
		o)
			[ $OPTARG = "wav" ] || [ $OPTARG = "mp3" ] && OUTPUT=$OPTARG

			if [ -z $OUTPUT ]; then
				echo "invalid output mode specified (valid: wav, mp3)"
				usage
			fi
			FUNCTION=${FUNCTION}${OUTPUT}
			;;
		f)
			if [ -d $OPTARG ]; then
				checkdir $OPTARG
			elif [ -f $OPTARG ]; then
				FILES=$OPTARG
			else
				echo "file option points to none existent file or directory"
			fi
			;;
		*)
			usage
			;;
	esac
done
$FUNCTION $FILES
