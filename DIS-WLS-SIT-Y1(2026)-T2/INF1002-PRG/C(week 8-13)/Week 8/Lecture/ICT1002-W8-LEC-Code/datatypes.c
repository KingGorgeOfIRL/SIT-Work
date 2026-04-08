/*
 * Data types program example.
 */
#include <stdio.h>

int main() {

	int	a = 3000;			/* integer data type */
	float	b = 4.5345;			/* floating point data type */
	char	c = 'A';			/* character data type */
	long	d = 31456;			/* long integer data type */
	double	e = 5.1234567890;	/* double-precision floating point data type */
	
	printf(" Here the list of the basic data types\n");
	printf("\n1. This an integer (int): %d", a);
	printf("\n2. This is a floating point number (float): %f", b);
	printf("\n3. This is a character (char): %c", c);
	printf("\n4. This is a long integer (long): %ld", d);
	printf("\n5. This is a double-precision float (double): %.10f", e);
	printf("\n6. This is a sequence of characters: %s", "Hello ICT1002 students");
	
	return 0;
	
}