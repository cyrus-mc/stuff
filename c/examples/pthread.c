#include <pthread.h>

void print_message_function (void *ptr);

int main() {
	pthread_t threadID;
	char *message = "Hello";

	printf("%s\n", message);

	pthread_create(&threadID, NULL, (void*)&print_message_function, (void *)message);
	printf("%s\n", message);
	exit(0);
}


void print_message_function (void *ptr) {
	char *message;
	message = (char *)ptr;
	printf("%s\n", message);
}
