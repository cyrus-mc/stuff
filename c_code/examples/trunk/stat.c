#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>

int main() {
	struct stat statbuf;
	char input[20];
	
	if (fstat(0, &statbuf) < 0) {
		printf("Error");
		exit(1);
	}

	printf("Size of buffer is %d\n", statbuf.st_size);
	return 0;
}
