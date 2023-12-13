
<?php
 error_reporting(E_ALL);
ini_set('display_errors', '1');

$servername = "localhost";
$username = "root";
$password = "Root#roo12";
$dbname = "mydb";



$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Add new user
if (isset($_POST["action"]) && $_POST["action"] === "add_user") {
    $name = sanitizeInput($_POST["name"]);
    $email = sanitizeInput($_POST["email"]);
    $gender = sanitizeInput($_POST["gender"]);
    $birthdate = sanitizeInput($_POST["birthdate"]);
    $country = sanitizeInput($_POST["country"]);
    $hobbies = sanitizeInput($_POST["hobbies"]);

    $image = '';

if (!empty($_FILES["image"]["name"])) {
    if ($_FILES["image"]["error"] === 0) {
        $target_dir = "test/"; // Make sure this path is correct

        // Sanitize the file name to remove special characters
        $filename = basename($_FILES["image"]["name"]);
        $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename); // Remove any characters not allowed in a file name
        $image = $target_dir . $filename;

        // Validate and sanitize file extension (optional)
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }

        // Check if the file is an image (optional)
        if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
            echo "File is not an image.";
            exit;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo "The file " . htmlspecialchars($filename) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Error uploading the file. Error code: " . $_FILES["image"]["error"];
    }
   
}

    
$sql = "INSERT INTO users_ajax (`name`, email, gender, birthdate, country, `image`, hobbies, created_at) 
        VALUES ('$name', '$email', '$gender', '$birthdate', '$country', '$image', '$hobbies', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "User added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


// Check if the form was submitted
//update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] === "update_user") {
            $user_id = $_POST["user_id"];
            $name = sanitizeInput($_POST["name"]);
            $email = sanitizeInput($_POST["email"]);
            $gender = sanitizeInput($_POST["gender"]);
            $birthdate = sanitizeInput($_POST["birthdate"]);
            $country = sanitizeInput($_POST["country"]);
            $hobbies = sanitizeInput($_POST["hobbies"]);

            // Check if the image is being updated
            if (!empty($_FILES["image"]["name"])) {
                $target_dir = "test/"; 
                $filename = basename($_FILES["image"]["name"]);
                $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
                $image = $target_dir . $filename;

                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_extensions)) {
                    echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
                    exit;
                }

                if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
                    echo "File is not an image.";
                    exit;
                }

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
                    echo "The file " . htmlspecialchars($filename) . " has been uploaded and the image path has been updated.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                // If the image is not being updated, keep the existing image path in the database
                $sql_get_image = "SELECT image FROM users_ajax WHERE id='$user_id'";
                $result = $conn->query($sql_get_image);
                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $image = $row['image'];
                }
            }

            // Update the user details in the database
            $sql = "UPDATE users_ajax SET name='$name', email='$email', gender='$gender', birthdate='$birthdate',
                    country='$country', image='$image', hobbies='$hobbies' WHERE id='$user_id'";
            
            if ($conn->query($sql) === TRUE) {
                echo "User updated successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        
        if ($_POST["action"] === "get_user") {
            $user_id = $_POST["user_id"];
            // Fetch user data based on the user ID
    $sql = "SELECT * FROM users_ajax WHERE id = '$user_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo json_encode($user); // Return user data as JSON response
    } else {
        echo "User not found";
    }
        }
    }
}




// Delete user
if (isset($_POST["action"]) && $_POST["action"] === "delete_user") {
    $user_id = $_POST["user_id"];

    $sql = "DELETE FROM users_ajax WHERE id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully!";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

// Fetch all users
if (isset($_POST["action"]) && $_POST["action"] === "fetch_users") {
    $sql = "SELECT * FROM users_ajax";
    $result = $conn->query($sql);
    $users = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
    } else {
        echo "No users found.";
    }
}

$conn->close();
?>
