<!-- starting template voor een html web page-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>video comments</title>
</head>
<body>
<!-- title en embeded youtube video -->
    <h1>Cool video</h1>

    <iframe width="560" height="315" src="https://www.youtube.com/embed/ZuyhdtAxnz4?si=TiwEythDItJRuLLo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>



 

   
<!-- de form die post naar onze database 

echo htmlspecialchars zorgt ervoor dat de charaters die de form worden gezet als nette html charaters wordt doorgestuurdt dus dat beveiligt de form een ebeetje

-->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="comment">Comment:</label><br>
        <textarea id="comment" name="comment" rows="4" cols="50" required></textarea><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>

    <?php

/*
dit vraagt de carbon package op zo dat je die later kan gebruiken ;) 
*/
require_once 'vendor/autoload.php';
use Carbon\Carbon;
/*
functie die er voorzoorgt dat HTML niet wordt laten runnen door speciale charaters weg tehalen 
*/
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
/*
ik kijk of er wat woordt verstuurdt en connect met de database en verstuur alles naar de form ik gebruik de functie nog niet om speciale charaters weg tehalen alleen htmlspecialchars bij het submiten in de html
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $email = $_POST["email"];
    $comment = $_POST["comment"];

    $currentDateTime = Carbon::now();

    // ik connect hier met mn database en log in met $conn
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "module13";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // ik check de connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // hier verstuur ik hem de "ssss" maakt an alles een string 
    $sql = "INSERT INTO feedback (fname, email, comment, submission_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fname, $email, $comment, $currentDateTime);

    if ($stmt->execute()) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    // sluit de connectie
    $stmt->close();
    $conn->close();
}


?>
<?php


// ik verbindt opnieuw naar mn database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "module13";

$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ik slecteer welke database ik wil pakken
$conn->select_db($db_name);

// een SQL query om mn data op te halen
$query = "SELECT fname, email, comment, submission_date FROM feedback";
$result = $conn->query($query);

//dit post all mn rows van mn database ik gebruik hier de fuctie van test_input zodat als mensen stiek html code gebruiken wordt dat niet vertoondt want hij haalt de onderdelen er van weg

if ($result->num_rows > 0) {
    echo "<h2>Data from the Database:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $fname = test_input($row["fname"]);
        $email = test_input($row["email"]);
        $comment = test_input($row["comment"]);        
        $submissionDate = test_input($row["submission_date"]);

        // dit stukje gebruikt Carbon om de date op te slaan en vertoondt het in dagen uren of minuten 
        
        $formattedDate = Carbon::parse($submissionDate)->diffInDays(Carbon::now());

        $daysAgo = Carbon::parse($submissionDate)->diffInDays(Carbon::now());
        
        if ($daysAgo < 1) {
            $hoursAgo = Carbon::parse($submissionDate)->diffInHours(Carbon::now());
            if ($hoursAgo < 1) {
                $minutesAgo = Carbon::parse($submissionDate)->diffInMinutes(Carbon::now());
                echo "<li>Username: $fname <br> email: $email <br> comment: $comment <br> posted $minutesAgo minutes ago";
            } else {
                echo "<li>Username: $fname <br> email: $email <br> comment $comment <br> posted $hoursAgo hours ago </li>";
            }
        } else {
            echo "<li>Username: $fname <br> email: $email <br> comment $comment <br> posted $daysAgo days ago </li>";
        }
    }
    echo "</ul>";
} else {
    echo "No data found in the database.";
}

$conn->close();
?>

</body>
</html>