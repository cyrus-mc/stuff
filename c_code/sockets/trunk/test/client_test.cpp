/*
	C/C++ template file 

	$Author: cyrus $
	$Date: 2004/01/26 21:05:46 $
	$Revision: 1.1 $
*/

#include <iostream>
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <netdb.h>

#include "client_socket.h"
#include "socket_exception.h"

using namespace std;

int main(void) {
	int file_d;
	struct hostent *host;

	file_d = open("test_file", O_RDONLY);
	try {
		ClientSocket sock("127.0.0.1", 2000); 
		sock.write(file_d);
	} catch (SocketException e) {
		cout << e.description() << endl;
		close(file_d);
	}
	close(file_d);
	return 0;
}
