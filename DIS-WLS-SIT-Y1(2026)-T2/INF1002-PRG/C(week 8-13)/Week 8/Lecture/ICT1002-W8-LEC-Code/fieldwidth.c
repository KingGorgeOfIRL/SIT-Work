/*
 * Printing field width example adapted from Deitel & Deitel [2]
 */
#include <stdio.h>

int main() {

	printf("%4d\n", 1);
	printf("%04d\n", 1);
	printf("%-4d\n", 1);
	printf("%4d\n", 12);
	printf("%4d\n", 123);
	printf("%4d\n", 1234);
	printf("%4d\n", 12345);
	
	return 0;
}
	