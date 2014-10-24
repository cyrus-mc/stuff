/*
	C/C++ template file 

	$Author: cyrus $
	$Date: 2004/01/26 21:05:46 $
	$Revision: 1.1 $
*/

void function(char *str) {
	char buffer[5];
	char buffer2[4];

	strcpy(buffer, "test");
	strncpy(buffer2,str, 8);
	printf("%s", buffer2);
}

void main() {
	char large_string[10];
	int i;
	for (i = 0; i < 8; i++)
		large_string[i] = 'A';

	
	function(large_string);
}


