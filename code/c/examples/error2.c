#include <stdio.h>
#include <sys/stat.h>
#include <fcntl.h>

int openFile(char *filename) {
	return open(filename, O_RDONLY);
}
