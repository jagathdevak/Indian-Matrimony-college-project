<?php
require "config.php";
$id=(int)($_GET["id"]??0);
$stmt=$conn->prepare("SELECT * FROM users WHERE id=? AND banned=0"); $stmt->bind_param("i",$id); $stmt->execute(); $p=$stmt->get_result()->fetch_assoc();
if(!$p){header("Location: profiles.php");exit;}
$sent=false;
if(isLoggedIn() && $_SERVER["REQUEST_METHOD"]==="POST" && $_POST["action"]==="interest" && $id!=$_SESSION["user_id"]){
    $x=$conn->prepare("INSERT IGNORE INTO interests(sender_id,receiver_id) VALUES(?,?)");
    $x->bind_param("ii",$_SESSION["user_id"],$id);$x->execute();$sent=true;
}
$pageTitle=$p["name"]; require "includes/header.php";
$age=date("Y")-date("Y",strtotime($p["dob"])); if(date("md")<date("md",strtotime($p["dob"])))$age--;
?>
<section class="profile-detail">
  <div class="detail-photo portrait portrait-<?=($id%4)+1?>"></div>
  <div class="detail-info">
    <span class="eyebrow">PROFILE</span><h1><?=e($p["name"])?> <span class="verified">✓</span></h1>
    <div class="big-meta"><?=$age?> years · <?=e($p["city"])?>, <?=e($p["state"])?></div>
    <p class="detail-bio"><?=e($p["bio"] ?: "Looking for a meaningful and genuine connection.")?></p>
    <div class="detail-grid">
      <div><small>Education</small><b><?=e($p["education"])?></b></div><div><small>Profession</small><b><?=e($p["profession"])?></b></div>
      <div><small>Religion</small><b><?=e($p["religion"])?></b></div><div><small>Location</small><b><?=e($p["city"])?></b></div>
    </div>
    <?php if($sent): ?><div class="success">Interest sent ♥</div><?php elseif(isLoggedIn() && $id!=$_SESSION["user_id"]): ?>
      <form method="post"><input type="hidden" name="action" value="interest"><button class="btn btn-primary">Send interest ♥</button></form>
    <?php elseif(!isLoggedIn()): ?><a class="btn btn-primary" href="login.php">Login to connect →</a><?php endif; ?>
  </div>
</section>
<?php require "includes/footer.php"; ?>