#include <stdio.h>

int main(void) {
	int dep, with;
	char input[80];
	char cmd[80], param[80];
        fgets(input, sizeof(input), stdin);
//	input[strlen(input) - 1] = '\0';
	printf("Input = %s, size is %d\n", input, strlen(input));
	sscanf(input, "%s %s", cmd, param);
	printf("Cmd = %s, param = %s\n", cmd, param);	
	return 0;
}
