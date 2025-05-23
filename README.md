 ### Fetch data from TMDB API using:
   ```
   php artisan tmdb:fetch
   
   ```
### API Endpoints
- **GET /api/movies**
  - Retrieves a paginated list of movies
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en
    - `per_page`: Number of items per page. Default: 15
    - `page`: Page number. Default: 1
    - `genre`: Filter movies by genre ID

- **GET /api/movies/{id}**
  - Retrieves detailed information for a specific movie
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en

- **GET /api/series**
  - Retrieves a paginated list of TV series
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en
    - `per_page`: Number of items per page. Default: 15
    - `page`: Page number. Default: 1
    - `genre`: Filter series by genre ID

- **GET /api/series/{id}**
  - Retrieves detailed information for a specific TV series
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en

- **GET /api/genres**
  - Retrieves a list of all genres
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en

- **GET /api/genres/{id}**
  - Retrieves information for a specific genre
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en

- **GET /api/genres/{id}/movies**
  - Retrieves a paginated list of movies belonging to a specific genre
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en
    - `per_page`: Number of items per page. Default: 15
    - `page`: Page number. Default: 1

- **GET /api/genres/{id}/series**
  - Retrieves a paginated list of TV series belonging to a specific genre
  - Query Parameters:
    - `language`: Language code (en, pl, de). Default: en
    - `per_page`: Number of items per page. Default: 15
    - `page`: Page number. Default: 1

## Example Responses

### GET /api/movies

```json
{
  "data": [
    {
      "id": 1,
      "tmdb_id": 123,
      "title": "Movie Title",
      "overview": "Movie overview text...",
      "poster_path": "/path/to/poster.jpg",
      "backdrop_path": "/path/to/backdrop.jpg",
      "vote_average": 7.5,
      "vote_count": 1200,
      "release_date": "2023-01-15",
      "genres": [
        {
          "id": 1,
          "name": "Action"
        },
        {
          "id": 2,
          "name": "Drama"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 50
  }
}
```

### GET /api/genres/{id}/movies

```json
{
  "data": [
    {
      "id": 5,
      "tmdb_id": 456,
      "title": "Horror Movie Title",
      "overview": "Horror movie overview text...",
      "poster_path": "/path/to/poster.jpg",
      "backdrop_path": "/path/to/backdrop.jpg",
      "vote_average": 6.8,
      "vote_count": 950,
      "release_date": "2022-10-31",
      "genres": [
        {
          "id": 27,
          "name": "Horror"
        },
        {
          "id": 53,
          "name": "Thriller"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 18
  }
}
```

### GET /api/genres/{id}/series

```json
{
  "data": [
    {
      "id": 3,
      "tmdb_id": 789,
      "name": "Horror Series Title",
      "overview": "Horror series overview text...",
      "poster_path": "/path/to/poster.jpg",
      "backdrop_path": "/path/to/backdrop.jpg",
      "vote_average": 8.2,
      "vote_count": 1500,
      "first_air_date": "2020-09-15",
      "genres": [
        {
          "id": 27,
          "name": "Horror"
        },
        {
          "id": 10765,
          "name": "Sci-Fi & Fantasy"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 5
  }
}
```

### GET api/genres?language=pl

```json
[
  {
    "id": 2,
    "tmdb_id": 28,
    "name": "Akcja"
  },
  {
    "id": 3,
    "tmdb_id": 12,
    "name": "Przygodowy"
  },
  {
    "id": 4,
    "tmdb_id": 16,
    "name": "Animacja"
  },
  {
    "id": 5,
    "tmdb_id": 35,
    "name": "Komedia"
  }
]
```