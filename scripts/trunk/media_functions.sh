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
FILES=			# variable used to hold files to operate on 
FUNCTION=		# variable used to hold function pointer
ERROR=0

# parameters for decoding, encoding, ripping and burning
DEC_PROG="mplayer"
DEC_WAV_OPTS="-ao pcm:waveheader:file="

ENC_MP3_PROG="lame"
ENC_MP3_OPTS="-V 0 -h -b 192 --vbr-new"

ENC_OGG_PROG="oggenc"
ENC_OGG_OPTS="-b 192 -q 5"

PLAY_PROG="mplayer"
PLAY_PROG_OPTS=""

CDPARANOIA_OPTS="-B"
CDRECORD_AOPTS="dev=/dev/hdd -eject speed=8 -pad -audio"
CDRECORD_DOPTS="dev=/dev/hdd -eject speed=8"
MKISOFS_OPTS="-R"

usage() {
	echo 'usage: media_functions.sh -a [convert||burn||rip||play] -o [wav||mp3||ogg]'
	ERROR=1
}

# function used to check if helper programs are available in path
is_installed() {
	PROGRAM=$1

	PATHNAME=`type $PROGRAM 2> /dev/null`

	if [ -z "$PATHNAME" ]; then
		echo "cannot locate $PROGRAM in path - script will now exit"
		exit 1;
	fi
}

# function used to normalize sound files
norm() {

	printf "normalize: set ($*)\n\n"
	normalize -m $*
	printf "\nnormalize: done\n"

}

# convert file from any format to WAVE audio
convert_to_wav() {

	# check if file is already in WAVE audio format
	file -b $file | grep -i "wave audio" &> /dev/null
	if [ $? -eq 0 ]; then
		printf "convert_to_wav: %s is already in the correct format\n\n" $file
		cp $1 $2
	else
		printf "convert_to_wav: %s --> %s (using %s)\n\n" $1 $2 $DEC_PROG
		${DEC_PROG} ${DEC_WAV_OPTS}${2} $1
	fi

	printf "convert_to_wav: normalizing file\n\n"
	norm $2

	printf "\nconvert_to_wav: done\n\n"

}

# convert file from WAV format to specified format
#
# parameters:
#	wav_filename: filename of the WAV file to process
#	output_filename_ext: extension of the output filename
#	encoding_program: program to use to encode the file
#	encoding_options: options to pass to encoding program
convert_from_wav() {

	converted_filename=`basename $1 .wav`.$2

	# check if encoding program is available
	is_installed $3

	# check that the file is in correct WAVE audio format
	file -b $1 | grep -i "wave audio" &> /dev/null
	if [ $? -eq 0 ]; then
			printf "convert_%s: %s --> %s (using %s)\n\n" $2 $1 $converted_filename $3
			$3 $4 $1 $converted_filename
		else
			printf "convert_%s: %s not in correct WAVE audio format" $2 $1
	fi

}

convert_mp3() {

	for file in $*; do
		wav_filename=`echo $file | sed -e 's/\..*$/\.wav/g'`
		convert_to_wav $file $wav_filename
		convert_from_wav $wav_filename 'mp3' "$ENC_MP3_PROG" "$ENC_MP3_OPTS"
	done

	printf "\nconvert_mp3: done\n\n"

}

convert_ogg() {

	for file in $*; do
		wav_filename=`echo $file | sed -e 's/\..*$/\.wav/g'`
		convert_to_wav $file $wav_filename
		convert_from_wav $wav_filename 'ogg' "$ENC_OGG_PROG" "$END_OGG_OPTS"
	done

	printf "\nconvert_ogg: done\n\n"

}

burnmp3wav() {

local wav_files= 
	# convert files first
	convertmp3wav $*

	# replace file extensions on filelist with mp3
	wav_files=`echo $* | sed -e 's/\.mp3$/\.wav/g'`

	# now burn (must modify file extensions first)
	cdrecord $CDRECORD_AOPTS $wav_files 

}

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

burnwavwav() {

	echo "wavburn: Starting burn process"
	# burn the files
	cdrecord $CDRECORD_AOPTS $*
	echo "wavburn: done"
	
}

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

# verify that some parameter options were specified
if [ $# -eq 0 ]; then
	usage
fi

# loop through parameters and decide what to do
while getopts a:o: option
do
	case $option in
		a) 
			[ $OPTARG = "convert" ] || [ $OPTARG = "burn" ] || \
			[ $OPTARG = "play" ] || [ $OPTARG = "rip" ] && MODE=$OPTARG

			if [ -z $MODE ]; then
				usage
			fi
			FUNCTION=$MODE
			;;
		o)
			OUTPUT=$OPTARG
			FUNCTION="${FUNCTION}_${OUTPUT}"
			;;
		*)
			usage
			;;
	esac
done

# the rest of the command line should be the files to operate on
shift `expr $OPTIND - 1`
# loop over all the files and see if any directories were specified
for file in $*; do
	if [ -d $file ]; then
		FILES="$FILES `ls $file/* | sort`"
	elif [ -f $file ]; then
		FILES="$FILES $file"
	else
		echo "file option points to none existent file or directory"
	fi
done

$FUNCTION "$FILES"
