<?php
require "config.php";
$q=trim($_GET["q"]??""); $gender=$_GET["gender"]??""; $city=trim($_GET["city"]??"");
$sql="SELECT * FROM users WHERE banned=0"; $types=""; $params=[];
if(isLoggedIn()) { $sql.=" AND id != ?"; $types.="i"; $params[]=$_SESSION["user_id"]; }
if($q){$sql.=" AND (name LIKE ? OR profession LIKE ? OR education LIKE ? OR state LIKE ?)";$types.="ssss";$x="%$q%";array_push($params,$x,$x,$x,$x);}
if($gender){$sql.=" AND gender=?";$types.="s";$params[]=$gender;}
if($city){$sql.=" AND city LIKE ?";$types.="s";$params[]="%$city%";}
$sql.=" ORDER BY created_at DESC";
$stmt=$conn->prepare($sql); if($types) $stmt->bind_param($types,...$params); $stmt->execute(); $rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$pageTitle="Discover Profiles"; require "includes/header.php";
?>
<section class="discover-head"><span class="eyebrow">DISCOVER</span><h1>Meet people with <em>potential.</em></h1><p>Search by city, profession, education or anything that feels important to you.</p></section>
<form class="search-bar" method="get">
  <input name="q" placeholder="Search name, profession, education..." value="<?=e($q)?>">
  <select name="gender"><option value="">Any gender</option><option <?= $gender==="Male"?"selected":""?>>Male</option><option <?= $gender==="Female"?"selected":""?>>Female</option><option <?= $gender==="Other"?"selected":""?>>Other</option></select>
  <input name="city" placeholder="City" value="<?=e($city)?>">
  <button class="btn btn-primary">Search</button>
</form>
<section class="section"><div class="profile-grid profile-grid-wide">
<?php if(!$rows): ?><div class="empty-state">No profiles found. Try a broader search.</div><?php endif; ?>
<?php foreach($rows as $i=>$p): ?>
<article class="profile-card big">
  <div class="profile-img portrait <?=['portrait-b','portrait-c','portrait-d','portrait-e'][$i%4]?>"><span class="online-dot">●</span></div>
  <div class="profile-body"><div class="profile-name"><?=e($p["name"])?>, <?=date("Y")-date("Y",strtotime($p["dob"]))?> <span>✓</span></div>
  <p><?=e($p["city"])?>, <?=e($p["state"])?> · <?=e($p["profession"])?></p>
  <div class="chips"><i><?=e($p["education"])?></i><i><?=e($p["religion"])?></i></div>
  <p class="bio-preview"><?=e($p["bio"] ?: "Looking for a meaningful and genuine connection.")?></p>
  <a class="btn btn-outline small" href="profile.php?id=<?=$p["id"]?>">View profile</a></div>
</article>
<?php endforeach; ?>
</div></section>
<?php require "includes/footer.php"; ?>