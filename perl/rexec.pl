#!/usr/bin/perl
#

use constant SCRIPT_LOCATION => '';
use constant SYSTEM_FILES_LOCATION => ''; 

my $parsed_config;

# retrieve a section of the script configuration file
sub parse_script {
	my $script_file = shift @_;
	my $section_name = '';
	my $in_section = 0;
	my $index = 0;
	my %hconfig;

	print "reading in script file $script_file\n";
	open CONFIG, "< $script_file" or die "could not open script file\n";
	while (<CONFIG>) {
		if ($_ =~ m/(.*):start/ ) {
			$section_name = $1;
			$hconfig{$1} = ();
			$in_section = 1;
		} elsif ($_ =~ m/(.*):end/ ) {
			$in_section = 0;
			$index = 0;
		} elsif ($in_section == 1) {
			$hconfig{$section_name}[$index] = $_;
			$index++;
		}
	}
	close CONFIG;
	return \%hconfig;
}

#--------------- user input -------------------------
print "enter the name of the script to run (blank to exit): ";
my $script = SCRIPT_LOCATION . <STDIN>;

print "enter the systems to run on (blank to exit): ";
my $systems = SYSTEM_FILES_LOCATION . <STDIN>;

$parsed_config = parse_script $script;

# loop over systems file and run specified commands
open SYSTEMS, "< $systems" or die "could not open systems file\n";
while (<SYSTEMS>) {
	@system_details = split(/:/, $_);
}

# tests
#foreach(@{$parsed_config->{'bap'}}) {
#	print $_;
#}
