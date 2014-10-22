#!/bin/sh
#
# System Backup script
#
# This script is used to backup important system files and create archives
# that can be burned to a CD for restoration if needed
#
# TODO:
#	- do not follow symlinks when taring since that could duplicate data
#	- see if we can add code to retroactively add the linked file or directory
#	  to the backup list
#
# $Author: $
# $Date: $
# $Revision: $

# initialize list file (contains list of files and directories to backup)
if [[ -z $1 ]]; then
	LIST_FILE=/bkp/scripts/list
	if [[ ! -f $LIST_FILE ]]; then
		echo "error: list file not specified and master list file not found, exiting"
		exit 1;
	fi
else
	LIST_FILE=$1
fi

# source master file
. $LIST_FILE 

BKP_DIRECTORY=/home/cyrus/tmp
BKP_PROG="rsync"
BKP_PROG_OPTS="-avz"

# ---------------------- OLD STUFF ----------------------------
FLAGS=
if [ ! -z "$1" ]; then
	FLAGS=-v
fi

LAYOUT_DIR=/bkp/layout
ARCHIVE_DIR=/bkp/archives
TMP_DIR=/bkp/tmp
DESTINATION_DIR=$LAYOUT_DIR/files

MOD_FLAG="-T 0000"

# ------------------- END OLD STUFF ---------------------------

# used to create the parent path of the directory or file being backed up
create_bkp_directory() {

	dir_name=$1
	# check if directory already exists, if it does do not create
	if [[ $dir_name != '.' ]] || [[ $dir_name != '/' ]]; then
		if [[ ! -d ${BKP_DIRECTORY}/$dir_name ]]; then
			mkdir -p ${BKP_DIRECTORY}/$dir_name
		fi
	fi
}

# function used to determine whether a item is a link or not
is_link() {
	local link_path
	link_path=`file -b $1 | grep -i link`

	if [[ $? -eq 0 ]]; then
		return 1
	else
		return 0
	fi
}

backup () {

	local prefix_dir, link_path

	for item in $*; do
		prefix_dir=`dirname $item`
		if [ -e $item ]; then
			echo "backup: backing up $item to $BKP_DIRECTORY"
			create_bkp_directory $prefix_dir
			$BKP_PROG $BKP_PROG_OPTS $item ${BKP_DIRECTORY}/${item}

			# check if item is a link and if so, retroactively add the linked
			# directory to the backup archive
			link_path=`file -b $item | grep -i link`
			if [[ $? -eq 0 ]]; then
				echo "LINKED DIRECTORY FOUND:" 
				# backup $( echo $link_path | awk '{print $4}' | sed -e s/[\'\`]//g )
			fi

		else
			echo "backup: $item not found, ignoring"
		fi
	done

}

# scan user home directories for .backup_files file
scan_home_directories() {

	for user in `ls /home`; do
		if [[ -d /home/$user ]]; then
			if [[ -f /home/$user/.backup_files ]]; then
				source /home/${user}/.backup_files
				backup `echo ${bkp_items[*]}`
			fi
		fi
	done

}

echo -e "backup: starting copy process (to: $DESTINATION_DIR)\n"

backup `echo ${bkp_items[*]}`
#scan_home_directories

echo -e "\nbackup: finished copy process\n"

#echo "------------------------------------------"
#echo "Copy process done, moving onto archiving"
#echo "------------------------------------------"

#cd $ARCHIVE_DIR
# backup old archive, if it exists
#[ -f archive.cpio.gz ] && mv archive.cpio.gz archive.cpio.gz-old

# archive and compress backup files
#pax -x cpio -wzf archive.cpio.gz $LAYOUT_DIR

#echo "Archiving complete"
#echo "------------------------------------------"
#echo -n "Backup completed on "
#echo `date`

# backup old file listing, if it exists
#[ -f $TMP_DIR/file-list ] && mv -f $TMP_DIR/file-list $TMP_DIR/file-list.old

# check differences between new archive and old
#$PAX -zf archive.tar.gz | sort > $TMP_DIR/file-list

#diff $TMP_DIR/file-list $TMP_DIR/file-list.old > new_files

#touch /bkp/.backup
#echo `date` > /bkp/.backup
