#!/bin/sh
#
# System Backup script
#
# This script is used to backup important system files and create archives
# that can be burned to a CD for restoration if needed
#
# $Author: $
# $Date: $
# $Revision: $

if [ -z $1 ]; then
	LIST_FILE=/bkp/scripts/list
else
	LIST_FILE=$1
fi

# check for existence of master file
if [ ! -f $LIST_FILE ]; then
	echo "Master file missing, aborting"
fi

# source master file
. $LIST_FILE 

FLAGS=
if [ ! -z "$1" ]; then
	FLAGS=-v
fi

LAYOUT_DIR=/bkp/layout
ARCHIVE_DIR=/bkp/archives
TMP_DIR=/bkp/tmp
DESTINATION_DIR=$LAYOUT_DIR/files

MOD_FLAG="-T 0000"

copydir () {
	for dir in $*; do
		echo "Copying $dir to $DESTINATION_DIR"
		if [ -d $dir ]; then
			pax $FLAGS -rw $dir $DESTINATION_DIR
		else
			echo "Directory $dir does not exist, skipping.."
		fi
	done
}

copyfiles () {
	for file in $*; do
		echo "Copying $file to $DESTINATION_DIR"
		if [ -f $file ]; then
			cp --preserve=all --parents $file $DESTINATION_DIR
		else
			echo "File $file does not exist, skipping.."
		fi
	done
}

echo "Starting copy process"
echo "-----------------------------------------"
copydir `echo ${bkpdir[*]}`
copyfiles `echo ${files[*]}` 

# scan home directories for .backup_files file
for user in `ls /home`; do
	if [ -f /home/$user/.backup_files ]; then
		echo "Backup up files for user: $user"
		unset files
		unset bkpdir
		. /home/$user/.backup_files
		
		# change to home directory
		cd /home/$user

		#modify DESTINATION_DIR to have /home/user_name appended
		DESTINATION_DIR=$DESTINATION_DIR/home/$user

		# create root directory if necessary
		[ ! -d $DESTINATION_DIR ] && mkdir -p $DESTINATION_DIR

		copydir `echo ${bkpdir[*]}`
		copyfiles `echo ${files[*]}`
	fi
done

echo "------------------------------------------"
echo "Copy process done, moving onto archiving"
echo "------------------------------------------"

cd $ARCHIVE_DIR
# backup old archive, if it exists
[ -f archive.cpio.gz ] && mv archive.cpio.gz archive.cpio.gz-old

# archive and compress backup files
pax -x cpio -wzf archive.cpio.gz $LAYOUT_DIR

echo "Archiving complete"
echo "------------------------------------------"
echo -n "Backup completed on "
echo `date`

# backup old file listing, if it exists
#[ -f $TMP_DIR/file-list ] && mv -f $TMP_DIR/file-list $TMP_DIR/file-list.old

# check differences between new archive and old
#$PAX -zf archive.tar.gz | sort > $TMP_DIR/file-list

#diff $TMP_DIR/file-list $TMP_DIR/file-list.old > new_files

touch /bkp/.backup
echo `date` > /bkp/.backup
