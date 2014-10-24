#include <unistd.h>
#include <stdio.h>

int main(int argc, char **argv) {
	int c;
	c = getopt(argc, argv, "c");
	printf("%c\n", c);
	return 0;
}
