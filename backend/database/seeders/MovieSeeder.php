<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'titulo' => 'Avengers: Endgame',
                'descripcion' => 'Después de los devastadores eventos de Avengers: Infinity War, el universo está en ruinas. Con la ayuda de los aliados restantes, los Vengadores se reúnen una vez más para revertir las acciones de Thanos y restaurar el equilibrio del universo.',
                'director' => 'Anthony Russo, Joe Russo',
                'actores' => json_encode(['Robert Downey Jr.', 'Chris Evans', 'Mark Ruffalo', 'Chris Hemsworth', 'Scarlett Johansson']),
                'duracion' => 181,
                'clasificacion' => '+12',
                'genero' => 'Acción, Aventura, Ciencia Ficción',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c',
                'omdb_id' => 'tt4154796',
            ],
            [
                'titulo' => 'The Dark Knight',
                'descripcion' => 'Batman se enfrenta a su enemigo más formidable, el Joker, quien intenta sumir Gotham City en la anarquía y obligar a Batman a cruzar la línea entre héroe y vigilante.',
                'director' => 'Christopher Nolan',
                'actores' => json_encode(['Christian Bale', 'Heath Ledger', 'Aaron Eckhart', 'Michael Caine', 'Gary Oldman']),
                'duracion' => 152,
                'clasificacion' => '+12',
                'genero' => 'Acción, Crimen, Drama',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=EXeTwQWrcwY',
                'omdb_id' => 'tt0468569',
            ],
            [
                'titulo' => 'Interstellar',
                'descripcion' => 'Un grupo de exploradores espaciales emprende la misión más importante de la historia de la humanidad: viajar más allá de nuestra galaxia para descubrir si la humanidad tiene un futuro entre las estrellas.',
                'director' => 'Christopher Nolan',
                'actores' => json_encode(['Matthew McConaughey', 'Anne Hathaway', 'Jessica Chastain', 'Michael Caine']),
                'duracion' => 169,
                'clasificacion' => '+12',
                'genero' => 'Ciencia Ficción, Aventura, Drama',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=zSWdZVtXT7E',
                'omdb_id' => 'tt0816692',
            ],
            [
                'titulo' => 'Spider-Man: No Way Home',
                'descripcion' => 'Con la identidad de Spider-Man ahora revelada, Peter pide ayuda al Doctor Strange. Cuando un hechizo sale mal, comienzan a aparecer enemigos peligrosos de otros mundos, obligando a Peter a descubrir lo que realmente significa ser Spider-Man.',
                'director' => 'Jon Watts',
                'actores' => json_encode(['Tom Holland', 'Zendaya', 'Benedict Cumberbatch', 'Jacob Batalon', 'Marisa Tomei']),
                'duracion' => 148,
                'clasificacion' => '+12',
                'genero' => 'Acción, Aventura, Ciencia Ficción',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA',
                'omdb_id' => 'tt10872600',
            ],
            [
                'titulo' => 'Wonder Woman',
                'descripcion' => 'Antes de ser Wonder Woman, era Diana, princesa de las Amazonas, entrenada para ser una guerrera invencible. Criada en una isla paradisíaca protegida, Diana se encuentra con un piloto americano que le informa de un conflicto masivo en el mundo exterior.',
                'director' => 'Patty Jenkins',
                'actores' => json_encode(['Gal Gadot', 'Chris Pine', 'Robin Wright', 'Danny Huston', 'David Thewlis']),
                'duracion' => 141,
                'clasificacion' => '+12',
                'genero' => 'Acción, Aventura, Fantasía',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/gfJGlDaHuWimErCr5Ql0I8x9QSy.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=1Q8fG0TtVAY',
                'omdb_id' => 'tt0451279',
            ],
            [
                'titulo' => 'Black Panther',
                'descripcion' => 'T\'Challa, heredero del reino oculto pero avanzado de Wakanda, debe dar un paso adelante para guiar a su pueblo hacia un nuevo futuro y debe enfrentarse a un rival del pasado de su familia.',
                'director' => 'Ryan Coogler',
                'actores' => json_encode(['Chadwick Boseman', 'Michael B. Jordan', 'Lupita Nyong\'o', 'Danai Gurira', 'Letitia Wright']),
                'duracion' => 134,
                'clasificacion' => '+12',
                'genero' => 'Acción, Aventura, Ciencia Ficción',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/uxzzxijgPIY7slzFvMotPv8wjKA.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=xjDjIWPwcPU',
                'omdb_id' => 'tt1825683',
            ],
            [
                'titulo' => 'Joker',
                'descripcion' => 'En Gotham City, el comediante con problemas mentales Arthur Fleck es ignorado y maltratado por la sociedad. Se adentra en una espiral de revolución y crimen sangriento que lo lleva cara a cara con su alter-ego: el Joker.',
                'director' => 'Todd Phillips',
                'actores' => json_encode(['Joaquin Phoenix', 'Robert De Niro', 'Zazie Beetz', 'Frances Conroy']),
                'duracion' => 122,
                'clasificacion' => '+18',
                'genero' => 'Crimen, Drama, Thriller',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=zAGVQLHvwOY',
                'omdb_id' => 'tt7286456',
            ],
            [
                'titulo' => 'Inception',
                'descripcion' => 'Un ladrón especializado en el robo de secretos del subconsciente se le ofrece la oportunidad de recuperar su vida anterior como pago por una tarea considerada imposible: la implantación de una idea en la mente de un objetivo.',
                'director' => 'Christopher Nolan',
                'actores' => json_encode(['Leonardo DiCaprio', 'Joseph Gordon-Levitt', 'Ellen Page', 'Tom Hardy', 'Ken Watanabe']),
                'duracion' => 148,
                'clasificacion' => '+12',
                'genero' => 'Acción, Ciencia Ficción, Thriller',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/8IB2e4r4oVhHnANbnm7O3Tj6tF8.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=YoHD9XEInc0',
                'omdb_id' => 'tt1375666',
            ],
            [
                'titulo' => 'Guardianes de la Galaxia',
                'descripcion' => 'Un grupo de criminales intergalácticos debe unirse para detener a un guerrero fanático con planes de purgar el universo.',
                'director' => 'James Gunn',
                'actores' => json_encode(['Chris Pratt', 'Zoe Saldana', 'Dave Bautista', 'Vin Diesel', 'Bradley Cooper']),
                'duracion' => 121,
                'clasificacion' => '+12',
                'genero' => 'Acción, Aventura, Comedia',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/r7vmZjiyZw9rpJMQJdXpjgiCOk9.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=d96cjJhvlMA',
                'omdb_id' => 'tt2015381',
            ],
            [
                'titulo' => 'Zack Snyder\'s Justice League',
                'descripcion' => 'Determinado a asegurar que el sacrificio final de Superman no fue en vano, Bruce Wayne une fuerzas con Diana Prince para reclutar un equipo de metahumanos para proteger al mundo de una amenaza inminente de proporciones catastróficas.',
                'director' => 'Zack Snyder',
                'actores' => json_encode(['Ben Affleck', 'Henry Cavill', 'Gal Gadot', 'Jason Momoa', 'Ezra Miller']),
                'duracion' => 242,
                'clasificacion' => '+16',
                'genero' => 'Acción, Aventura, Fantasía',
                'lenguaje' => 'Inglés',
                'imagen' => 'https://image.tmdb.org/t/p/w500/tnAuB8q5vv7Ax9UAEje5Xi4BXik.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=vM-Bja2Gy04',
                'omdb_id' => 'tt12361974',
            ],
        ];

        foreach ($movies as $movie) {
            Movie::create($movie);
        }
    }
}