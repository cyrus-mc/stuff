#include <stdio.h>

void testFunction();

int main(void) {
	testFunction();
	return 0;
}

void testFunction() {
	int port;
	printf("Enter port ");
	scanf("%d", &port);
	printf("Entered port = %d\n", port);
}
