# Movie API - Setup Guide

## Overview

This Laravel application provides a complete movie database with role-based authentication system featuring:

- **Users**: Can view movies, rate them, and leave comments
- **Admins**: Can create, update, delete movies and manage all user comments
- **Authentication**: Laravel Sanctum for API token-based authentication
- **Frontend**: Blade templates with Bootstrap for web interface

## Quick Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Admin Registration Security
ADMIN_REGISTRATION_KEY=your_secure_admin_key_here

# Database Configuration (if not already set)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=movie_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration (for OTP functionality)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed

# Create admin user (optional)
php artisan db:seed --class=AdminSeeder
```

### 3. Start the Application

```bash
# Start Laravel development server
php artisan serve

# The application will be available at:
# Frontend: http://localhost:8000
# API: http://localhost:8000/api
```

## API Authentication

### For Regular Users
1. **Register**: `POST /api/register`
2. **Login**: `POST /api/login`
3. **Use token**: Add `Authorization: Bearer {token}` header

### For Admins
1. **Register**: `POST /api/admin/register` (requires admin_key)
2. **Login**: `POST /api/admin/login`
3. **Use token**: Add `Authorization: Bearer {token}` header

## Role Permissions

### 👤 User Permissions
- ✅ View all movies (public)
- ✅ View movie details and comments (public)
- ✅ Register and login
- ✅ Rate movies (authenticated)
- ✅ Comment on movies (authenticated)
- ✅ Edit/delete own comments (authenticated)
- ✅ Add movies to watchlist and favorites
- ✅ View personal watchlist and favorites
- ❌ Create, update, or delete movies
- ❌ Delete other users' comments

### 👑 Admin Permissions
- ✅ View all movies (public)
- ✅ View movie details and comments (public)
- ✅ Register and login
- ✅ Rate movies (authenticated)
- ✅ Comment on movies (authenticated)
- ✅ Edit/delete own comments (authenticated)
- ✅ Create new movies
- ✅ Update any movie
- ✅ Delete any movie
- ✅ Delete any user comment
- ✅ View all users
- ❌ Add movies to watchlist and favorites (admin feature disabled)

## Frontend Features

### Navigation
- **Guest users**: See login, register, and admin login options
- **Regular users**: See user badge, profile options, and "My Lists" dropdown
- **Admins**: See admin badge and additional admin tools (no watchlist/favorites)

### Movie Management
- **Public**: Anyone can view movies and ratings
- **Users**: Can rate, comment, and add movies to watchlist/favorites
- **Admins**: Can create, edit, and delete movies (no watchlist/favorites)

### Comment System
- **Users**: Can add, edit, and delete their own comments
- **Admins**: Can delete any comment (marked with "Admin" badge)

### Watchlist & Favorites
- **Users**: Can add movies to watchlist and favorites, view personal lists
- **Admins**: Watchlist and favorites functionality is disabled for admin accounts

## Testing the API

### Using Postman
1. Import the endpoints from `POSTMAN_API_TESTING.md`
2. Set up environment variables for tokens
3. Test the complete user and admin workflows

### Key Test Scenarios
1. **User Flow**: Register → Login → Comment → Rate → Logout
2. **Admin Flow**: Register → Login → Create Movie → Manage Comments → Delete Movie
3. **Permission Testing**: Try accessing admin endpoints with user tokens

## Security Features

### Authentication
- Laravel Sanctum token-based authentication
- Separate admin registration with secure key
- Token expiration and revocation

### Authorization
- Middleware-based role checking
- Resource ownership verification
- Admin privilege escalation for comment management

### Frontend Security
- CSRF protection on all forms
- Role-based UI visibility
- Secure logout with token invalidation

## API Endpoints Summary

### Public Endpoints
- `GET /api/movies` - List all movies
- `GET /api/movies/{id}` - Get movie details
- `POST /api/register` - User registration
- `POST /api/login` - User login

### User Endpoints (Require Authentication)
- `POST /api/comments` - Add comment
- `PUT /api/comments/{id}` - Update own comment
- `DELETE /api/comments/{id}` - Delete own comment
- `POST /api/ratings` - Add rating
- `PUT /api/ratings/{id}` - Update own rating

### Admin Endpoints (Require Admin Token)
- `POST /api/movies` - Create movie
- `PUT /api/movies/{id}` - Update movie
- `DELETE /api/movies/{id}` - Delete movie
- `DELETE /api/admin/comments/{id}` - Delete any comment
- `GET /api/admin/users` - List all users

## Troubleshooting

### Common Issues

1. **"Unauthenticated" Error**
   - Ensure token is included in Authorization header
   - Check token format: `Bearer {token}`
   - Verify token hasn't expired

2. **"Access denied" Error**
   - Check user role (admin vs user)
   - Verify endpoint requires correct permission level
   - Ensure admin is using admin token

3. **Database Connection Issues**
   - Verify `.env` database settings
   - Run `php artisan migrate` to create tables
   - Check database server is running

4. **Admin Registration Fails**
   - Verify `ADMIN_REGISTRATION_KEY` in `.env`
   - Check admin_key parameter in request
   - Ensure email is unique

### Debug Mode
Enable debug mode for detailed error messages:
```env
APP_DEBUG=true
```

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php     # Authentication logic
│   │   ├── MovieController.php    # Movie CRUD operations
│   │   ├── CommentController.php  # Comment management
│   │   └── WebController.php      # Frontend controllers
│   └── Middleware/
│       ├── AdminMiddleware.php    # Admin role verification
│       └── UserMiddleware.php     # User authentication
├── Models/
│   ├── User.php                   # User model with roles
│   ├── Movie.php                  # Movie model
│   └── Comment.php                # Comment model
routes/
├── api.php                        # API routes with middleware
└── web.php                        # Frontend routes
resources/views/                   # Blade templates with role-based UI
```

## Next Steps

1. **Production Deployment**
   - Set `APP_DEBUG=false`
   - Configure proper mail server
   - Set secure `ADMIN_REGISTRATION_KEY`
   - Use HTTPS for API endpoints

2. **Additional Features**
   - User profile management
   - Movie categories and search
   - Advanced rating system
   - Comment moderation

3. **Security Enhancements**
   - Rate limiting for API endpoints
   - Two-factor authentication
   - Advanced admin permissions
   - Audit logging

This setup provides a complete, secure, role-based movie management system ready for both API consumption and web interface usage!
