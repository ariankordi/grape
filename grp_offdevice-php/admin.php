<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SESSION['pid'])) {
    header("Location: /act/login");
    exit();
}
$me = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'"')->fetch_assoc();

if(!$me){
    noLogin();
    exit();
}

if($me["official_user"] !== "1"){
    require "404.php";
    exit();
}

/*
$unban = $mysql->query('SELECT * FROM bans WHERE `comment` = "inactivity"');
while($row = $unban->fetch_assoc()){
    $stmt = $mysql->prepare("UPDATE `people` SET `status` = 0 AND `las` = 0 WHERE `pid` = ?");
    $stmt->bind_param("i", $row["reciever"]);
    $stmt->execute();
    $stmt = $mysql->prepare("DELETE FROM bans WHERE `bans`.`operation_id` = ?");
    $stmt->bind_param("i", $row["operation_id"]);
    $stmt->execute();
}
*/

if(isset($_GET["inactivity"])){
    $me = $mysql->query("SELECT pid, user_id, screen_name, mii_hash, nnas_info, face, email, official_user, organization, platform_id, privilege, image_perm, status, ban_status, la, las, lai FROM `people`;");
    while($mer = $me->fetch_assoc()){
        echo $mer["screen_name"]."<br>";
        if($mer["lai"] == 0){
            $date = new DateTime($mer['la']);
            $now = new DateTime();
            $interval = $now->diff($date);
            $daysDifference = $interval->format('%a');
            echo $mer['la']."<br>";
            echo $daysDifference."<br>";
            if($daysDifference > 28 && $mer["las"] == 0) {
                $stmt = $mysql->prepare("UPDATE `people` SET `las` = 1 WHERE `pid` = ?");
                $stmt->bind_param("i", $mer["pid"]);
                $stmt->execute();
                pleasebanme($mer["pid"], "Banned for inactivity");
                echo "Not OK<br>";
                //sendHook($mer["screen_name"]." was not actually banned by plsbanme, however, the activity limit has been exceeded (45 days)");
            } else {
                echo "OK<br>";
            }
        } else {
            echo "Immune<br>";
        }
    }
}
if(isset($_GET["giveimmune"])){
    $stmt = $mysql->prepare("UPDATE `people` SET `lai` = 1 WHERE `user_id` = '".$mysql->real_escape_string($_GET["giveimmune"])."'");
    $stmt->execute();
    if($stmt->error){
        exit("failed");
    } else {
        echo "gave immunity<br>";
    }
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty($_POST["action"])){
        goto skip;
    }
    /*
    if(empty($_POST["csrf"]) || $_POST["csrf"] !== $_COOKIE["grp_identity"]){
        echo "The CSRF check failed.<br>";
        goto skip;
    }
    */
    if($_POST["action"] == "ban"){
        $_POST["value"] = $_POST["value1"];
        if(empty($_POST["value"])){
            echo "Please fill in all fields.<br>";
            goto skip;
        }
        $stmt = $mysql->prepare('SELECT * FROM people WHERE user_id = ?');
        $stmt->bind_param("s", $_POST["value"]);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows == 0){
            echo "This user doesn't exist.<br>";
            goto skip;
        }
        $user = $res->fetch_assoc();
        if(!empty($user["official_user"]) || $user["official_user"] == "0" && $_SESSION["pid"] !== 1741588700){
            echo "You cannot ban this user.<br>";
            goto skip;
        }
        if(new DateTime() > new DateTime($_POST["time"])){
            echo "Please pick a time in the future.<br>";
            goto skip;
        }
        $mee = $mysql->query('SELECT * FROM bans WHERE reciever = "'.$mysql->real_escape_string($user["pid"]).'" LIMIT 1')->num_rows;
        if($mee !== 0){
            echo "This user is already banned.<br>";
            goto skip;
        }
        $mee = $mysql->query("INSERT INTO `bans` (`operator`, `reciever`, `operation_id`, `operation`, `created_at`, `expires_at`, `finished`) VALUES ('".$_SESSION["pid"]."', '".$user["pid"]."', NULL, '1', current_timestamp(), '".$mysql->real_escape_string(strval($_POST["time"]))."', '0');");
        if(!$mee){
            echo "Failed to ban user. :(<br>";
            goto skip;
        }
        $mee = $mysql->query("UPDATE `people` SET `status` = 2 WHERE `pid` = '".$user["pid"]."'");
        if(!$mee){
            echo "Failed to ban user. :(<br>";
            goto skip;
        }
        echo "Done!<br>";
        goto skip;
    }
    if($_POST["action"] == "post"){
        $_POST["value"] = $_POST["value2"];
        if(empty($_POST["value"])){
            echo "Please fill in all fields.<br>";
            goto skip;
        }
        $mee = $mysql->query('SELECT * FROM posts WHERE id = "'.$mysql->real_escape_string($_POST["value"]).'" AND is_hidden = 0 LIMIT 1');
        if($mee->num_rows == 0){
            echo "This post does not exist.<br>";
            goto skip;
        }
        $row = $mee->fetch_assoc();
        $stmt = $mysql->prepare('SELECT * FROM people WHERE pid = ?');
        $stmt->bind_param("i", $row["pid"]);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res == 0){
            echo "Failed to get the post's owner.<br>Continuing anyway...<br>";
        } else {
            $user = $res->fetch_assoc();
        }
        if(!empty($user["official_user"]) || $user["official_user"] == "0"){
            if($_SESSION["pid"] !== $row["pid"]){
                if($_SESSION["pid"] !== 1741588700){
                    echo "You cannot delete this user's posts.<br>";
                    goto skip;
                }
            }
        }
        $stmt = $mysql->prepare("UPDATE `posts` SET `is_hidden` = '1' WHERE `id` = ?");
        if(!$stmt){
            echo "Failed to prepare statement :(<br>";
            goto skip;
        }
        $stmt->bind_param("s", $_POST["value"]);
        $stmt->execute();
        if($stmt->error){
            echo "Failed to delete post. :(<br>";
            goto skip;
        }
        $stmt = $mysql->prepare("UPDATE `posts` SET `hidden_resp` = 0 WHERE `id` = ?");
        if(!$stmt){
            echo "Failed to prepare statement :(<br>";
            goto skip;
        }
        $stmt->bind_param("s", $_POST["value"]);
        $stmt->execute();
        if($stmt->error){
            echo "Failed to delete post. :(<br>";
            goto skip;
        }
        echo "Done!<br>";
        goto skip;
    }
    if($_POST["action"] == "reply"){
        $_POST["value"] = $_POST["value3"];
        if(empty($_POST["value"])){
            echo "Please fill in all fields.<br>";
            goto skip;
        }
        $mee = $mysql->query('SELECT * FROM replies WHERE id = "'.$mysql->real_escape_string($_POST["value"]).'" AND is_hidden = 0 LIMIT 1');
        if($mee->num_rows == 0){
            echo "This reply does not exist.<br>";
            goto skip;
        }
        $row = $mee->fetch_assoc();
        $stmt = $mysql->prepare('SELECT * FROM people WHERE pid = ?');
        $stmt->bind_param("i", $row["pid"]);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res == 0){
            echo "Failed to get the post's owner.<br>Continuing anyway...<br>";
        } else {
            $user = $res->fetch_assoc();
        }
        if(!empty($user["official_user"]) || $user["official_user"] == "0"){
            if($_SESSION["pid"] !== $row["pid"]){
                if($_SESSION["pid"] !== 1741588700){
                    echo "You cannot delete this user's posts.<br>";
                    goto skip;
                }
            }
        }
        $stmt = $mysql->prepare("UPDATE `replies` SET `is_hidden` = '1' WHERE `id` = ?");
        if(!$stmt){
            echo "Failed to prepare statement :(<br>";
            goto skip;
        }
        $stmt->bind_param("s", $_POST["value"]);
        $stmt->execute();
        if($stmt->error){
            echo "Failed to delete post. :(<br>";
            goto skip;
        }
        $stmt = $mysql->prepare("UPDATE `replies` SET `hidden_resp` = 0 WHERE `id` = ?");
        if(!$stmt){
            echo "Failed to prepare statement :(<br>";
            goto skip;
        }
        $stmt->bind_param("s", $_POST["value"]);
        $stmt->execute();
        if($stmt->error){
            echo "Failed to delete post. :(<br>";
            goto skip;
        }
        echo "Done!<br>";
        goto skip;
    }
}
$info = getdate();
$date = $info['mday'];
$month = $info['mon'];
$year = $info['year'];
$hour = $info['hours'];
$min = $info['minutes'];
$sec = $info['seconds'];

