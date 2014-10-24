#include <stdio.h>

int main() {
	char input[20];
	char input2[20];
	int length;
	
	printf("Please enter some input: ");
	fgets(input, sizeof(input), stdin);
	input[strlen(input) - 1] = '\0';
	printf("Got %s\n", input);
	if (fputs(input, stdin) < 0)
		printf("Error\n");

	return 0;
}
