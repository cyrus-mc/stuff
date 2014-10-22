#include <stdio.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>

#include "error2.h"
int main() {
	int file_d;

//	file_d = open("test", O_WRONLY | O_CREAT | O_EXCL);
	file_d = openFile("test2");
	printf("File id = %d and errno value is %d\n", file_d, errno);
	perror("Perror message is");
	return 0;
}
