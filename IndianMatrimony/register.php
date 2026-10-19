<?php
require "config.php";
if (isLoggedIn()) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $dob = $_POST["dob"] ?? "";
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $religion = trim($_POST["religion"] ?? "Not specified");
    $education = trim($_POST["education"] ?? "Not specified");
    $profession = trim($_POST["profession"] ?? "Not specified");
    $bio = trim($_POST["bio"] ?? "");

    if (!$name || !$email || !$password || !$gender || !$dob || !$city || !$state) $error = "Please fill all required fields.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Enter a valid email.";
    elseif (strlen($password) < 6) $error = "Password must be at least 6 characters.";
    else {
        $bannedCheck = $conn->prepare("SELECT id FROM banned_users WHERE email=?");
        $bannedCheck->bind_param("s",$email);
        $bannedCheck->execute();
        if ($bannedCheck->get_result()->num_rows) {
            $error = "This email address has been permanently banned and cannot be used to create an account.";
        } else {
            $check = $conn->prepare("SELECT id FROM users WHERE email=?");
            $check->bind_param("s",$email); $check->execute();
            if ($check->get_result()->num_rows) $error = "An account with this email already exists.";
            else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(name,email,password,gender,dob,city,state,religion,education,profession,bio) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssssss",$name,$email,$hash,$gender,$dob,$city,$state,$religion,$education,$profession,$bio);
            if ($stmt->execute()) {
                $_SESSION["user_id"] = $conn->insert_id;
                $_SESSION["user_name"] = $name;
                header("Location: dashboard.php"); exit;
            }
            $error = "Could not create profile.";
            }
        }
    }
}
$pageTitle="Create Profile"; require "includes/header.php";
?>
<section class="form-shell">
  <div class="form-intro"><span class="eyebrow">YOUR STORY STARTS HERE</span><h1>Create your profile.</h1><p>Tell people what makes you, you. You can refine your profile later.</p><div class="form-art">♥</div></div>
  <form class="glass-form" method="post">
    <h2>Basic details</h2>
    <?php if($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
    <div class="two-col">
      <label>Full name<input name="name" required value="<?=e($_POST['name']??'')?>"></label>
      <label>Email<input type="email" name="email" required value="<?=e($_POST['email']??'')?>"></label>
      <label>Password<input type="password" name="password" required></label>
      <label>Date of birth<input type="date" name="dob" required value="<?=e($_POST['dob']??'')?>"></label>
      <label>Gender<select name="gender" required><option value="">Choose</option><option>Male</option><option>Female</option><option>Other</option></select></label>
      <label>City<input name="city" required value="<?=e($_POST['city']??'')?>"></label>
      <label>State<input name="state" required value="<?=e($_POST['state']??'')?>"></label>
      <label>Religion<input name="religion" value="<?=e($_POST['religion']??'')?>"></label>
      <label>Education<input name="education" value="<?=e($_POST['education']??'')?>"></label>
      <label>Profession<input name="profession" value="<?=e($_POST['profession']??'')?>"></label>
    </div>
    <label>About you<textarea name="bio" rows="4" placeholder="A little about your personality, interests and what you're looking for..."><?=e($_POST['bio']??'')?></textarea></label>
    <button class="btn btn-primary full" type="submit">Create my profile →</button>
    <p class="form-note">Already registered? <a href="login.php">Log in</a></p>
  </form>
</section>
<?php require "includes/footer.php"; ?>