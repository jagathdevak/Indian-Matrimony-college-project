<?php
require "config.php";
if (isLoggedIn()) { header("Location: dashboard.php"); exit; }
$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $email=trim($_POST["email"]??""); $password=$_POST["password"]??"";
    $stmt=$conn->prepare("SELECT id,name,password FROM users WHERE email=? AND banned=0");
    $stmt->bind_param("s",$email); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
    if($u && password_verify($password,$u["password"])){
        $_SESSION["user_id"]=$u["id"]; $_SESSION["user_name"]=$u["name"];
        header("Location: dashboard.php"); exit;
    } else {
        $error="Email or password is incorrect.";
    }
}
$pageTitle="Login"; require "includes/header.php";
?>
<section class="auth-page">
  <div class="auth-card">
    <div class="auth-icon">♥</div><span class="eyebrow">WELCOME BACK</span><h1>Good to see you.</h1><p>Sign in to continue your journey.</p>
    <?php if($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
    <form method="post">
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <button class="btn btn-primary full">Login →</button>
    </form>
    <div class="demo-box"><b>Demo login</b><br>ananya@example.com / password<br><small>Seed accounts use the password: <strong>password</strong></small></div>
    <p class="form-note">New here? <a href="register.php">Create a profile</a></p>
  </div>
</section>
<?php require "includes/footer.php"; ?>