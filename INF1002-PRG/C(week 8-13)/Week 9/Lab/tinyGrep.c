/*******************************************************************************
Task Description: 
    Unix systems provide a utility known as grep that searches the lines of a 
    file for a given pattern of characters. (The pattern is known as a regular 
    expression and the name grep comes from “global regular expression print”). 
    We won’t learn how to use files until Week 12, so we’ll just search a string 
    entered at the keyboard. We’ll also support only very simple patterns.

    Write a program, called tinyGrep.c, that performs as follows:
    1.	The program asks the user to enter a line of text of up to 255 characters.
    2.	The program asks the user to enter a pattern (a string), also of up to 
        255 characters.
    3.	The program asks the user whether the match should be case-sensitive or 
        case-insensitive.
    4.	The program outputs whether the pattern occurs anywhere in the line of 
        text, and, if it does, the index of the string at which the first instance 
        of the pattern occurs.

    The rules for patterns are as follows:
    •	Any English letter matches itself. If the match is case-sensitive, 
        lower-case letters match only lower-case letters, and upper-case letters 
        match only upper-case letters. If the match is case-insensitive, 
        lower-case letters match upper-case letters, and vice versa.
    •	A dot (.) matches any character.
    •	An underscore (_) matches any form of whitespace (i.e. any character for 
        which isspace() returns a true value).
    •	All other characters match only themselves.

    The following table shows some examples.
        --------------------------------------------------------------------------------
        | Text                    | Pattern | Case-sensitive | Output                  |
        --------------------------------------------------------------------------------
        | The cat sat on the mat. | cat     | N              | Matches at position 4.  |
        | The cat sat on the mat. | rat     | N              | No match.               |
        | The cat sat on the mat. | at      | N              | Matches at position 5.  |
        | The cat sat on the mat. | .at     | N              | Matches at position 4.  |
        | The cat sat on the mat. | the     | N              | Matches at position 0.  |
        | The cat sat on the mat. | the     | Y              | Matches at position 15. |
        | The cat sat on the mat. | ...     | Y              | Matches at position 0.  |
        | "Hello," said the cat.  | ,       | N              | Matches at position 6.  |
        --------------------------------------------------------------------------------

    You might like to proceed as follows:
        1.	Write a main program that reads the strings and case-sensitivity 
            information as described above.
        2.	Don’t use sys.argv[] for user inputs. Use other functions such 
            as scanf(), fgets(), fgetc(), etc.,
        3.	Ignoring the pattern-matching rules for now, write a loop that simply 
            searches the input text for an occurrence of the pattern string using 
            strncmp() or similar function.
        4.	Replace strncmp() with a new function that matches case-sensitively 
            or case-insensitively depending on the user’s answer to this question.
        5.	There is no white space in the print after the colons (:).
        6.	Modify your new function to handle dot and underscore characters 
            according to the rules above.

Sample Program inputs and outputs:
Example - 1:
    Enter a line of text (up to 255 characters):
    The cat sat on the mat.
    Enter a pattern (up to 255 characters):
    cat
    Should the match be case-sensitive? (Y/N):
    N
    Matches at position 4.

Example - 2: 
    Enter a line of text (up to 255 characters):
    The cat sat on the mat.
    Enter a pattern (up to 255 characters):
    rat
    Should the match be case-sensitive? (Y/N):
    N
    No match.
 *******************************************************************************/
#include <stdio.h>
#include <stdbool.h>
#include <ctype.h>
#include <string.h>

char* lower_char(char* input){
    for(int i = 0; input[i]; i++){
        input[i] = tolower(input[i]);
    }
    return input;
}
int main() 
{
    /* code here */
    char input[255], pattern[255],caseSensitive[2];
    int position = sizeof(input) + 1;

    printf("Enter a line of text (up to 255 characters):\n");
    fgets(input,sizeof(input),stdin);
    input[strlen(input)-1] = '\0';

    printf("Enter a pattern (up to 255 characters):\n");
    fgets(pattern,sizeof(pattern),stdin);
    pattern[strlen(pattern)-1] = '\0';

    printf("Should the match be case-sensitive? (Y/N):\n");
    fgets(caseSensitive,sizeof(caseSensitive),stdin);
    caseSensitive[strlen(caseSensitive)-1] = '\0';

    int largest = strlen(pattern);
    int input_length = strlen(input);
    int check;
    for (int i = 0; i < input_length; i++){
        for (int p = 0; p < largest;p++){
            if (p+1 >= largest){
                position = i;
                break;
            }
            char str1 = input[i+p]+"\n";
            char str2 = pattern[p]+"\n";
            check = strcmp(input[i+p]+"\n",pattern[p]+"\n");
            if (check == 0){
                continue;
            }else if (pattern[p] == '_' && isspace(input[i+p])){
                continue;
            }else if (pattern[p] == '.'){
                continue;
            }else if (check == 32 && lower_char(caseSensitive) == "n"){
                continue;
            }else{
                break;
            }
        }

    }
    if (position > sizeof(input)){
        printf("No match.\n");
    }else{
        printf("Matches at position %d.\n", position);
    }
    return 0;
}
