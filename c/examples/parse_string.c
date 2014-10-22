#include <stdio.h>
#include <string.h>

char **parseInput(char *, char);
char *generateString(char **);

int main(void) {
	char *input, **command;
	input = (char *)malloc(24);
	
	strcpy(input, "part1 part2 part3 part4");
	command = parseInput(input, ' ');
	printf("First string is %s\n", command[0]);
	generateString(command);
	printf("After generateString: %s\n", generateString(command));
	return 0;
}

char **parseInput(char *input, char delimit) {
	int index = 1;
	char *string_ptr, **command;

	command = (char **)malloc(7 * sizeof(char *));
	command[0] = input;

	string_ptr = input;
//	while (cont) {
	for (index = 1; index < 7; index++) {
		if (string_ptr != NULL) 
			string_ptr = strchr(string_ptr, delimit);

		if (string_ptr != NULL) {
			
			string_ptr[0] = '\0';
			command[index] = string_ptr + 1;
			string_ptr++;
		} else {
			command[index] = NULL;
		}
	}
	return command;
}

char *generateString(char **commands) {
	char *string_ptr, index;

	for (index = 1; index < 7; index++) {
		if (commands[index] != NULL) {
			string_ptr = commands[index] - 1;
			string_ptr[0] = '/';
		}
	}

/*	string_ptr = commands[1] - 1;
	printf("String ptr %s\n", string_ptr);
	string_ptr[0] = '/';
	printf("String ptr is %s\n", string_ptr);
	string_ptr = commands[2]--;
	string_ptr[0] = '/';*/
	return commands[0];
}
