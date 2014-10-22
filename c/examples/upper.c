#include <stdio.h>

int main(void) {
	int i, value;
	char input[5];
	strcpy(input, "test");
	for (i = 0; i < sizeof(input) - 1; i++) {
		value = input[i];
		if (value >= 97 && value <= 123) 
			input[i] = value - 32;
	}
	printf("%s\n", input);

	return 0;
}
