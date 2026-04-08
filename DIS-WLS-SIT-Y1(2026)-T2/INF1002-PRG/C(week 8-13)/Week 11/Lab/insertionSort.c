/*******************************************************************************
Task Description: 
    One way to sort a collection of items is called insertion sort. The idea is 
    to start with an empty list, then insert items one at a time into it, placing 
    them in their correct position in the order each time. Your task is to 
    implement this algorithm using a linked list in a program insertionSort, so 
    that the program can sort an arbitrary number of words entered by the user.

    Every node in the list should store one word, composed entirely of lower-case 
    characters. The word may also contain apostrophes and hyphens, but not spaces, 
    quotes, or any other characters that do not normally appear in the middle of 
    English words. The word may be up to 32 characters long.

    The program should repeatedly ask the user to enter a word. The program should 
    automatically convert upper-case letters into lower-case ones, but reject words 
    containing characters other than letters, apostrophes, and hyphens.

    Each new word should be inserted into the list into its correct position in 
    alphabetical order. For example, if the list currently contains the words 
    "cat", "dog", and "monkey", and the user enters the word "elephant", the new 
    word should be inserted between "dog" and "monkey". You can use strcmp() to 
    determine whether a word comes before or after another in the alphabet (hyphens 
    and apostrophes will be sorted according to their ASCII values).

    The program stops asking for words when the user enters the special text 
    "***". The program should then print out the words, in order, one per line.

    Finally, the program should de-allocate all the memory that is has created and terminate.
    Note:
    1.	Use #define and comments as usual.
    2.	Check for memory allocation failures and report an error if they occur but 
        continue to execute the program.
    3.	There is no white space in the print after the colons (:).
    4.	Don’t use sys.argv[] for user inputs. Use other functions such as 
        scanf(), fgets(), fgetc(), etc.,

Some sample output is shown below, with the user input shown in red:
Example – 1:
    Please enter a word: 
    cat
    Please enter a word: 
    dog
    Please enter a word: 
    monkey
    Please enter a word: 
    elephant
    Please enter a word: 
    ***
    All the entered words in order:
    cat
    dog
    elephant
    monkey

Example – 2:
    Please enter a word:
    hello
    Please enter a word:
    good-bye
    Please enter a word:
    it's
    Please enter a word:
    invalid word
    Invalid word.
    Please enter a word:
    valid
    Please enter a word:
    "quote"
    Invalid word.
    Please enter a word:
    another
    Please enter a word:
    ***
    All the entered words in order:
    another
    good-bye
    hello
    it's
    valid
*******************************************************************************/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <stdbool.h>

#define stop_condition "***"
#define max_length_word 32

typedef struct LinkedList
{   
    char string[max_length_word+1];
    struct LinkedList *next_node;
} LinkedList;

int validate_input(char string[255]){
    int string_length = strlen(string);
    if (string_length <= max_length_word){
        for (int i = 0; i < string_length; i++){
            char letter = string[i];
            if (!(isalpha(string[i]) || letter == '\'' || letter == '-')){
                if (strstr(string,stop_condition) == NULL){
                    return 0;
                }else{
                    return -1;
                }
            }
            if(isupper(string[i])){
                string[i] = tolower(string[i]);
            }
        }
        return 1;
    }
    return 0;
}


int main() 
{
    char input[255];
    LinkedList *head;
    int result = 0;
    do{
        LinkedList *current = head;
        /*get user input*/
        printf("Please enter a word:\n");
        scanf("%[^\n]%*c",input);
        int result = validate_input(input);
        if (result == 1){
            LinkedList *new_link = (LinkedList*)malloc(sizeof(LinkedList));
            strcpy(new_link->string,input);
            /* first link initialisation */
            if (head->string == NULL){
                head = new_link;
            }
            /* link before head */
            else if (strcmp(head->string, input) > 0){
                new_link->next_node = head;
                head = new_link;
            }else{
                while (current->next_node != NULL) {
                    /* middle of chain */
                    int lesser_than = strcmp(current->string, input);
                    int greater_than = strcmp(current->next_node->string,input);
                    if (strcmp(current->string, input) <= 0 && strcmp(current->next_node->string,input) > 0){
                        new_link->next_node = current->next_node;
                        current->next_node = new_link;
                        break;
                    }
                    current = current->next_node;
                }
                /* end link */
                if (current->next_node == NULL){
                    current->next_node = new_link;
                }
            } 
        }else if (result == -1){
            break;
        }else{
            printf("Invalid word.\n");
        }
    }while (result != -1);

    /*return ordered list*/
    printf("All the entered words in order:\n");
    while (head != NULL){
        printf("%s\n",head->string);
        head = head->next_node;
    }
    return 0;
}
