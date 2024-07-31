<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cli_ar');
define('DB_USER', 'root');
define('DB_PASS', '');
define('TELEGRAM_TOKEN', '');
define('CHAT_ID', '');

$userName = strip_tags(htmlspecialchars(filter_var($_POST["userName"], FILTER_SANITIZE_STRING)));
$brday = filter_var($_POST["birthday"], FILTER_SANITIZE_STRING);
$userEmail = strip_tags(htmlspecialchars(filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)));
$userPhone = filter_var($_POST["userPhone"], FILTER_SANITIZE_STRING);
$consent = filter_var($_POST["consent"], FILTER_SANITIZE_STRING);

if ($consent == "on") {
    $consent = "розиги дода шуд";
}

if (empty($userName) || empty($brday) || empty($userEmail) || empty($userPhone) || empty($consent)) {
    die("All fields are required.");
}

function send_message($chat_id, $text) {
    $token = TELEGRAM_TOKEN;
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = array(
        'chat_id' => $chat_id,
        'text' => $text
    );
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        error_log("Failed to send message to Telegram.");
    }
    return json_decode($result, true);
}


$chat_id = "";


$textReg = " саҳифаи ба қайгири \n ному насаб: {$userName}\n  санаи тавваллуд: {$brday}\n почтаи электронӣ: {$userEmail} \n рқами телефон: {$userPhone} \n розиги: {$consent} 
";
echo $textReg;
 

send_message($chat_id, $textReg);




?>
<?php
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO users (username, brt, useremail, userphone, consent) VALUES (:username, :brt, :useremail, :userphone, :consent)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':username', $userName);
    $stmt->bindParam(':brt', $brday);
    $stmt->bindParam(':useremail', $userEmail);
    $stmt->bindParam(':userphone', $userPhone);
    $stmt->bindParam(':consent', $consent);

    if ($stmt->execute()) {
        $pdo->commit();
        echo "New record created successfully";
    } else {
        $pdo->rollBack();
        echo "Error: " . $stmt->errorInfo()[2];
    }
} catch (Exception $e) {
    $pdo->rollBack();
    die("Failed to insert data: " . $e->getMessage());
}




?>
