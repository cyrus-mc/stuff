#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>

int main() {
	int in_fd, out_fd;
	struct stat sbuf;
	char *buffer;
	int readbytes, totalbytes;
	int temp;

	readbytes = 0;
	totalbytes = 0;

	stat("simple.mp3", &sbuf);
	
	in_fd = open("simple.mp3", O_RDONLY);
	out_fd = open("simple2.mp3", O_WRONLY | O_CREAT);

	buffer = (char *)malloc(sbuf.st_size);
	buffer[sbuf.st_size - 1] = '\0';
	printf("Size allocated is %d\n", strlen(buffer));
	printf("File size is %d\n", sbuf.st_size);

	readbytes = sbuf.st_size;
	while (totalbytes < sbuf.st_size) {
		temp = read(in_fd, buffer, readbytes);
		readbytes -= temp;
		totalbytes += temp;
	}
	
	printf("Total bytes read is %d\n", totalbytes);

	printf("Wrote %d bytes", write(out_fd, buffer, totalbytes));
	close(in_fd);
	close(out_fd);
	return 0;
}
