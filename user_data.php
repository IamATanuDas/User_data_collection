<?php
// ======== CONFIG ========
$host = "localhost";
$user = "root";       // your MySQL username
$pass = "";           // your MySQL password
$db   = "user_data";  // database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// ======== AJAX SAVE ========
if (isset($_POST['action']) && $_POST['action'] == "save") {
    $stmt = $conn->prepare("INSERT INTO users 
        (first_name, middle_name, last_name, age, sex, father_name, mother_name, vill, post, ps, dist, pin_code, state, country) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissssssssss",
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['age'],
        $_POST['sex'],
        $_POST['father_name'],
        $_POST['mother_name'],
        $_POST['vill'],
        $_POST['post'],
        $_POST['ps'],
        $_POST['dist'],
        $_POST['pin_code'],
        $_POST['state'],
        $_POST['country']
    );
    if ($stmt->execute()) {
        echo "✅ Data Saved Successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    exit;
}

// ======== SEARCH USERS (AJAX) ========
if (isset($_POST['action']) && $_POST['action'] == "search") {
    $q = "%" . $_POST['query'] . "%";
    $stmt = $conn->prepare("SELECT * FROM users WHERE 
        first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR father_name LIKE ? OR mother_name LIKE ? OR vill LIKE ? OR dist LIKE ?");
    $stmt->bind_param("sssssss", $q,$q,$q,$q,$q,$q,$q);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<table border='1' cellpadding='6' cellspacing='0'>
                <tr style='background:#007bff;color:#fff'>
                  <th>ID</th><th>Name</th><th>Age</th><th>Sex</th>
                  <th>Father</th><th>Mother</th>
                  <th>Address</th><th>PIN</th><th>State</th><th>Country</th>
                </tr>";
        while($row = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</td>
                    <td>{$row['age']}</td>
                    <td>{$row['sex']}</td>
                    <td>{$row['father_name']}</td>
                    <td>{$row['mother_name']}</td>
                    <td>{$row['vill']}, {$row['post']}, {$row['ps']}, {$row['dist']}</td>
                    <td>{$row['pin_code']}</td>
                    <td>{$row['state']}</td>
                    <td>{$row['country']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No records found!";
    }
    exit;
}

// ======== DOWNLOAD EXCEL ========
if (isset($_GET['download']) && $_GET['download'] == "excel") {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=user_data.xls");

    $res = $conn->query("SELECT * FROM users");
    echo "ID\tFirst Name\tMiddle Name\tLast Name\tAge\tSex\tFather Name\tMother Name\tVillage\tPost\tPS\tDistrict\tPin Code\tState\tCountry\n";
    while ($row = $res->fetch_assoc()) {
        echo implode("\t", $row) . "\n";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Data Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f7f9;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 30px;
    }
    .container {
      background: #fff;
      padding: 20px 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 750px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }
    input, select, button {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      width: 100%;
    }
    button {
      background: #007bff;
      color: #fff;
      cursor: pointer;
      border: none;
      margin-top: 10px;
    }
    button:hover { background: #0056b3; }
    #msg { margin-top: 15px; font-weight: bold; color: green; }
    #results { margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #007bff; color: white; }
  </style>
</head>
<body>
  <div class="container">
    <h2>User Registration Form</h2>
    <!-- User Form -->
    <form id="userForm">
      <div class="grid">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="middle_name" placeholder="Middle Name">
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="number" name="age" placeholder="Age" required>
        <select name="sex" required>
          <option value="">Select Sex</option>
          <option>Male</option>
          <option>Female</option>
          <option>Other</option>
        </select>
        <input type="text" name="father_name" placeholder="Father's Name" required>
        <input type="text" name="mother_name" placeholder="Mother's Name" required>
        <input type="text" name="vill" placeholder="Village" required>
        <input type="text" name="post" placeholder="Post" required>
        <input type="text" name="ps" placeholder="Police Station" required>
        <input type="text" name="dist" placeholder="District" required>
        <input type="text" name="pin_code" placeholder="PIN Code" required>
        <input type="text" name="state" placeholder="State" required>
        <input type="text" name="country" value="India" readonly>
      </div>
      <button type="submit">Save Data</button>
    </form>
    <button onclick="window.location='?download=excel'">Download Excel</button>
    <div id="msg"></div>

    <hr>

    <!-- Search Box -->
    <h2>Search Users</h2>
    <div style="display:flex;gap:10px;">
      <input type="text" id="searchQuery" placeholder="Enter name, father, mother, village, district...">
      <button onclick="searchUser()">Search</button>
    </div>
    <div id="results"></div>
  </div>

  <script>
    // Save user via AJAX
    document.getElementById("userForm").addEventListener("submit", function(e){
      e.preventDefault();
      let formData = new FormData(this);
      formData.append("action", "save");

      fetch("user_data.php", { method: "POST", body: formData })
      .then(res => res.text())
      .then(data => {
        document.getElementById("msg").innerHTML = data;
        if (data.includes("✅")) this.reset();
      })
      .catch(err => console.error(err));
    });

    // Search users via AJAX
    function searchUser() {
      let query = document.getElementById("searchQuery").value.trim();
      if (query === "") {
        document.getElementById("results").innerHTML = "⚠️ Please enter search text.";
        return;
      }
      let formData = new FormData();
      formData.append("action", "search");
      formData.append("query", query);

      fetch("user_data.php", { method: "POST", body: formData })
      .then(res => res.text())
      .then(data => {
        document.getElementById("results").innerHTML = data;
      })
      .catch(err => console.error(err));
    }
  </script>
</body>
</html>
