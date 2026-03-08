#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
struct Student 
{
    int id;
    char name[255];
    char programme[255];
    float mark;
};
struct Student *openfile(const char *file_path, int *out_count){
    FILE *fptr = fopen(file_path,"r");
    struct Student *student_records = NULL;
    int count = 0, capacity = 0;

    if (out_count != NULL){
        *out_count = 0;
    }

    // checks file pointer is vaild
    if(fptr != NULL){
        while (1){
            int id = 0;
            char name[255] = "";
            char programme[255] = "";
            float mark = 0.0f;
            int result = fscanf(fptr, "%d,%254[^,],%254[^,],%f", &id, name, programme, &mark);
            if (result == 4){
                if (count == capacity){
                    int new_cap = (capacity == 0) ? 2 : capacity * 2;
                    struct Student *temp = realloc(student_records,new_cap * sizeof(*student_records));
                    if (temp == NULL){
                        free(student_records);
                        fclose(fptr);
                        return NULL;
                    }
                    student_records = temp;
                    capacity = new_cap;
                }
                student_records[count].id = id;
                strncpy(student_records[count].name, name, sizeof(student_records[count].name) - 1);
                student_records[count].name[sizeof(student_records[count].name) - 1] = '\0';

                strncpy(student_records[count].programme, programme, sizeof(student_records[count].programme) - 1);
                student_records[count].programme[sizeof(student_records[count].programme) - 1] = '\0';

                student_records[count].mark = mark;
                count++;
            }else if (result == EOF){
                break;
            }else{
                int ch;
                while ((ch = fgetc(fptr)) != '\n' && ch != EOF) {
                }
            }

        }
        // loops through file, line by line of a buffer size of 255 and will break if a newline character is found

    } else{
        return NULL;
    }
    fclose(fptr);
    if (out_count != NULL) {
        *out_count = count;
    }
    return student_records;
}


int main(){
    int count = 0;
    struct Student *records = openfile("../../DB/Sample_CMS.txt", &count);
    
    if (records != NULL) {
        for (int i = 0; i < count; i++) {
            printf("%d, %s, %s, %.2f\n",
                records[i].id,
                records[i].name,
                records[i].programme,
                records[i].mark);
        }
    }

    free(records);

    return 0;
}