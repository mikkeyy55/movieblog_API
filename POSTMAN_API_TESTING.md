# Movie API - Postman Testing Guide

This guide provides comprehensive instructions for testing the Movie API with role-based authentication using Postman.

## API Overview

The Movie API uses **Laravel Sanctum** for token-based authentication with two distinct roles:

- **User**: Can view movies, add comments, and rate movies
- **Admin**: Can perform all user actions + create/update/delete movies and delete any comments

## Base URL
```
http://localhost:8000/api
```

## Authentication Headers

For protected routes, include the authorization header:
```
Authorization: Bearer {your_token_here}
```

---

## 🔐 Authentication Endpoints

### 1. User Registration
**POST** `/register`

**Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef123456..."
}
```

### 2. User Login
**POST** `/login`

**Body (JSON):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    },
    "token": "2|abcdef789012..."
}
```

### 3. Admin Registration
**POST** `/admin/register`

**Body (JSON):**
```json
{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "admin123",
    "password_confirmation": "admin123",
    "admin_key": "admin_secret_key"
}
```

**Success Response (201):**
```json
{
    "message": "Admin registered successfully",
    "user": {
        "id": 2,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin"
    },
    "token": "3|abcdef345678..."
}
```

### 4. Admin Login
**POST** `/admin/login`

**Body (JSON):**
```json
{
    "email": "admin@example.com",
    "password": "admin123"
}
```

**Success Response (200):**
```json
{
    "message": "Admin login successful",
    "user": {
        "id": 2,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin"
    },
    "token": "4|abcdef901234..."
}
```

### 5. Logout
**POST** `/logout`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
    "message": "Logged out successfully"
}
```

### 6. Get Profile
**GET** `/profile`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    }
}
```

---

## 🎬 Movie Endpoints

### 1. Get All Movies with Search & Filtering (Public)
**GET** `/movies`

**Query Parameters:**
- `search` (string): Search by movie title
- `genre` (string): Filter by genre
- `year` (integer): Filter by release year
- `min_rating` (float): Filter by minimum rating
- `tag` (string): Filter by tag
- `sort_by` (string): Sort by `rating`, `year`, `title`, `popularity`, or `created_at`
- `sort_order` (string): `asc` or `desc`
- `per_page` (integer): Number of results per page (default: 12)

**Example Request:**
```
GET /api/movies?search=matrix&genre=Sci-Fi&sort_by=rating&sort_order=desc
```

