#include <stdio.h>
#include <stdlib.h>

int main() {
	unsigned short port = 5;
	char sport[100];

	sprintf(sport, "%d", port);
	printf("Port is %d, string port is %s\n", port, sport);
	return 0;
}
