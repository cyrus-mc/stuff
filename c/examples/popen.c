#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <fcntl.h>

int main(void) {
	FILE *file_ptr;
	struct stat sbuf;
	int total = 100, offset = 0, temp;
	int file_d;
	char *buffer;

	file_ptr = popen("ls -al", "r");
	file_d = open("test", O_WRONLY | O_CREAT);
	sendfile(file_d, fileno(file_ptr), 0, 100);
	
	return 0;
}
