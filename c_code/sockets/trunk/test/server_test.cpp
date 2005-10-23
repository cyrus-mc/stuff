#include <iostream.h>
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>

#include "server_socket.h"
#include "socket_exception.h"

int main(void) {
	char *data;
	Socket *sock2;
	int file_d;
	file_d = open("test_file2", O_WRONLY | O_CREAT, S_IRUSR | S_IWUSR);
	try {
		ServerSocket sock(2000);
		sock2 = sock.listen();
		sock2->read(file_d);
	} catch (SocketException e) {
		close(file_d);
		cout << e.description() << endl;
	}
	close(file_d);
	return 0;
}
