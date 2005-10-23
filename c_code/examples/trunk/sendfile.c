#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

int main() {
	int in_fd, out_fd;

	in_fd = open("send", O_RDONLY);
	if (in_fd == -1) {
		printf("In file failed to open");
		exit(1);
	}
	
	out_fd = open("receive", O_WRONLY | O_CREAT);
	if (out_fd == -1) {
		printf("Out file failed to open");
		exit(1);
	}
	sendfile(out_fd, in_fd, 0, 3);	
	return 0;
}
