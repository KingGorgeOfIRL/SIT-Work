# Pet Lovers Community - Onboarding Wizard

A full-stack web application for a pet lover community with user onboarding, authentication, profile management, and pet tracking.

## Project Structure

```
pet-community/
├── index.php                    # Landing page
├── login.php                    # User login
├── dashboard.php                # User directory
├── profile.php                  # User profile (view/edit/delete)
├── onboarding/
│   ├── step1.php               # Create account (username & password)
│   ├── step2.php               # Personal info (name, email, phone)
│   ├── step3.php               # Profile photo upload
│   ├── step4.php               # Pet information
│   └── step5.php               # Confirmation & save
├── process/
│   ├── session.php             # Session & authentication management
│   ├── user.php                # User CRUD operations
│   ├── upload.php              # File upload handler
│   ├── logout.php              # Logout handler
│   └── delete.php              # Account deletion
├── includes/
│   └── functions.php           # Utility functions
├── css/
│   └── style.css               # Custom styling (Bootstrap 5)
├── data/
│   ├── users.csv               # User database
│   ├── pets.csv                # Pet database
│   └── uploads/                # Uploaded images
│       ├── profiles/           # User profile photos
│       └── pets/               # Pet photos
└── README.md                   # This file
```

## Features

### Onboarding Wizard (5 Steps)
1. **Username & Password** - Create account with validation
2. **Personal Info** - Add name, email, phone
3. **Profile Photo** - Upload square profile image (min 300x300px)
4. **Pet Information** - Add multiple pets with details and photos
5. **Confirmation & Save** - Review and complete setup

### Authentication & Security
- Secure password hashing with bcrypt (password_hash/password_verify)
- CSRF token protection on all forms
- Session-based authentication
- Session timeout for inactivity (30 minutes)
- Input sanitization and validation

### User Features
- View community member directory
- Browse user profiles and their pets
- View own profile with full details
- Edit profile information (except username)
- Upload/change profile photo
- Manage multiple pets per account
- Delete account and all associated data

### Database (CSV-based)
- **users.csv**: user_id, username, password_hash, name, email, phone, profile_photo, created_at, updated_at
- **pets.csv**: pet_id, user_id, pet_name, breed, age, pet_photo
- File locking for concurrent access safety
- Automatic backup before writes

## Requirements

- PHP 7.4 or higher
- Web server (Apache with mod_rewrite recommended)
- File write permissions for `/data/` directory
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Installation

1. **Extract files** to your web server directory
2. **Set permissions** for data directory:
   ```bash
   chmod 755 data/
   chmod 755 data/uploads/
   chmod 755 data/uploads/profiles/
   chmod 755 data/uploads/pets/
   chmod 666 data/users.csv
   chmod 666 data/pets.csv
   ```
3. **Access application** at: `http://localhost/` (or your server URL)

## Usage

### For New Users
1. Click "Get Started" on the landing page
2. Follow the 5-step onboarding wizard
3. Provide required information at each step
4. Review and confirm all data
5. Automatically logged in after completion

### For Existing Users
1. Click "Login" on the landing page
2. Enter username and password
3. Access dashboard to browse members
4. Click "View Profile" to see user details including pets
5. Click "My Profile" to view/edit own information

### Profile Management
- **View**: Click user card on dashboard
- **Edit**: Click "Edit Profile" on your profile
- **Add Pet**: Use "Add New Pet" button on profile
- **Delete Pet**: Click "Delete" button on pet card
- **Delete Account**: Click "Delete Account" button with confirmation

## Data Validation Rules

- **Username**: 3-20 characters, alphanumeric + underscore only
- **Password**: Minimum 6 characters
- **Email**: Valid email format (required)
- **Phone**: No specific format (optional)
- **Pet Name**: Required, max 50 characters
- **Photos**: JPG/PNG/GIF, max 5MB
  - Profile photos: Square, minimum 300x300px
  - Pet photos: Minimum 200x200px

## Security Considerations

- Passwords are hashed using bcrypt (not reversible)
- CSRF tokens prevent cross-site request forgery
- Sessions expire after 30 minutes of inactivity
- User inputs are sanitized to prevent XSS attacks
- File uploads validated for type, size, and dimensions
- CSV files use proper quote escaping for special characters
- File locking prevents race conditions during concurrent writes

## File Upload Handling

- Uploads stored in `/data/uploads/{type}/` directory
- Files renamed with timestamp to prevent conflicts
- Original filename preserved in database
- Delete operations clean up associated images
- Image validation checks MIME type and dimensions

## CSV Storage Format

All CSV files use standard format with headers:
- Special characters (quotes, commas, newlines) are properly escaped
- Automatic backups created before writes
- Concurrent access safe with file locking

### Example users.csv entry:
```csv
user_id,username,password_hash,name,email,phone,profile_photo,created_at,updated_at
user_1a2b3c,john_doe,"$2y$12$Hash...",John Doe,john@example.com,555-1234,img_timestamp.jpg,2024-02-25 10:30:00,2024-02-25 10:30:00
```

### Example pets.csv entry:
```csv
pet_id,user_id,pet_name,breed,age,pet_photo
pet_5x6y7z,user_1a2b3c,Max,Golden Retriever,3 years,img_timestamp.jpg
```

## Troubleshooting

### Upload fails
- Check file format (JPG, PNG, GIF only)
- Verify file size is under 5MB
- Ensure `/data/uploads/` directories exist and are writable
- For profile photos, ensure image is square with minimum 300x300px

### Session expires
- Sessions timeout after 30 minutes
- Max file size in php.ini must allow uploads (default 2MB)
- Check `upload_max_filesize` and `post_max_size` in php.ini

### CSV file errors
- Ensure `/data/` directory has write permissions
- Check disk space is available
- Verify CSV headers are not modified

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Features Implemented

✅ Multi-step onboarding wizard
✅ User authentication (login/logout)
✅ Password hashing with bcrypt
✅ CSRF protection
✅ File upload with validation
✅ Image dimension checking
✅ User profile management
✅ Pet management (add/edit/delete)
✅ Community member directory
✅ CSV-based data storage
✅ Responsive Bootstrap UI
✅ Session management
✅ Profile view for other users

## License

This project is provided as-is for educational purposes.

## Support

For issues or questions, check the following:
1. Ensure all files are in correct directories
2. Verify file permissions on `/data/` directories
3. Check web server error logs
4. Verify PHP version is 7.4 or higher
5. Ensure Bootstrap 5 CDN is accessible