$current_date = "$date/$month/$year == $hour:$min:$sec";
skip:
?>
<body style="background-color: grey;">
<p>Yes, I know this admin panel kinda sucks. But it's not that bad. (CSS SOON!!!!!!! SHUT UP!!!!!!)</p>
<a href="/admin/reports">View reports (not implemented)</a>
<br><br>
<form id="form" method="POST">
<label for="action">Choose an action:</label>
<br>
<select id="action" name="action">
  <option value="">Select an option</option>
  <option value="ban">Ban User</option>
  <option value="post">Delete Post</option>
  <option value="reply">Delete Reply</option>
</select>
<br><br>
<div id="ban" style="display: none;">
Current time of the server when the page was rendered: <?php echo $current_date; ?>
<p>Until when?: </p><input type="datetime-local" name="time"><br>
<input name="value1" placeholder="Username"></input>
<button type="submit">Submit</button>
</div>
<div id="post" style="display: none;">
<input name="value2" placeholder="Post ID (Can be found in URL)"></input>
<button type="submit">Submit</button>
</div>
<div id="reply" style="display: none;">
<input name="value3" placeholder="Reply ID (Can be found in URL)"></input>
<button type="submit">Submit</button>
</div>
</form>
<script>
    let form = document.getElementById('form');

    // Listen for input events on the form
    form.addEventListener('input', function (event) {
        let action = document.getElementById("action");
        let banform = document.getElementById("ban");
        let postform = document.getElementById("post");
        let replyform = document.getElementById("reply");
        if(action.value == "ban"){
            banform.style = "";
            postform.style = "display: none;";
            replyform.style = "display: none;"
        } else if(action.value == "post"){
            banform.style = "display: none;";
            postform.style = "";
            replyform.style = "display: none;"
        } else if(action.value == "reply"){
            banform.style = "display: none;";
            postform.style = "display: none;";
            replyform.style = ""
        }
    });
</script>
</body>