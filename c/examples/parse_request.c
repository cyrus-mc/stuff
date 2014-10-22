#include <stdio.h>

int main(void) {
	char input[100];
	char one[100], two[100];
	unsigned short port;

	fgets(input, sizeof(input), stdin);
	sscanf(input, "%s", one);
	printf("One is %s\n", one);

	return 0;
}
