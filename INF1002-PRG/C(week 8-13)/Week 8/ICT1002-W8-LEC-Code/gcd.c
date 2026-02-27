/*
 * GCD program from [5]
 */
#include <stdio.h>

int gcd(int a, int b) {
 
	if (b == 0)
		return a;
	else
		return gcd(b, a % b);
		
}

int main() {

	printf("GCD: %d\n", gcd(24, 40));
	
	return 0;
	
}