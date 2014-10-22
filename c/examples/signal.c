#include <stdio.h>
#include <signal.h>

void sighandler(int signum);

int main(void) {

	if (signal(SIGINT, &sighandler) == SIG_ERR) {
		fprintf(stderr, "Couldn't register signal handler\n");
		exit(1);
	}

	while (1) {
	}
	return 0;
}

void sighandler(int signum) {
	printf("Caught signal %d\n", signum);
}
