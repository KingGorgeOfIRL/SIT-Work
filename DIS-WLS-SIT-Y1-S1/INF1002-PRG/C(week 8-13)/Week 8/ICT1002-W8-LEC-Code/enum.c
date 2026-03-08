/*
 * Enumerated data type example.
 */
#include <stdio.h>

enum colour {
	Blue,
	Yellow,
	White,
	Black
};

int main() {

	enum colour colourIndex;
	
	for (colourIndex = Blue; colourIndex <= Black; colourIndex++) {
	
		switch (colourIndex) {
			case Blue:
				printf("Blue's index is: \t%d\n", Blue);
				break;
			case Yellow:
				printf("Yellow's index is: \t%d\n", Yellow);
				break;
			case White:
				printf("White's index is: \t%d\n", White);
				break;
			case Black:
				printf("Black's index is: \t%d\n", Black);
				break;
		}
	}
}