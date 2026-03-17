#include <stdlib.h>
#include <stdio.h>
int main(){
    int val = 1;
    int *valp = &val;
    if (1){
        
    }
    printf("%d %p\n", val, &val);
    printf("%p %p %d\n", &valp, valp, *valp);
    return 0;
}