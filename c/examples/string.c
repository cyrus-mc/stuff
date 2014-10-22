#include <stdio.h>

int main(void) {
	char input[100];
	char cmd[100], param[100];
	char *string_ptr;

	fgets(input, sizeof(input), stdin);
	input[strlen(input) - 1] = 0; // remove trailer \n
	printf("String is %s\n", input);
	
	string_ptr = strchr(input, ' ');
	// remove space and replace with back slash
	string_ptr[0] = '/';
	printf("String is %s\n", input);
	sscanf(input, "%s/%s", cmd, param);
	printf("Cmd is %s and param is %s", cmd, param);
	
}
