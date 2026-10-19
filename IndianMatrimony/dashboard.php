<?php
require "config.php"; requireLogin();
$me=$_SESSION["user_id"];
$stmt=$conn->prepare("SELECT * FROM users WHERE id=?");$stmt->bind_param("i",$me);$stmt->execute();$user=$stmt->get_result()->fetch_assoc();
$q=$conn->prepare("SELECT u.*, i.status FROM interests i JOIN users u ON u.id=i.receiver_id WHERE i.sender_id=? ORDER BY i.created_at DESC");
$q->bind_param("i",$me);$q->execute();$sent=$q->get_result()->fetch_all(MYSQLI_ASSOC);
$r=$conn->prepare("SELECT u.*, i.status FROM interests i JOIN users u ON u.id=i.sender_id WHERE i.receiver_id=? ORDER BY i.created_at DESC");
$r->bind_param("i",$me);$r->execute();$received=$r->get_result()->fetch_all(MYSQLI_ASSOC);
$pageTitle="My Dashboard"; require "includes/header.php";
?>
<section class="dash-head"><div><span class="eyebrow">YOUR SPACE</span><h1>Hello, <?=e($user["name"])?>. <em>♥</em></h1><p>Your matchmaking journey, all in one place.</p></div><a href="profiles.php" class="btn btn-primary">Discover people →</a></section>
<section class="dash-grid">
  <div class="dash-card profile-summary"><div class="avatar portrait portrait-b">♥</div><h2><?=e($user["name"])?></h2><p><?=e($user["city"])?> · <?=e($user["profession"])?></p><a href="profile.php?id=<?=$me?>" class="text-link">View public profile →</a></div>
  <div class="dash-card"><span class="eyebrow">SENT</span><strong class="dash-number"><?=count($sent)?></strong><p>Interests you've sent</p></div>
  <div class="dash-card"><span class="eyebrow">RECEIVED</span><strong class="dash-number"><?=count($received)?></strong><p>People interested in you</p></div>
</section>
<section class="section compact-section"><div class="section-heading"><div><span class="eyebrow">YOUR ACTIVITY</span><h2>Connections</h2></div></div>
<div class="activity-list">
<?php foreach(array_slice(array_merge($sent,$received),0,8) as $item): ?><div class="activity-item"><div class="tiny-avatar portrait portrait-c"></div><div><b><?=e($item["name"])?></b><p><?=e($item["city"])?> · <?=e($item["profession"])?></p></div><span class="status <?=$item["status"]?>"><?=e(ucfirst($item["status"]))?></span><a href="profile.php?id=<?=$item["id"]?>">View →</a></div><?php endforeach; ?>
<?php if(!$sent&&!$received): ?><div class="empty-state">No connections yet. Start discovering profiles and send an interest.</div><?php endif; ?>
</div></section>
<?php require "includes/footer.php"; ?>