<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $admin = User::first();
        }

        $movies = [
            [
                'title' => 'The Shawshank Redemption',
                'genre' => 'Drama',
                'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency. A masterpiece that explores hope, friendship, and the human spirit in the face of institutional brutality.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BNDE3ODcxYzMtY2YzZC00NmNlLWJiNDMtZDViZWM2MzIxZDYwXkEyXkFqcGdeQXVyNjAwNDUxODI@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'The Godfather',
                'genre' => 'Crime',
                'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son. A cinematic masterpiece that defined the crime genre and influenced countless films.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Pulp Fiction',
                'genre' => 'Crime',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption. Tarantino\'s groundbreaking non-linear narrative redefined modern cinema.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'The Dark Knight',
                'genre' => 'Action',
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice. Heath Ledger\'s iconic performance elevates this superhero film to legendary status.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Inception',
                'genre' => 'Sci-Fi',
                'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O. Christopher Nolan\'s mind-bending thriller challenges audiences to question reality itself.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Parasite',
                'genre' => 'Thriller',
                'description' => 'A poor family schemes to become employed by a wealthy family and infiltrate their household by posing as unrelated, highly qualified individuals. Bong Joon-ho\'s Oscar-winning masterpiece is a brilliant social commentary wrapped in a thrilling narrative.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BYWZjMjk3ZTItODQ2ZC00NTY5LWE0ZDYtZTI3MjcwN2Q5NTVkXkEyXkFqcGdeQXVyODk4OTc3MTY@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Spider-Man: Into the Spider-Verse',
                'genre' => 'Animation',
                'description' => 'Teen Miles Morales becomes the Spider-Man of his reality, crossing his path with five counterparts from other dimensions to stop a threat for all realities. A groundbreaking animated film that redefined what superhero movies could be.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BMjMwNDkxMTgzOF5BMl5BanBnXkFtZTgwNTkwNTQ3NjM@._V1_.jpg',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Avengers: Endgame',
                'genre' => 'Action',
                'description' => 'After the devastating events of Infinity War, the Avengers assemble once more to reverse Thanos\' actions and restore balance to the universe. The epic conclusion to over a decade of Marvel storytelling.',
                'cover_image' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_.jpg',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($movies as $movie) {
            Movie::create($movie);
        }
    }
}
