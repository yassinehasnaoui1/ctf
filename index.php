<?php
// Database connection
$conn = mysqli_connect("localhost", "reviewflix_user", "your_password", "reviewflix");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch parameters from URL
$page = $_GET['page'] ?? 'home';
$title = $_GET['title'] ?? null;
$movie_id = $_GET['movie_id'] ?? null;

// Display SQL errors for educational purposes
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MovieReviewr - Your Cinema Guide</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #e74c3c;
            --bg-color: #1a1a2e;
            --card-bg: #16213e;
            --text-color: #e1e1e1;
            --border-color: #30475e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            padding: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent-color);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        nav a {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            margin: 0 0.5rem;
            border: 2px solid var(--accent-color);
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        nav a:hover {
            background: var(--accent-color);
            color: white;
        }
        
        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .feature-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .feature-card h3 {
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }
        
        .movie-card {
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 2rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .movie-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-color);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .movie-card:hover::before {
            transform: scaleX(1);
        }
        
        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.2);
        }
        
        .movie-title {
            font-size: 1.8rem;
            color: var(--accent-color);
            margin: 0 0 1rem 0;
        }
        
        .movie-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .movie-stats {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .stat {
            padding: 0.5rem;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .search-box {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .search-input {
            width: 100%;
            max-width: 600px;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            background: var(--bg-color);
            color: var(--text-color);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .error-box {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--accent-color);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            color: var(--accent-color);
        }
        
        .review-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
        }
        
        .rating {
            color: var(--accent-color);
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php" class="logo">MovieReviewr</a>
            <nav>
                <a href="index.php?page=home">Home</a>
                <a href="index.php?page=search">Search</a>
            </nav>
        </div>
    </header>
    
    <main>
        <?php
        // Home Page
        if ($page == 'home') {
            echo '<div class="features">
                    <div class="feature-card">
                        <h3>Trending Now</h3>
                        <p>Discover what movies are hot this week</p>
                    </div>
                    <div class="feature-card">
                        <h3>Latest Reviews</h3>
                        <p>Fresh opinions from our community</p>
                    </div>
                    <div class="feature-card">
                        <h3>Top Rated</h3>
                        <p>Highest rated films of all time</p>
                    </div>
                  </div>';
            
            echo '<h1>Featured Movies</h1>';
            echo '<div class="movie-grid">';
            
            $featured_movies = [
                ['id' => 1, 'title' => 'Inception', 'year' => '2010', 'genre' => 'Sci-Fi', 'rating' => '9.3', 'reviews' => '2.4M'],
                ['id' => 2, 'title' => 'Interstellar', 'year' => '2014', 'genre' => 'Sci-Fi', 'rating' => '8.9', 'reviews' => '1.8M'],
                ['id' => 3, 'title' => 'The Dark Knight', 'year' => '2008', 'genre' => 'Action', 'rating' => '9.0', 'reviews' => '2.9M'],
                ['id' => 4, 'title' => 'Pulp Fiction', 'year' => '1994', 'genre' => 'Crime', 'rating' => '8.8', 'reviews' => '2.1M']
            ];
            
            foreach ($featured_movies as $movie) {
                echo '<div class="movie-card">';
                echo '<h3 class="movie-title">' . htmlspecialchars($movie['title']) . '</h3>';
                echo '<div class="movie-meta">';
                echo '<span>' . htmlspecialchars($movie['year']) . '</span>';
                echo '<span>' . htmlspecialchars($movie['genre']) . '</span>';
                echo '</div>';
                echo '<div class="movie-stats">';
                echo '<span class="stat">★ ' . htmlspecialchars($movie['rating']) . '</span>';
                echo '<span class="stat">' . htmlspecialchars($movie['reviews']) . ' reviews</span>';
                echo '</div>';
                echo '<a href="index.php?page=reviews&movie_id=' . $movie['id'] . '" class="btn">Read Reviews</a>';
                echo '</div>';
            }
            echo '</div>';
        }
        
        // Search Page
        elseif ($page == 'search') {
            echo '<div class="search-box">';
            echo '<h2>Search Movies</h2>';
            echo '<form action="index.php" method="GET">';
            echo '<input type="hidden" name="page" value="search">';
            echo '<input type="text" name="title" class="search-input" placeholder="Enter movie title..." value="' . htmlspecialchars($title ?? '') . '">';
            echo '<button type="submit" class="btn">Search</button>';
            echo '</form>';
            echo '</div>';
            
            if ($title) {
                try {
                    $query = "SELECT * FROM movies WHERE title = '$title'"; // Intentional SQLi vulnerability
                    $result = mysqli_query($conn, $query);
                    echo '<div class="movie-grid">';
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="movie-card">';
                        echo '<h3 class="movie-title">' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<div class="movie-meta">';
                        echo '<span>Released: ' . htmlspecialchars($row['year']) . '</span>';
                        echo '<span>Genre: ' . htmlspecialchars($row['genre']) . '</span>';
                        echo '</div>';
                        echo '<a href="index.php?page=reviews&movie_id=' . $row['id'] . '" class="btn">Read Reviews</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                } catch (mysqli_sql_exception $e) {
                    echo '<div class="error-box">';
                    echo 'SQL Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
        }
        
        // Reviews Page
        elseif ($page == 'reviews' && $movie_id) {
            try {
                $query = "SELECT * FROM reviews WHERE movie_id = '$movie_id'"; // Intentional SQLi vulnerability
                $result = mysqli_query($conn, $query);
                echo "<h2>Movie Reviews</h2>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="review-card">';
                    echo '<div class="review-meta">';
                    echo '<span>By ' . htmlspecialchars($row['user']) . '</span>';
                    echo '<span class="rating">' . htmlspecialchars($row['rating']) . '/5 ★</span>';
                    echo '</div>';
                    echo '<p>' . htmlspecialchars($row['comment']) . '</p>';
                    echo '</div>';
                }
            } catch (mysqli_sql_exception $e) {
                echo '<div class="error-box">';
                echo 'SQL Error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        }
        ?>
    </main>
</body>
</html>
<?php mysqli_close($conn); ?>