**Success Response (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "The Matrix",
            "genre": "Sci-Fi",
            "description": "A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.",
            "release_year": 1999,
            "cover_image": "https://example.com/matrix.jpg",
            "created_by": 2,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z",
            "creator": {
                "id": 2,
                "name": "Admin User"
            },
            "tags": [
                {"id": 1, "tag": "action"},
                {"id": 2, "tag": "cyberpunk"}
            ],
            "ratings_avg_rating": 4.5,
            "ratings_count": 10,
            "watchlists_count": 25
        }
    ],
    "total": 1,
    "per_page": 12,
    "current_page": 1,
    "last_page": 1
}
```

### 2. Get Single Movie (Public)
**GET** `/movies/{id}`

**Success Response (200):**
```json
{
    "movie": {
        "id": 1,
        "title": "The Matrix",
        "genre": "Sci-Fi",
        "description": "A computer hacker learns...",
        "cover_image": "https://example.com/matrix.jpg",
        "created_by": 2,
        "creator": {
            "id": 2,
            "name": "Admin User"
        },
        "comments": [
            {
                "id": 1,
                "content": "Great movie!",
                "user": {
                    "id": 1,
                    "name": "John Doe"
                },
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "ratings": [
            {
                "id": 1,
                "rating": 5,
                "user": {
                    "id": 1,
                    "name": "John Doe"
                }
            }
        ],
        "ratings_avg_rating": 4.5,
        "ratings_count": 10
    }
}
```

### 3. Create Movie (Admin Only)
**POST** `/movies`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "title": "Inception",
    "genre": "Sci-Fi",
    "description": "A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.",
    "release_year": 2010,
    "cover_image": "https://example.com/inception.jpg",
    "tags": ["action", "mind-bending", "leonardo-dicaprio"]
}
```

**Success Response (201):**
```json
{
    "message": "Movie created successfully",
    "movie": {
        "id": 2,
        "title": "Inception",
        "genre": "Sci-Fi",
        "description": "A thief who steals corporate secrets...",
        "cover_image": "https://example.com/inception.jpg",
        "created_by": 2,
        "creator": {
            "id": 2,
            "name": "Admin User"
        }
    }
}
```

### 4. Update Movie (Admin Only)
**PUT** `/movies/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "title": "Inception (Updated)",
    "genre": "Sci-Fi/Thriller",
    "description": "Updated description...",
    "cover_image": "https://example.com/inception-new.jpg"
}
```

**Success Response (200):**
```json
{
    "message": "Movie updated successfully",
    "movie": {
        "id": 2,
        "title": "Inception (Updated)",
        "genre": "Sci-Fi/Thriller",
        "description": "Updated description...",
        "cover_image": "https://example.com/inception-new.jpg"
    }
}
```

### 5. Delete Movie (Admin Only)
**DELETE** `/movies/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "message": "Movie deleted successfully"
}
```

---

## 💬 Comment Endpoints

### 1. Get All Comments (Public)
**GET** `/comments`

**Success Response (200):**
```json
{
    "comments": [
        {
            "id": 1,
            "content": "Great movie!",
            "user_id": 1,
            "movie_id": 1,
            "user": {
                "id": 1,
                "name": "John Doe"
            },
            "movie": {
                "id": 1,
                "title": "The Matrix"
            },
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

### 2. Get Single Comment (Public)
**GET** `/comments/{id}`

### 3. Create Comment (Authenticated Users)
**POST** `/comments`

**Headers:**
```
Authorization: Bearer {user_or_admin_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1,
    "content": "This is an amazing movie! Highly recommended."
}
```

**Success Response (201):**
```json
{
    "message": "Comment added successfully",
    "comment": {
        "id": 2,
        "content": "This is an amazing movie! Highly recommended.",
        "user_id": 1,
        "movie_id": 1,
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Update Comment (Own Comments or Admin)
**PUT** `/comments/{id}`

**Headers:**
```
Authorization: Bearer {user_or_admin_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "content": "Updated comment content here."
}
```

### 5. Delete Comment (Own Comments or Admin)
**DELETE** `/comments/{id}`

**Headers:**
```
Authorization: Bearer {user_or_admin_token}
```

### 6. Admin Delete Any Comment
**DELETE** `/admin/comments/{id}`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "message": "Comment deleted by admin successfully"
}
```

---

## ⭐ Rating Endpoints

### 1. Get All Ratings (Public)
**GET** `/ratings`

### 2. Get Single Rating (Public)
**GET** `/ratings/{id}`

### 3. Create Rating (Authenticated Users)
**POST** `/ratings`

**Headers:**
```
Authorization: Bearer {user_or_admin_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1,
    "rating": 5
}
```

**Success Response (201):**
```json
{
    "message": "Rating added successfully",
    "rating": {
        "id": 1,
        "rating": 5,
        "user_id": 1,
        "movie_id": 1,
        "user": {
            "id": 1,
            "name": "John Doe"
        }
    }
}
```

### 4. Update Rating (Own Ratings)
**PUT** `/ratings/{id}`

### 5. Delete Rating (Own Ratings)
**DELETE** `/ratings/{id}`

---

## 🎭 Movie Discovery Endpoints (Public)

### 1. Get Trending Movies
**GET** `/movies/trending/list`

**Query Parameters:**
- `days` (integer): Number of days to look back (default: 7)
- `limit` (integer): Number of movies to return (default: 10)

**Example Request:**
```
GET /api/movies/trending/list?days=7&limit=5
```

**Success Response (200):**
```json
{
    "trending_movies": [
        {
            "id": 1,
            "title": "The Matrix",
            "genre": "Sci-Fi",
            "release_year": 1999,
            "creator": {"id": 2, "name": "Admin User"},
            "tags": [{"id": 1, "tag": "action"}],
            "ratings_avg_rating": 4.5,
            "ratings_count": 10,
            "comments_count": 15,
            "recent_ratings_count": 8
        }
    ],
    "period_days": 7
}
```

### 2. Get Top-Rated Movies
**GET** `/movies/top-rated/list`

**Query Parameters:**
- `limit` (integer): Number of movies to return (default: 10)
- `min_ratings` (integer): Minimum number of ratings required (default: 3)

**Success Response (200):**
```json
{
    "top_rated_movies": [
        {
            "id": 1,
            "title": "The Matrix",
            "genre": "Sci-Fi",
            "release_year": 1999,
            "ratings_avg_rating": 4.8,
            "ratings_count": 50
        }
    ],
    "min_ratings_required": 3
}
```

### 3. Get Popular Movies (Most in Watchlists)
**GET** `/movies/popular/list`

**Query Parameters:**
- `limit` (integer): Number of movies to return (default: 10)

**Success Response (200):**
```json
{
    "popular_movies": [
        {
            "id": 1,
            "title": "The Matrix",
            "watchlists_count": 120,
            "ratings_avg_rating": 4.5
        }
    ]
}
```

### 4. Get All Genres
**GET** `/movies/genres/list`

**Success Response (200):**
```json
{
    "genres": ["Action", "Comedy", "Drama", "Horror", "Sci-Fi", "Thriller"]
}
```

### 5. Get All Tags
**GET** `/movies/tags/list`

**Success Response (200):**
```json
{
    "tags": ["action", "blockbuster", "classic", "cyberpunk", "superhero"]
}
```

### 6. Get Movies by Genre
**GET** `/movies/genre/{genre}`

**Query Parameters:**
- `per_page` (integer): Number of results per page (default: 12)

**Example Request:**
```
GET /api/movies/genre/Sci-Fi?per_page=5
```

---

## 📋 Watchlist & Favorites Endpoints (Regular Users Only)

**Note**: These endpoints are restricted to regular users only. Admin accounts cannot use watchlist/favorites functionality.

### 1. Get User's Watchlist
**GET** `/watchlist`

**Headers:**
```
Authorization: Bearer {user_token}
```

**Success Response (200):**
```json
{
    "watchlist": [
        {
            "id": 1,
            "user_id": 1,
            "movie_id": 1,
            "type": "watchlist",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "movie": {
                "id": 1,
                "title": "The Matrix",
                "genre": "Sci-Fi",
                "creator": {"id": 2, "name": "Admin User"},
                "ratings_avg_rating": 4.5
            }
        }
    ]
}
```

### 2. Get User's Favorites
**GET** `/favorites`

**Headers:**
```
Authorization: Bearer {user_token}
```

### 3. Add Movie to Watchlist
**POST** `/watchlist/add`

**Headers:**
```
Authorization: Bearer {user_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1
}
```

**Success Response (201):**
```json
{
    "message": "Movie added to watchlist successfully",
    "watchlist_item": {
        "id": 1,
        "user_id": 1,
        "movie_id": 1,
        "type": "watchlist",
        "movie": {
            "id": 1,
            "title": "The Matrix"
        }
    }
}
```

### 4. Add Movie to Favorites
**POST** `/favorites/add`

**Headers:**
```
Authorization: Bearer {user_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1
}
```

### 5. Remove Movie from Watchlist
**DELETE** `/watchlist/{movieId}`

**Headers:**
```
Authorization: Bearer {user_token}
```

**Success Response (200):**
```json
{
    "message": "Movie removed from watchlist successfully"
}
```

### 6. Remove Movie from Favorites
**DELETE** `/favorites/{movieId}`

**Headers:**
```
Authorization: Bearer {user_token}
```

### 7. Toggle Movie in Watchlist
**POST** `/watchlist/toggle`

**Headers:**
```
Authorization: Bearer {user_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1
}
```

**Success Response (200):**
```json
{
    "message": "Movie added to watchlist",
    "in_watchlist": true,
    "watchlist_item": {
        "id": 1,
        "movie": {"id": 1, "title": "The Matrix"}
    }
}
```

### 8. Toggle Movie in Favorites
**POST** `/favorites/toggle`

**Headers:**
```
Authorization: Bearer {user_token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "movie_id": 1
}
```

**Success Response (200):**
```json
{
    "message": "Movie added to favorites",
    "is_favorite": true,
    "favorite_item": {
        "id": 1,
        "movie": {"id": 1, "title": "The Matrix"}
    }
}
```

---

## 👥 Admin Endpoints

### 1. Get All Users (Admin Only)
**GET** `/admin/users`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "users": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "comments": [],
            "ratings": []
        },
        {
            "id": 2,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin",
            "comments": [],
            "ratings": []
        }
    ]
}
```

---

## 🧪 Testing Scenarios

### Scenario 1: Complete User Flow
1. **Register** a new user
2. **Login** with user credentials
3. **Get all movies** with search and filters (public access)
4. **View trending movies** and **top-rated movies**
5. **View a specific movie** with details and user status
6. **Add movie to watchlist** and **favorites**
7. **Add a comment** to a movie
8. **Rate a movie**
9. **View personal watchlist** and **favorites**
10. **Update own comment**
11. **Try to create a movie** (should fail - 403 Forbidden)
12. **Remove movie from watchlist**
13. **Logout**

### Scenario 2: Complete Admin Flow
1. **Register** a new admin (with admin_key)
2. **Login** with admin credentials
3. **Create a new movie** with tags and release year
4. **Update the movie** information
5. **View all users**
6. **Get trending** and **popular movies**
7. **Delete any user comment** (admin privilege)
8. **Delete the movie**
9. **Logout**

### Scenario 3: Search & Discovery Testing
1. **Search movies** by title
2. **Filter movies** by genre, year, and rating
3. **Sort movies** by different criteria
4. **Get movies by specific genre**
5. **View available genres** and **tags**
6. **Get trending movies** for different time periods
7. **Get top-rated movies** with different minimum ratings

### Scenario 4: Watchlist & Favorites Testing (Users Only)
1. **Login as regular user**
2. **Add movies to watchlist** and **favorites**
3. **Toggle movies** in/out of watchlist
4. **View personal lists**
5. **Remove movies** from lists
6. **Try adding same movie twice** (should handle gracefully)
7. **Login as admin** and verify watchlist/favorites are not available

### Scenario 5: Permission Testing
1. **Login as regular user**
2. **Try to access admin endpoints** (should fail - 403 Forbidden)
3. **Try to delete another user's comment** (should fail - 403 Forbidden)
4. **Access own watchlist** and **favorites** (should succeed)
5. **Login as admin**
6. **Access all admin endpoints** (should succeed)
7. **Delete any comment** (should succeed)
8. **View all users** (should succeed)
9. **Verify admin cannot access watchlist/favorites** (should not see buttons/links)

---

## 🚫 Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "Access denied. Admin privileges required."
}
```

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\Movie] 999"
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

---

## 📋 Postman Collection Setup

### Environment Variables
Create a Postman environment with these variables:

- `base_url`: `http://localhost:8000/api`
- `user_token`: (will be set automatically after login)
- `admin_token`: (will be set automatically after admin login)

### Pre-request Scripts
For requests requiring authentication, add this pre-request script:

```javascript
// For user endpoints
pm.environment.set("Authorization", "Bearer " + pm.environment.get("user_token"));

// For admin endpoints
pm.environment.set("Authorization", "Bearer " + pm.environment.get("admin_token"));
```

### Test Scripts
Add this test script to login requests to automatically save tokens:

```javascript
// For user login
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("user_token", jsonData.token);
}

// For admin login
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("admin_token", jsonData.token);
}
```

---

## 🔧 Laravel Setup for Testing

Before testing, ensure your Laravel application is properly configured:

1. **Start the server:**
   ```bash
   php artisan serve
   ```

2. **Run migrations:**
   ```bash
   php artisan migrate
   ```

3. **Seed admin user (optional):**
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

4. **Set admin registration key in `.env`:**
   ```
   ADMIN_REGISTRATION_KEY=admin_secret_key
   ```

---

## 🎯 Key Testing Points

✅ **Authentication Flow**
- User registration and login
- Admin registration and login
- Token generation and validation

✅ **Role-based Access Control**
- Users can't access admin endpoints
- Admins can access all endpoints
- Proper permission checks on resources

✅ **Movie Management**
- Public access to view movies
- Admin-only creation, update, deletion

✅ **Comment System**
- Users can comment on movies
- Users can only edit/delete own comments
- Admins can delete any comment

✅ **Rating System**
- Users can rate movies
- Users can only manage own ratings

This comprehensive guide covers all API endpoints with proper role-based permissions as specified in your requirements!
