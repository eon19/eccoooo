<?php
$conn = new mysqli("localhost", "root", "", "countries_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

if ($search != "") {
    $stmt = $conn->prepare("SELECT * FROM countries WHERE name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM countries");
}

$countries = [];
while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Country Carousel</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }

        /* Top Search Bar */
        .top-bar {
            width: 100%;
            background: #0315df;
            padding: 20px;
            text-align: center;
        }

        .top-bar input {
            padding: 10px;
            width: 300px;
            margin-right: 10px;
            font-size: 16px;
        }

        .top-bar button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background: #4CAF50;
            color: white;
            border: none;
        }

        /* Main Container */
        .container {
            width: 900px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border: 1px solid #1303a4;
            position: relative;
        }

        /* Carousel */
        .carousel {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            text-align: center;
        }

        .slide img {
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 2px solid #333;
        }

        .country-info {
            margin-top: 15px;
            font-size: 20px;
            padding: 15px;
            width: 400px;
            margin: 0 auto;
            text-align: center;
        }

        /* Previous & Next Buttons */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px 15px;
            cursor: pointer;
            user-select: none;
            border-radius: 5px;
        }

        .prev {
            left: 10px;
        }

        .next {
            right: 10px;
        }

        /* Center text for country and capital */
        .country-info strong {
            font-size: 24px;
            display: block;
            margin-bottom: 10px;
        }

        .country-info span {
            display: block;
            font-size: 18px;
        }

    </style>
</head>
<body>


<div class="top-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search country..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
</div>


<div class="container">

    <?php if(count($countries) > 0): ?>

    <div class="carousel">
        <div class="slides" id="slides">

            <?php foreach($countries as $country): ?>
                <div class="slide">
                    <img src="<?= $country['flag_url'] ?>">
                    <div class="country-info">
                        <strong><?= $country['name'] ?></strong>
                        <span>Capital: <?= $country['capital'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="nav-btn prev" onclick="moveSlide(-1)">&#10094;</div>
        <div class="nav-btn next" onclick="moveSlide(1)">&#10095;</div>

    </div>

    <?php else: ?>
        <h2 style="text-align:center;">No country found</h2>
    <?php endif; ?>

</div>

<script>
let index = 0;

function moveSlide(step) {
    const slides = document.getElementById('slides');
    const totalSlides = document.querySelectorAll('.slide').length;

    index += step;

    if (index < 0) index = totalSlides - 1;
    if (index >= totalSlides) index = 0;

    slides.style.transform = 'translateX(' + (-index * 100) + '%)';
}
</script>

</body>
</html>