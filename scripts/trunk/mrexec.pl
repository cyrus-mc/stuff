#!/usr/bin/perl -w
#
use IPC::Open3;
use Symbol;

#-----------------------------
# Setup Variables for Open3
#-----------------------------
$WTR = gensym();
$RDR = gensym();
$ERR = gensym();

#-------------------------------------
# Setup some variables
#-------------------------------------
$LOGFILE = "/home/cyrus/.tmp/.email_console.log";

$FROM = "";
$REPLY_TO = "";

$seperator = "\n" . "_" x 60 . "\n\n";
$theOutput = $seperator; 
$command = "";
$params = "";

#--------------------------------------------
# HASH of valid commands that can be executed
# (provides a bit of added security)
#--------------------------------------------
%valid_cmds = ('ONLINE' => 'ssh', 'LISTING' => 'ls', 'RETRIEVE' => 'download');

$attachments = "";

#------------------------------------
# OPEN LOGFILE
# -----------------------------------
open LOG, ">>$LOGFILE";

#------------------------------------
# Parse email
#------------------------------------
while (<>) {

	# Retrieve Sender address
	if ( $_ =~ m/X-Sender: (.*)/) {
		$FROM = $1;
	}

	# Retrieve Sender override;
	if ( $_ =~ m/^Reply-To: (.*)/) {
		$REPLY_TO = $1;
		$FROM = $REPLY_TO;
		print "Reply_to = " . $FROM;
	}

	# Parse email for <exec> .. </exec> lines
	if ( $_ =~ m/^<exec>(.*)<\/exec>/ ) {
		
		$who = ($REPLY_TO ne "") ? "F:$FROM R:$REPLY_TO" : $FROM;
		$theOutput = scalar localtime() . " $who executed [$1]\n\n";

		# split xml tag <exec> body on space
		@xml_tag = split(/ /, $1);
		# check for a valid command
		$command = $valid_cmds{$xml_tag[0]};
		# set parameters as everything after the first element of array
		@parameters = splice(@xml_tag, 1, $#xml_tag + 1);

		$params = "";

		foreach $i (@parameters) {
			$params .= $i . " "; 
		}
		unless ($command) {
			$command = "";
		}
		
	   if ($command eq "download") {
			$attachments = $parameters[0]; 
		} elsif ($command ne "") {
			exec_cmd($command, $params);	
		}
		print LOG $theOutput;
	}
}

#--------------------------
# Close Log File
# -------------------------
close LOG;

#--------------------------
# Send replay e-mail
# -------------------------
exec "echo \"$theOutput\" | nail -s \"Console Auto Execute Reply\" \"$FROM\"";
# Send reply e-mail with execution results (use nail instead, or maybe mailx)
exit;

# run specified command
sub exec_cmd {
	
	my $cmd = shift @_;
	my $params = shift @_;

	$cmd .= " " . $params;

	$theOutput .= "running [$cmd]\n\n";
	open3($WTR, $RDR, $ERR, $cmd);
	close($WTR);

	while (<$RDR>) {
		$theOutput .= $_;
	}
	while (<$ERR>) {
		$theOutput .= $_;
	}

	$theOutput .= $seperator;

}

# remove leading / and .. to prevent execution of command outside of restricted area
sub removeSlash {
	my $cmd_parameters = shift @_;
        my $retval = "";
	
	@params = split(/ /, $cmd_parameters);
         
	foreach $param (@params) {
		$param =~ s/^\/*|\.*//g;
		$retval .= $param . " ";
	}
	
	return $retval;
}

