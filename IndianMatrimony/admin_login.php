<?php
require "config.php";
if (isAdminLoggedIn()) { header("Location: admin.php"); exit; }
$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $email=trim($_POST["email"]??"");
    $password=$_POST["password"]??"";
    $stmt=$conn->prepare("SELECT id,name,password FROM admins WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $admin=$stmt->get_result()->fetch_assoc();
    if($admin && password_verify($password,$admin["password"])){
        $_SESSION["admin_id"]=$admin["id"];
        $_SESSION["admin_name"]=$admin["name"];
        header("Location: admin.php");
        exit;
    }
    $error="Admin email or password is incorrect.";
}
$pageTitle="Admin Login";
require "includes/header.php";
?>
<section class="auth-page">
  <div class="auth-card">
    <div class="auth-icon">◆</div>
    <span class="eyebrow">ADMIN ACCESS</span>
    <h1>Command center.</h1>
    <p>Manage members and keep the Vivah community safe.</p>
    <?php if($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
    <form method="post">
      <label>Email<input type="email" name="email"  placeholder="Enter Admin email"required></label>
      <label>Password<input type="password" name="password" placeholder="Enter password" required></label>
      <button class="btn btn-primary full">Enter dashboard →</button>
    </form>
    <div class="demo-box"><b>Demo administrator</b><br>admin@vivah.local<br>password</div>
    <p class="form-note"><a href="index.php">← Back to Vivah</a></p>
  </div>
</section>
<?php require "includes/footer.php"; ?>