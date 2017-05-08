<?php
include_once '../../../grplib-php/init.php';
include_once '../../lib/htm.php';
printHeader(false); printMenu();
$pagetitle = 'Miiverse has been redesigned!';

print $GLOBALS['div_body_head'];
print '
<header id="header">
  
  <h1 id="page-title">Miiverse has been redesigned!</h1>

</header>


<div class="container" id="miiverse-will-reborn">
  <div class="main-header content">
    <h1>Miiverse has been ruined!</h1>
    <img src="/content/special/header-image.png" class="header-image">
  </div>
  <div class="guide-message content">
    <span class="icon-container official-user"><img src="/content/special/admin-us.png" class="icon test-official-user-img"></span>
    <p class="message-text">Tom from Nintendo here. We want to inform you that we\'ve fucked Miiverse up for no god damn reason other than to annoy all of our users, because fuck you, users. Thank you!</p>
  </div>
  <div class="album-guide content">
    <a class="album-screenshot title-capture-container">
      <img src="/content/special/album-screenshot-us.png" class="test-album-screenshot-img">
    </a>
    <div class="album-guide-conteiner">
      <h2>Your Screenshot Album</h2>
      <p>Okay, this is one semi-good feature of the redesign.</p>
      <p class="note">◆ You can only have up to 100 screenshots tho. <br>
◆ This is private.</p>
    </div>
    
  </div>
  <div class="content diary-guide">
    <img src="/content/special/diary-screenshot-us.png" class="diary-screenshot-image test-diary-screenshot-img">
    <div class="diary-guide-conteiner">
      <h2>Your Play Journal</h2>
      <p>We don\'t know why we implemented this.</p>
      <p class="note">◆ We just wanted to pretend that we have an alternative to Activity Feed, even though we don\'t.. we really don\'t.</p>
    </div>
    
  </div>
  <div class="content community-guide">
    <img src="/content/special/tutorial-community.png" class="community-illust">
    <h2 class="community-guide-title">A New Look for Communities</h2>
    <a class="community-screenshot title-capture-container">
      <img src="/content/special/community-screenshot-us.png" class="test-community-screenshot-img">
    </a>
    <div class="community-guide-conteiner">
      <p>We divided them into 3 sections for some reason.</p>
      <h3 class="diary"><img src="/content/special/com-diary-image.png">Play Journal Entries</h3>
      <p>We don\'t know what freedom is.</p>
      <h3 class="artwork"><img src="/content/special/com-artwork-image.png">Drawings</h3>
      <p>Because fuck you.</p>
      <p class="note">◆ You can\'t make handwritten drawings anywhere else, lolololololololol.</p>
      <h3 class="topic"><img src="/content/special/com-topic-image.png">Discussions</h3>
      <p>If you want to ask the meaning of life, here you go.</p>
    </div>
    
  </div>
  <div class="content other-guide">
    <h2 class="other">Other Changes</h2>
    <ul>
      <li>Did we mention the Activity Feed was gone?</li>
      <li>Oh yeah, and there\'s a post limit. If you post anything over 30 posts or comments, you won\'t be happy.</li>
    </ul>
  </div>

</div>

	';
	print $GLOBALS['div_body_head_end'];
    printFooter();