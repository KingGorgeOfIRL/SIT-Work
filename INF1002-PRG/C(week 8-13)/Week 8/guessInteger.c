/*******************************************************************************
Task Description: 
    Write a program called "guessInteger.c" that will play a simple two-player 
    number-guessing game as follows: 
    1.	The program will output "Player 1, enter a number between 1 and 1000:"". 
        If the player enters a number that is not in this range (inclusive), 
        the program will output "That number is out of range."" This repeats until 
        Player 1 has entered a number that is in range. 
    2.	Player 2 (who has not been watching Player 1) has 10 rounds in which to 
        correctly identify the number. At the start of each round, the program 
        will output "Player 2, you have n guess(es) remaining." where n is the 
        number of rounds left
    3.	The program will output "Enter your guess:" and wait for Player 2 to enter 
        a number. 
        a.	If Player 2's guess is too high, the program will output "Too high." 
        b.	If Player 2's guess is too low, the program will output "Too low." 
        c.	If Player 2's guess is correct, the program will output "Player 2 wins." 
            and stop. 
        d.	If Player 2's guess is out of range, the program will output "That number 
            is out of range." and return to Step 3. The number of guesses should not 
            be incremented. 
    4.	If Player 2 has not guessed the number at the end of the 10th round, the 
        program will output "Player 1 wins." and stop. 

    Some sample output is shown over the page, with the user input shown in red:
        Player 1, enter a number between 1 and 1000:
        1500
        That number is out of range.
        Player 1, enter a number between 1 and 1000:
        500
        Player 2, you have 10 guesses remaining.
        Enter your guess:
        750
        Too high.
        Player 2, you have 9 guesses remaining.
        Enter your guess:
        250
        Too low.
        Player 2, you have 8 guesses remaining.
        Enter your guess:
        500
        Player 2 wins.

Note: 
    .	Use macros (#define) to represent all constants, so that it is easy to 
        change things like    the number of guesses allowed, the highest number 
        allowed, and so on. 
    .	Don't use sys.argv[] for user inputs. 
        Use other functions such as scanf(), fgets(), fgetc(), etc.,
    .	There is no white space in the print after the colons (:).
    .	Include comment to your code that explains what each section of it does. 
*******************************************************************************/
#include <stdio.h>

int main()
{
    /* code here */

    return 0;
}
