/*
 * Another simple C program.
 */
#include <stdio.h>

int main() {

	int integer1, integer2, sum;
	
	printf("Enter two numbers to add\n");
	scanf("%d%d", &integer1, &integer2);
	
	sum = integer1 + integer2;
	
	printf("Sum of entered numbers = %d\n", sum);
	
	return 0;
	
}