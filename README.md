# Web Security Vulnerabilities Lab Report

This repository contains demonstrations of several common web security vulnerabilities. Below is a detailed description of each vulnerability, the complete source code for each vulnerable component, instructions for capturing screenshots, and the best security practices to mitigate them.

## 1. DOM-Based Cross-Site Scripting (XSS)

### Description
DOM-Based XSS occurs when an application contains client-side JavaScript that processes data from an untrusted source (like the URL) in an unsafe way, usually by writing that data directly to the Document Object Model (DOM). In this case, the `user` parameter from the URL is passed directly into `innerHTML`.

### Vulnerable Code
**File:** `Dom-based-xss.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>XSS Demo</title>
</head>
<body>
  <h1 id="welcome"></h1>

  <script>
    const params = new URLSearchParams(window.location.search);
    const user = params.get("user");

    const welcome = document.querySelector("#welcome");

    // VULNERABLE LINE
    welcome.innerHTML = `Welcome back, ${user}!`;
  </script>
</body>
</html>
```

### Screenshot

![DOM XSS Execution](![alt text](image.png))

### Best Security Practices
*   **Use Safe Properties:** Avoid using `innerHTML` when displaying untrusted input. Instead, use `textContent` or `innerText`, which automatically encode special characters and prevent them from being interpreted as HTML/JavaScript.
    *   *Fix:* `welcome.textContent = \`Welcome back, ${user}!\`;`
*   **Sanitize Input:** If rich HTML formatting is absolutely required, use a proven sanitization library like DOMPurify to strip out malicious tags and attributes before assignment.

---

## 2. Reflected Cross-Site Scripting (XSS)

### Description
Reflected XSS happens when user input is immediately returned (reflected) by a web application without being made safe to render.

### Vulnerable Code
**File:** `vulnerable-server.php`

```php
<?php
$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Page</title>
</head>
<body>
<h1>Search</h1>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search..." />
    <button type="submit">Search</button>

<hr>

<h2>
    <?php
    // ❌ VULNERABLE LINE (this causes XSS)
    ?>
</h2>

</body>
</html>
```

### Screenshot
![Reflected XSS Execution](![alt text](image-1.png))
### Best Security Practices
*   **Output Encoding:** Always encode user-controlled data before inserting it into HTML documents. In PHP, use `htmlspecialchars()` to convert special characters (like `<`, `>`, `"`, `'`, `&`) into safely displayable HTML entities.
    *   *Fix:* `echo "You searched for: " . htmlspecialchars($search, ENT_QUOTES, 'UTF-8');`
*   **Content Security Policy (CSP):** Implement a robust CSP to restrict where scripts can be loaded from and block inline script execution.

---


### Description
CSRF is an attack that forces an authenticated user to execute unwanted actions on a web application in which they are currently authenticated. The `change_email.php` script checks if a session exists but fails to verify if the post request was intentionally initiated by the actual user. An attacker can host an auto-submitting form on a different origin to make state changes.

### Vulnerable Code
**File:** `crsf-demo/change_email.php`

```php
<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];

pg_query($conn,
    "UPDATE users SET email='$email' WHERE id=$user_id"
);

echo "Email changed!";
```

**File:** `crsf-demo/attacker.html`

```html
<html>


<form action="http://localhost/csrf-demo/change_email.php" method="POST" id="csrfForm">
    <input type="hidden" name="email" value="hacker@evil.com">
</form>

<script>
</script>
</body>
```
### Screenshot
![alt text](image-6.png)
- The cookies will be used by html file to change the email 

### Best Security Practices
*   **Anti-CSRF Tokens:** Include a cryptographically strong, unpredictable token in every state-changing form. When the form is submitted, the server must verify that the token submitted matches the token stored in the user's session.

---

## 4. SQL Injection (SQLi)

### Description
SQL Injection occurs when user-supplied data is included in a SQL query without proper formatting or escaping. In this example, the `username` and `password` fields are concatenated directly into the database query, allowing an attacker to manipulate the SQL logic.

### Vulnerable Code
**File:** `sql-injection/login.php`

```php
<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM auth_user WHERE username = '$username' AND password = '$password'";

    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        echo "Login successful!";
    } else {
        echo "Invalid credentials";
    }
}

?>


<!DOCTYPE html>
<html>
<body>

<h2>Login</h2>

<form method="POST">
    Username: <input type="text" name="username"><br><br>
    Password: <input type="text" name="password"><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
```

### Screenshot
![alt text](image-2.png)
![alt text](image-3.png)

### Best Security Practices
*   **Prepared Statements (Parameterized Queries):** This is the ultimate defense against SQL injection. Prepared statements ensure that the database treats user input strictly as data (parameters) rather than executable code.
    *   *Fix:* Use `pg_prepare()` and `pg_execute()` instead of raw interpolation, or use an abstraction layer like PDO (`$stmt = $pdo->prepare("SELECT ... WHERE username = :user");`).
*   **Input Validation:** Validate inputs strictly against expected formats (e.g., using regex).
*   **Least Privilege:** Connect to the database using an account that has only the minimum necessary privileges.

---

## 5. Stored Cross-Site Scripting (XSS)

### Description
Stored XSS is a vulnerability where a malicious script is permanently saved to the server's database and safely served back to other legitimate users viewing the compromised content page.

### Vulnerable Code

**File:** `stored-xss/index.php`
```php
<!DOCTYPE html>
<html>
<head>
    <title>Stored XSS Demo</title>
</head>
<body>

<h2>Leave a Comment</h2>

<form action="store.php" method="POST">
    <textarea name="message" rows="4" cols="40"></textarea><br><br>
    <button type="submit">Submit</button>
</form>

<a href="view.php">View Comments</a>

</body>
</html>
```

**File:** `stored-xss/store.php`
```php
<?php

include "db.php";

$message = $_POST['message'];

$query = "INSERT INTO comments (message) VALUES ('$message')";
pg_query($conn, $query);

header("Location: view.php");
exit();

?>
```

**File:** `stored-xss/view.php`
```php
<?php
include "db.php";

$result = pg_query($conn, "SELECT * FROM comments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Comments</title>
</head>
<body>

<h2>Comments</h2>

<?php
while($row = pg_fetch_assoc($result)) {
    echo "<p>" . $row['message'] . "</p>";
}
?>

</body>
</html>
```

### Screenshot
![alt text](image-4.png)
![alt text](image-5.png)

### Best Security Practices
*   **Output Encoding (Context-Aware):** Just like with Reflected XSS, escaping user data is crucial the moment it is retrieved from the database and placed into the HTML response. Use `htmlspecialchars()` in PHP.
    *   *Fix:* `echo "<p>" . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . "</p>";`
*   **Input Validation:** Limit input length and reject unexpected formatting on the server side before storing it.
*   **Separation of Data and Code:** Employ security headers (like Content Security Policy) to add a strong layer of defense.

---


## How to Run the PHP Demos (Reflected XSS, Stored XSS, CSRF, SQLi, etc.)

These demos are implemented in plain PHP and assume you have PHP installed locally. For each PHP demo folder (for example `stored-xss`, `sql-injection`, `crsf-demo`), you can run a local PHP server from the repository root or from the specific folder.

From the specific demo folder:

```bash
cd stored-xss
php -S localhost:8000
# then open: http://localhost:8000/index.php
```

Or run a server from the repository root and navigate to the folder path in the URL:

```bash
php -S localhost:8000
# then open: http://localhost:8000/stored-xss/index.php
```


