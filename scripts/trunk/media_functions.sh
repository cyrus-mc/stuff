#!/bin/sh
#
# Audio conversion and burning script
#
# This file contains functions necessary to convert, modify and burn audio
# files
#
# TODO:
#	- fix WAV file renaming when there are multiple extensions (file.ext1.ext2)
#	- oggenc outputs to directory where temporary files are, not working dir
#	- oggenc outputs error about missing FILENAME.ogg file
#	- complete burn_mp3 and burn_wav and burn_ogg functions
#
# $Author: $
# $Date: $
# $Revision: $

# temporary directory to intermediary files
TMP_DIR=

# parameters for decoding, encoding, ripping and burning
DEC_PROG="mplayer"
DEC_WAV_OPTS="-ao pcm:waveheader:file="

ENC_MP3_PROG="lame"
ENC_MP3_OPTS="-V 0 -h -b 192 --vbr-new"

ENC_OGG_PROG="oggenc"
ENC_OGG_OPTS="-b 192 -q 5"

PLAY_PROG="mplayer"
PLAY_PROG_OPTS=""

ISO_PROG="mkisofs"
ISO_PROG_OPTS="-r -f -o"

RIP_PROG="cdparanoia"
RIP_PROG_OPTS="-B 1"

NORM_PROG="normalize"
NORM_PROG_OPTS="-b"

CDBURN_PROG="cdrecord"
CDBURN_PROG_DOPTS="dev=/dev/dvd -eject speed=48"
CDBURN_PROG_AOPTS="dev=/dev/dvd -eject speed=48 -pad -audio"

MODE=          # variable used to hold action
OUTPUT=        # variable used to hold output mode
FILES=         # variable used to hold files to operate on 
FUNCTION=      # variable used to hold function pointer
ERROR=0

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

	# check if normalizing program is available in path
	is_installed $NORM_PROG

	echo -e "normalize: set ($*)\n"
	$NORM_PROG $NORM_PROG_OPTS $*
	echo -e "\nnormalize: done\n"

}

# convert file from any format to WAVE audio
#
# parameters:
#	input_filename: filename of file to convert
#	output_filename: output filename (most likely .wav extension)
convert_to_wav() {

	# check if decoding program is available in path
	is_installed $DEC_PROG

	# check if file is already in WAVE audio format
	file -b $1 | grep -i "wave audio" &> /dev/null
	if [ $? -eq 0 ]; then
		printf "convert_to_wav: %s is already in the correct format\n\n" $file
		cp $1 $2
	else
		printf "convert_to_wav: %s --> %s (using %s)\n\n" $1 $2 $DEC_PROG
		${DEC_PROG} ${DEC_WAV_OPTS}${2} $1
	fi

	norm $2

	echo -e "\nconvert_to_wav: done\n"

}

# convert file from WAV format to specified format
#
# parameters:
#	wav_filename: filename of the WAV file to process
#	output_filename_ext: extension of the output filename
#	encoding_program: program to use to encode the file
#	encoding_options: options to pass to encoding program
convert_from_wav() {

	# check if encoding program is available in path
	is_installed $3

	converted_filename=`basename $1 .wav`.$2

	# check that the file is in correct WAVE audio format
	file -b $1 | grep -i "wave audio" &> /dev/null
	if [ $? -eq 0 ]; then
			printf "convert_%s: %s --> %s (using %s)\n\n" $2 $1 $converted_filename $3
			$3 $4 $1 $converted_filename
		else
			printf "convert_%s: %s not in correct WAVE audio format" $2 $1
	fi

}

# convert file to MP3 format
#
# function first converts file to intermediary WAV format then uses
# $ENC_MP3_PROG to convert to correct format
convert_mp3() {

	for file in $*; do
		wav_filename=`echo $file | sed -e 's/\..*$/\.wav/g'`
		convert_to_wav $file $wav_filename
		convert_from_wav $wav_filename 'mp3' "$ENC_MP3_PROG" "$ENC_MP3_OPTS"
	done

	echo -e "\nconvert_mp3: done\n"

}

# convert file to OGG format
#
# function first converts file to intermediary WAV format then uses
# $ENC_OGG_PROG to convert to correct format
convert_ogg() {

	for file in $*; do
		wav_filename=`echo $file | sed -e 's/\..*$/\.wav/g'`
		convert_to_wav $file $wav_filename
		convert_from_wav $wav_filename 'ogg' "$ENC_OGG_PROG" "$END_OGG_OPTS"
	done

	echo -e "\nconvert_to_ogg: done\n"

}

# play audio files
#
# parameters
#	input_files: files to play
play() {
	$PLAY_PROG $PLAY_PROG_OPTS $*
}

# play MP3 audio files (wrapper function to play function)
play_mp3() {
	play $*
}

# play OGG audiot files (wrapper function to play function)
play_ogg() {
	play $*
}

# generate ISO image
#
# parameters
#	input_files: file set to include in ISO image
generate_iso() {

	# check if ISO program is available in path
	is_installed $ISO_PROG

	# generate a random filename for the output file and store in TMP_DIR
	iso_file=${TMP_DIR}/iso_image.iso

	echo -e "generate_iso: creating ISO file $iso_file from file set\n"
	$ISO_PROG $ISO_PROG_OPTS $iso_file $*

}

# burn ISO image to CD drive
#
# parameters
#	input_files: file set to burn to CD
burn_data() {

	# check if burning program is available
	is_installed $CDBURN_PROG

	generate_iso $*

	# check if ISO image exists
	if [ -r ${TMP_DIR}/iso_image.iso ]; then
		echo -e "\nburn_data: burning ISO image to CD\n"
		#$CDBURN_PROG $CDBURN_PROG_DOPTS ${TMP_DIR}/iso_image.iso
	else
		echo -e "burn_data: iso_image.iso not found in ${TMP_DIR} or not readable\n"
	fi

	echo -e "\nburn_data: done\n"
}

burn_cd() {
	# convert files to WAV if necessary and then burn audio CD
	echo "here"
}

# burn MP3 files to CD drive
#
# parameters
#	input_files: file set to burn to CD
burn_mp3() {
	# generate ISO of selected MP3 files and burn ISO to CD
	burn_data $*
}

# rip audio track from CD drive
rip() {

	# check if CD ripping program is availble in PATH
	is_installed $RIP_PROG

	printf "rip: %s copying tracks from CD/DVD drive to $TMP_DIR\n\n" $RIP_PROG
	pushd $TMP_DIR > /dev/null

	$RIP_PROG $RIP_PROG_OPTS

	popd > /dev/null
}

# rip audio tracks from CD and convert to MP3 format
rip_mp3() {

	rip
	convert_mp3 ${TMP_DIR}/*

}

# rip audio tracks from CD and convert to OGG format
rip_ogg() {

	rip
	convert_ogg ${TMP_DIR}/*
}

# delete any temporary files created
cleanup() {
	# implement function to clean up temporary files
	echo "cleanup: removing temporary/intermediary files"
}

# verify that some parameter options were specified
if [ $# -eq 0 ]; then
	usage
fi

# initialize TMP_DIR
TMP_DIR=/tmp/mf_$$
if [ -e $TMP_DIR ]; then
	echo "warning: temporary directory $TMP_DIR exists, this may cause unexpected results"
else
	mkdir $TMP_DIR
	if [ $? -ne 0 ]; then
		echo "error: creation of temporary directory $TMP_DIR failed"
	fi
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
cleanup
