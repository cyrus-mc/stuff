#!/bin/bash

# set SSH options to force creation of pseudo terminal
SSH_OPTIONS="-t"

# scripts directory
SCRIPT_DIR=~/scripts

# systems directory
SYSTEM_DIR=~/.systems

CONNECT_AS=$USER

CMD_FILE=

# commands to execute can be specified on the command line or read from a script
# file. Parse command line using getopts
while getopts u:f:s: option
do
	case "$option" in
		u)
			CONNECT_AS=$OPTARG;;
		f)
			CMD_FILE=$OPTARG;;
		s)
			if [[ -f $SYSTEM_DIR/$OPTARG ]]; then
				SYSTEMS=`cat $SYSTEM_DIR/$OPTARG`
			else
				SYSTEMS="$OPTARG:$OPTARG"
			fi
			;;
		[?])
			echo "usage: $0 -f command_file -s systems [-u username]"
			exit 1;;
	esac
done

THIS_IP=`/sbin/ifconfig eth0 | grep inet | awk '{print $2}' | awk -F: '{print $2}'`

# execute script on remote system
for system in $SYSTEMS; do
	HOST=`echo $system | awk -F: '{print $1}'`
	IP=`echo $system | awk -F: '{print $2}'`
	echo "*************************************"
	echo "Connecting to $HOST (executing $CMD_FILE)"
	echo
	# copy script over only if this is a remote system
	if [[ ! "$THIS_IP" = "$IP" ]]; then
		scp $SCRIPT_DIR/$CMD_FILE ${CONNECT_AS}@${IP}:/tmp/${USER}_${CMD_FILE}
	fi
	ssh $SSH_OPTIONS ${CONNECT_AS}@${IP} /tmp/${USER}_${CMD_FILE}
done
