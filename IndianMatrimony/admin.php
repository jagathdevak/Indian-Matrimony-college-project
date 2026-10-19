<?php
require "config.php";
requireAdmin();

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $action=$_POST["action"]??"";
    $userId=(int)($_POST["user_id"]??0);

    // Banning a member permanently removes the account from the database.
    // Permanent ban: preserve the email in banned_users, then remove the
    // account. Related interests are removed by ON DELETE CASCADE.
    if($action==="ban_user" && $userId>0){
        $conn->begin_transaction();
        try {
            $find=$conn->prepare("SELECT email FROM users WHERE id=? LIMIT 1");
            $find->bind_param("i",$userId);
            $find->execute();
            $member=$find->get_result()->fetch_assoc();

            if(!$member){
                throw new Exception("Member not found.");
            }

            $ban=$conn->prepare("INSERT IGNORE INTO banned_users (email, reason) VALUES (?, ?)");
            $reason="Permanently banned by administrator";
            $ban->bind_param("ss",$member["email"],$reason);
            $ban->execute();

            $stmt=$conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i",$userId);
            $stmt->execute();

            $conn->commit();
            header("Location: admin.php?banned=1");
            exit;
        } catch (Throwable $e) {
            $conn->rollback();
            header("Location: admin.php?ban_error=1");
            exit;
        }
    }

    if($action==="delete_user" && $userId>0){
        $stmt=$conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i",$userId);
        $stmt->execute();
        header("Location: admin.php?deleted=1");
        exit;
    }
}

$notice="";
if(isset($_GET["banned"])) $notice="Member banned permanently. The account was removed and its email is blocked from future registration.";
elseif(isset($_GET["ban_error"])) $notice="Could not complete the permanent ban.";
elseif(isset($_GET["deleted"])) $notice="Member deleted permanently.";

$totalUsers=(int)$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()["c"];
$bannedUsers=(int)$conn->query("SELECT COUNT(*) c FROM users WHERE banned=1")->fetch_assoc()["c"];
$activeUsers=$totalUsers-$bannedUsers;
$totalInterests=(int)$conn->query("SELECT COUNT(*) c FROM interests")->fetch_assoc()["c"];
$users=$conn->query("SELECT id,name,email,gender,city,profession,created_at,banned FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle="Admin Dashboard";
require "includes/header.php";
?>
<section class="dash-head">
  <div>
    <span class="eyebrow">ADMIN COMMAND CENTER</span>
    <h1>Welcome, <em><?=e($_SESSION["admin_name"])?></em>.</h1>
    <p>Manage members, review accounts and control access.</p>
  </div>
  <a href="admin_logout.php" class="btn btn-ghost">Logout</a>
</section>

<section class="dash-grid">
  <div class="dash-card"><span class="eyebrow">TOTAL MEMBERS</span><strong class="dash-number"><?=$totalUsers?></strong><p>Registered profiles</p></div>
  <div class="dash-card"><span class="eyebrow">ACTIVE</span><strong class="dash-number"><?=$activeUsers?></strong><p>Accounts with access</p></div>
  <div class="dash-card"><span class="eyebrow">BANNED</span><strong class="dash-number"><?=$bannedUsers?></strong><p>Suspended accounts</p></div>
  <div class="dash-card"><span class="eyebrow">INTERESTS</span><strong class="dash-number"><?=$totalInterests?></strong><p>Total connections started</p></div>
</section>

<?php if($notice): ?><div class="success" style="margin-bottom:20px"><?=e($notice)?></div><?php endif; ?>

<section class="section compact-section">
  <div class="section-heading">
    <div><span class="eyebrow">MEMBER MANAGEMENT</span><h2>All registered users</h2></div>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Member</th><th>Location</th><th>Profession</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach($users as $member): ?>
        <tr>
          <td><b><?=e($member["name"])?></b><small><?=e($member["email"])?></small></td>
          <td><?=e($member["city"])?></td>
          <td><?=e($member["profession"])?></td>
          <td><span class="status accepted">Active</span></td>
          <td><?=date("d M Y",strtotime($member["created_at"]))?></td>
          <td class="admin-actions">
            <form method="post" onsubmit="return confirm('Ban this member? This permanently removes the account and its interests from the database.')">
              <input type="hidden" name="action" value="ban_user">
              <input type="hidden" name="user_id" value="<?=$member["id"]?>">
              <button class="admin-btn ban" type="submit">Ban &amp; Remove</button>
            </form>
            <form method="post" onsubmit="return confirm('Delete this member permanently?')">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="user_id" value="<?=$member["id"]?>">
              <button class="admin-btn delete" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$users): ?><tr><td colspan="6" class="empty-state">No members registered yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require "includes/footer.php"; ?>