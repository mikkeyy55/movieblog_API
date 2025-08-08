<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Movie Blog API

A Laravel-based Movie Blog API with token authentication using Laravel Sanctum. This project implements role-based access control with Admin and User roles.

## Features

### Authentication
- User registration and login with token-based authentication
- Role-based access control (Admin/User)
- Secure password hashing
- Token management

### Movie Management
- View all movies (public)
- Add new movies (admin only)
- Update movies (admin only)
- Delete movies (admin only)
- Movie details with ratings and comments

### Comments System
- Add comments to movies (authenticated users)
- Update own comments (users)
- Delete own comments (users)
- Delete any comment (admin only)

### Rating System
- Rate movies (1-5 stars, authenticated users)
- Update own ratings
- Delete own ratings
- Prevent duplicate ratings per user per movie

### Watchlist & Favorites System (Users Only)
- Add movies to watchlist (regular users only)
- Add movies to favorites (regular users only)
- View personal watchlist and favorites
- Remove movies from lists
- Toggle watchlist/favorite status

## Database Structure

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - User role (user/admin)
- `created_at`, `updated_at` - Timestamps

### Movies Table
- `id` - Primary key
- `title` - Movie title
- `genre` - Movie genre
- `description` - Movie description
- `cover_image` - Movie cover image URL (optional)
- `created_by` - Foreign key to users table
- `created_at`, `updated_at` - Timestamps

### Comments Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `movie_id` - Foreign key to movies table
- `content` - Comment text
- `created_at`, `updated_at` - Timestamps

### Ratings Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `movie_id` - Foreign key to movies table
- `rating` - Rating value (1-5)
- `created_at`, `updated_at` - Timestamps
- Unique constraint on `user_id` and `movie_id`

## API Endpoints

### Authentication Endpoints

| Endpoint | Method | Access | Description |
|----------|--------|--------|-------------|
| `/api/register` | POST | Public | Register new user |
| `/api/login` | POST | Public | Login and get token |
| `/api/logout` | POST | User | Logout and invalidate token |
| `/api/profile` | GET | User | Get user profile |

### Movie Endpoints

| Endpoint | Method | Access | Description |
|----------|--------|--------|-------------|
| `/api/movies` | GET | Public | View all movies |
| `/api/movies/{id}` | GET | Public | View specific movie |
| `/api/movies` | POST | Admin Only | Add new movie |
| `/api/movies/{id}` | PUT | Admin Only | Update movie |
| `/api/movies/{id}` | DELETE | Admin Only | Delete movie |

### Comment Endpoints

| Endpoint | Method | Access | Description |
|----------|--------|--------|-------------|
| `/api/comments` | GET | Public | View all comments |
| `/api/comments/{id}` | GET | Public | View specific comment |
| `/api/comments` | POST | User Only | Add comment |
| `/api/comments/{id}` | PUT | User Only | Update own comment |
| `/api/comments/{id}` | DELETE | User/Admin | Delete comment |

### Rating Endpoints

| Endpoint | Method | Access | Description |
|----------|--------|--------|-------------|
| `/api/ratings` | GET | Public | View all ratings |
| `/api/ratings/{id}` | GET | Public | View specific rating |
| `/api/ratings` | POST | User Only | Add rating |
| `/api/ratings/{id}` | PUT | User Only | Update own rating |
| `/api/ratings/{id}` | DELETE | User Only | Delete own rating |

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 10

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd GroupProject
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Configure database (SQLite for development)**
   ```bash
   # Create SQLite database
   echo "" > database/database.sqlite
   
   # Set environment variables
   $env:DB_CONNECTION="sqlite"
   $env:DB_DATABASE="database/database.sqlite"
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Testing with Postman

### 1. Setup Postman Collection

Create a new collection in Postman and set up the following environment variables:
- `base_url`: `http://localhost:8000/api`
- `token`: (will be set after login)

### 2. Authentication Flow

#### Register a New User
```
POST {{base_url}}/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```
POST {{base_url}}/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Save the token from the response to use in subsequent requests.**

#### Set Authorization Header
For all protected routes, add this header:
```
Authorization: Bearer {{token}}
```

### 3. Testing Movie Endpoints

#### View All Movies (Public)
```
GET {{base_url}}/movies
```

#### Add New Movie (Admin Only)
```
POST {{base_url}}/movies
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "title": "The Matrix",
    "genre": "Sci-Fi",
    "description": "A computer hacker learns from mysterious rebels about the true nature of his reality.",
    "cover_image": "https://example.com/matrix.jpg"
}
```

#### Update Movie (Admin Only)
```
PUT {{base_url}}/movies/1
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "title": "The Matrix Reloaded",
    "description": "Updated description"
}
```

#### Delete Movie (Admin Only)
```
DELETE {{base_url}}/movies/1
Authorization: Bearer {{admin_token}}
```

### 4. Testing Comment Endpoints

#### Add Comment (User Only)
```
POST {{base_url}}/comments
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "movie_id": 1,
    "content": "Great movie! Highly recommended."
}
```

#### Update Comment (User Only)
```
PUT {{base_url}}/comments/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "content": "Updated comment content"
}
```

### 5. Testing Rating Endpoints

#### Add Rating (User Only)
```
POST {{base_url}}/ratings
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "movie_id": 1,
    "rating": 5
}
```

#### Update Rating (User Only)
```
PUT {{base_url}}/ratings/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "rating": 4
}
```

## Default Users

The seeder creates two default users for testing:

### Admin User
- Email: `admin@movieblog.com`
- Password: `password123`
- Role: `admin`

### Regular User
- Email: `user@movieblog.com`
- Password: `password123`
- Role: `user`

## Sample Data

The MovieSeeder creates 5 sample movies:
1. The Shawshank Redemption (Drama)
2. The Godfather (Crime)
3. Pulp Fiction (Crime)
4. The Dark Knight (Action)
5. Inception (Sci-Fi)

## Error Handling

The API returns appropriate HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error

## Security Features

- Token-based authentication with Laravel Sanctum
- Role-based middleware protection
- Input validation and sanitization
- Password hashing
- CSRF protection (for web routes)
- Rate limiting on API routes

## Technologies Used

- **Laravel 10** - PHP framework
- **Laravel Sanctum** - API authentication
- **SQLite** - Database (for development)
- **Postman** - API testing

## Project Structure

```
GroupProject/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── MovieController.php
│   │   │   ├── CommentController.php
│   │   │   └── RatingController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       └── UserMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Movie.php
│       ├── Comment.php
│       └── Rating.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── README.md
```

## Future Enhancements

- Email verification
- Password reset functionality
- Movie search and filtering
- User profiles
- Movie watchlists
- Comment replies (threaded comments)
- Movie categories and tags
- Analytics and reporting
- File upload for movie covers
- API rate limiting
- Caching for better performance

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
