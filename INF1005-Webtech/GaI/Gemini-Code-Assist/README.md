# Pet Lovers Community Onboarding Wizard

This is a full-stack web application for a pet lovers community. It features a multi-step onboarding wizard for new users, user profiles, and a flat-file CSV database.

## Technologies Used

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript, Bootstrap
*   **Database:** CSV flat-file

## How to Run

1.  Clone this repository.
2.  Navigate to the project directory in your terminal.
3.  Start the PHP built-in web server:
    ```bash
    php -S localhost:8000
    ```
4.  Open your web browser and go to `http://localhost:8000`.

## Folder Structure

```
.
├── css
│   └── style.css
├── data
│   ├── pets.csv
│   ├── uploads
│   └── users.csv
├── includes
│   ├── database.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── session.php
├── js
│   └── script.js
├── onboarding
│   ├── step1_user_pass.php
│   ├── step2_personal_info.php
│   ├── step3_profile_photo.php
│   ├── step4_pet_info.php
│   └── step5_confirmation.php
├── profile
│   ├── delete.php
│   ├── edit.php
│   └── view.php
├── .gitignore
├── index.php
├── login.php
├── logout.php
└── README.md
```
