<?php 
$activate = true;
require_once '../../grplib-php/init.php';
$pagetitle = 'Begin Setup';
require_once '../lib/htm.php';
printHeader(false);
?>
    <div id="body">
<div id="welcome-start" class="slide-page" data-slide-number="1">
<div class="window-page">
  <div class="window welcome-window">
    <h1 class="window-title">Welcome to Miiverse!</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p>Miiverse is a gaming community that connects people from all over the world using Mii characters.<br>
Use Miiverse to share your gaming experiences and meet people from around the world.</p>
    </div></div>
  </div>
</div>
<a href="#" class="fixed-bottom-button left exit-button welcome-exit-button" data-sound="SE_WAVE_EXIT">Close</a>
<a href="#" class="fixed-bottom-button right next-button slide-button" data-slide="#welcome-parental_control">Next</a>
</div>
<div id="welcome-parental_control" class="slide-page none" data-slide-number="3">
<div class="window-page">
  <div class="window welcome-window">
    <h1 class="window-title">Parental Controls</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p>By using the console's Parental Controls feature, parents and guardians can restrict the extent to which children can use Miiverse.</p>
    </div></div>
  </div>
</div>
<a href="#" class="fixed-bottom-button left back-button slide-button accesskey-B" data-slide="#welcome-start" data-sound="SE_WAVE_BACK">Back</a>
<a href="#" class="fixed-bottom-button right next-button slide-button" data-slide="#welcome-about">Next</a>
</div>
<div id="welcome-about" class="slide-page none" data-slide-number="4">
<div class="window-page">
  <div class="window welcome-window">
    <h1 class="window-title">What Is Miiverse?</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p class="p1">Miiverse is a user-driven gaming community where you can interact with people all over the world.</p>
      <p>You can write or draw posts in game communities or send messages directly to your friends.</p>
    </div></div>
  </div>
</div>
<a href="#" class="fixed-bottom-button left back-button slide-button accesskey-B" data-slide="#welcome-parental_control" data-sound="SE_WAVE_BACK">Back</a>
<a href="#" class="fixed-bottom-button right next-button slide-button" data-slide="#welcome-guideline">Next</a>
</div>
<div id="welcome-guideline" class="slide-page none" data-slide-number="5">
<div class="window-page">
  <div class="guideline-container">
    <h1 class="window-title">Miiverse Manners</h1>
    <p>The following are some important guidelines for making Miiverse a fun and enjoyable experience for everyone.<br>
<br>
The Miiverse Code of Conduct contains detailed information, so please read it carefully.</p>
    <div class="guideline1">
      <h2>Posts Can Be Viewed Around the World</h2>
      <p>Miiverse contains many gaming communities where people from all over the world can share their thoughts.</p>
      <p>When you post in a community, remember that everyone can see it, so please express yourself in a way that everyone can enjoy. Use common sense, and think before you post. Miiverse is a service that is also accessible from the Internet, so bear in mind that people who don't use Miiverse may also see your posts.</p>
      <p>Additionally, any comments you make on your friends’ posts will be seen not by just your friends but by people around the world, too. Please keep this in mind.</p>
    </div>
    <div class="guideline2">
      <h2>Be Nice to One Another</h2>
      <p>In order to keep Miiverse a fun place for everyone, we ask that you be considerate to other users.</p>
      <p>Help us keep Miiverse an enjoyable experience by not posting anything inappropriate or offensive.</p>
      <p></p>
    </div>
    <div class="guideline3">
      <h2>Do Not Post Personal Information—Yours or Others’</h2>
      <p>Remember, knowing someone in Miiverse isn’t the same as knowing them in real life.</p>
      <p>Never share your e-mail address, home address, work or school name, or other personally identifying information with anyone on Miiverse, and never share anyone else's information either.</p>
      <p>Additionally, if someone you meet in Miiverse invites you to meet him or her in the real world, do not accept. Miiverse is an online community and should not be used to arrange real-world meet-ups.</p>
    </div>
    <div class="guideline4">
      <h2>Don’t Post Spoilers</h2>
      <p>Some people come to Miiverse looking for tips and tricks for games, but others want to discover a game's secrets all on their own.</p>
      <p>Posts that reveal secrets of a game or its story are called "spoilers."</p>
      <p>If you're posting something about a game that might be a spoiler, be sure to check the Spoilers box before sending your post.</p>
      <p>This way, people who don't want to be spoiled won't see your post.</p>
    </div>
    <div class="guideline5">
      <h2>Code of Conduct Violations</h2>
      <p>Our goal is to keep Miiverse fun and enjoyable for everyone.</p>
      <p>In the event that someone violates the Miiverse Code of Conduct, we will take appropriate action, up to and including blocking the offending user or console.</p>
    </div>
    <div class="guideline6">
      <h2>Have You Played That Game?</h2>
      <p>If you're posting in the community for a game you've played, your posts will have an icon indicating that you've played it.</p>
    </div>
    <p class="guideline7"><a href="#" class="guideline-button slide-button" data-slide="#welcome-guideline-body" data-save-scroll="1">Read Miiverse Code of Conduct</a></p>
    <a href="#" class="fixed-bottom-button left back-button slide-button accesskey-B" data-slide="#welcome-about" data-sound="SE_WAVE_BACK">Back</a>
    <a href="#" class="next-button slide-button" data-slide="#welcome-luminous_opt_in">I understand the Miiverse Code of Conduct</a>
  </div>
</div>
</div>

<div id="welcome-guideline-body" class="slide-page none" data-body-id="help" data-slide-number="6"><div class="help-left-button">
  <a href="#" class="guide-exit-button exit-button index" data-sound="SE_WAVE_BACK">Close the Miiverse Code of Conduct</a>
</div>
<h2 id="sub-header" class="guide-sub-header">Miiverse Code of Conduct &amp; FAQs</h2>
<div id="guide" class="help-content">
  <div class="num1">
    <h2>Miiverse Manners</h2>
    <p>The following are some important guidelines for making Miiverse a fun and enjoyable experience for everyone.<br>
<br>
The Miiverse Code of Conduct contains detailed information, so please read it carefully.</p>

    <h3>Posts Can Be Viewed Around the World</h3>
    <p>Miiverse contains many gaming communities where people from all over the world can share their thoughts.</p>
    <p>When you post in a community, remember that everyone can see it, so please express yourself in a way that everyone can enjoy. Use common sense, and think before you post. Miiverse is a service that is also accessible from the Internet, so bear in mind that people who don't use Miiverse may also see your posts.</p>
    <p>Additionally, any comments you make on your friends’ posts will be seen not by just your friends but by people around the world, too. Please keep this in mind.</p>
    <p class="guide-img1"><img src="/img/welcome/welcome5-1.png" width="552" height="388"></p>

    <h3>Be Nice to One Another</h3>
    <p>In order to keep Miiverse a fun place for everyone, we ask that you be considerate to other users.</p>
    <p>Help us keep Miiverse an enjoyable experience by not posting anything inappropriate or offensive.</p>
    
    <p class="guide-img2"><img src="/img/welcome/welcome5-4.png" width="616" height="399"></p>

    <h3>Do Not Post Personal Information—Yours or Others’</h3>
    <p>Remember, knowing someone in Miiverse isn’t the same as knowing them in real life.</p>
    <p>Never share your e-mail address, home address, work or school name, or other personally identifying information with anyone on Miiverse, and never share anyone else's information either.</p>
    <p>Additionally, if someone you meet in Miiverse invites you to meet him or her in the real world, do not accept. Miiverse is an online community and should not be used to arrange real-world meet-ups.</p>
    <p class="guide-img3"><img src="/img/welcome/welcome5-2.png" width="612" height="159"></p>

    <h3>Don’t Post Spoilers</h3>
    <p>Some people come to Miiverse looking for tips and tricks for games, but others want to discover a game's secrets all on their own.</p>
    <p>Posts that reveal secrets of a game or its story are called "spoilers."</p>
    <p>If you're posting something about a game that might be a spoiler, be sure to check the Spoilers box before sending your post.</p>
    <p>This way, people who don't want to be spoiled won't see your post.</p>
    <p class="guide-img4"><img src="/img/welcome/welcome5-3.png" width="767" height="394"></p>

    <h3>Users Aged 12 and Under</h3>
    <p>For the protection of younger users, direct friend requests are not possible in Miiverse for users aged 12 and under. At the same time, younger users can make friends on Wii U outside Miiverse by entering each other's Nintendo Network IDs in the friend list on the HOME Menu.</p>
    <p>We encourage younger users to make friends on Wii U only if they are friends in real life (such as friends from the same school or neighborhood). Therefore, do not attempt to exchange your Nintendo Network ID with other users on Miiverse.</p><p>Additionally, do not attempt to exchange Nintendo 3DS or other friend codes.</p>
    <p class="guide-img7"><img src="/img/welcome/welcome5-7.png" width="187" height="166"></p>

    <h3>Code of Conduct Violations</h3>
    <p>Our goal is to keep Miiverse fun and enjoyable for everyone.</p>
    <p>In the event that someone violates the Miiverse Code of Conduct, we will take appropriate action, up to and including blocking the offending user or console.</p>
    <p class="guide-img5"><img src="/img/welcome/welcome5-6.png" width="210" height="210"></p>
  </div>

  <div class="num2">
    <h2>A Few Reminders</h2>
    <p>Please keep the following considerations in mind before posting on Miiverse.</p>

    <h3>By Gamers, About Games</h3>
    <p>Miiverse is a gaming community that allows people from around the world to interact and discuss the games they love.</p>
    <p>In communities dedicated to a specific game, please stay on topic, and only make posts relevant to that community and that contribute to the overall discussion. When someone posts on an unrelated topic, it may detract from users who want to discuss the game.</p>
    <p>In addition, please refrain from making posts that other users may find irritating, such as those asking for friend requests or asking people to Yeah your posts.</p>

    <h3>Respect Other People’s Work</h3>
    <p>You can post handwritten illustrations in Miiverse.</p>
    <p>Because you can make handwritten posts, you can also make sketches. Please post only your original works or works for which you have been given permission to use.</p>
    <p>As long as you follow the Miiverse Code of Conduct, you can use Nintendo’s copyrighted works within Miiverse.</p>

    <h3>Do Not Let Others Use Your Nintendo Network ID</h3>
    <p>Now that Miiverse can be accessed online from many different devices, you may be asked by others to let them use your Nintendo Network ID.</p>
    <p>However, letting others use your Nintendo Network ID is in violation of the Nintendo Network Agreement and may lead to suspension of your ID.</p>

    <h3>Violation Types</h3>
    <p>Additionally, the following content is prohibited from being posted or included in messages by the Miiverse Code of Conduct.</p>
    <ul>
      <li>Personal Information
          <p>Personal information includes but is not limited to your e-mail address, home address, work or school name, and phone number. Never use Miiverse as a means of setting up real-world meet-ups. Never write or ask in public for account information or IDs for other services, or any information that would allow people to be contacted directly.</p>
      </li>
      <li>Violent Content
        <p>This kind of content includes anything that depicts violence, promotes suicide, or endorses acts of cruelty or violence.</p>
      </li>
      <li>Inappropriate/Harmful
        <p>Inappropriate or harmful content includes anything that promotes dangerous behavior or illegal activities.</p>
      </li>
      <li>Hateful/Bullying
        <p>This includes any content that slanders, defames, or misrepresents another person, as well as any discriminatory, harassing, or abusive content.</p>
      </li>
      <li>Advertising
        <p>This includes any posts containing commercial or marketing content (including advertisements; however, messages that have received permission from Nintendo and cases where an authorized URL can be attached to a post as a feature of an application are exempt), as well as any attempts to sway public opinion for the purpose of financial gain.</p>
      </li>
      <li>Sexually Explicit
        <p>Sexually explicit content includes anything containing nudity, sexuality, or propositions.</p>
      </li>
      <li>Inappropriate Reporting
        <p>Intentionally misreporting a post for violating the Miiverse Code of Conduct is itself a violation of the Miiverse Code of Conduct.</p>
      </li>
      <li>Other
        <p>Additional kinds of violation include intentionally posting the following:</p>
        <ul>
          <li>Content that infringes on the copyrights, intellectual-property rights, usage of likeness, or privacy rights of any third party</li>
          <li>Religious or political content</li>
          <li>Content that disrupts the community (multi-posts, completely empty/black posts, meaningless scribbles, etc.)</li>
          <li>Content related to the borrowing, lending, or transferal of goods or money.</li>
          <li>Content soliciting donations or participation in fundraisers or demonstrations</li>
          <li>Soliciting people to enter their Nintendo Network IDs on friend lists, publishing Nintendo 3DS or other friend codes in communities, soliciting people to make public their friend codes, using other methods besides friend requests to try and establish relationships in communities. (This does not include making public the community code of a community you created.)</li>
          <li>Mentions of inappropriate or vulgar bodily functions that may make others uncomfortable.</li>
          <li>Impersonating other people, such as verified users, celebrities, or other Miiverse users.</li>
          <li>Any conduct that violated the Nintendo Network Services Agreement or any agreements pertaining to other games or services.</li>
        </ul>
      </li>
    </ul>

    <h3>Consequences of Inappropriate Behavior</h3>
    <p>Users who violate the Miiverse Code of Conduct may find their posts deleted and their access to Miiverse blocked. This can also impact all Nintendo Network IDs using the same Wii U consoles and Nintendo 3DS systems.</p>
    <p>If you see content that presents a credible risk to your or another's safety, please contact law enforcement. If contacted by law enforcement, Nintendo will work with them directly on their investigation.</p>

    <h3>Reuse of Posted Content</h3>
    <p>Your posts and comments may appear within games and may be modified to appear in those games.</p>
    <p>For example, only a portion of a post might be used, the poster’s name might be obscured, or a specific word might be left out.</p>
    <p>This is because posts sent to Miiverse will be used to make games more fun and will be in keeping with the game’s world feel or design. Thank you for your understanding.</p>

    <h3>The Use of Messages and Content on Miiverse</h3>
    <p>Nintendo may use your publicly available posts, comments, drawings, and other contributions to Miiverse as set forth in the Nintendo Network Services Agreement.</p>

    <h3>Disclaimers and User Responsibilities</h3>
    <p>Each Miiverse user is responsible for understanding and complying with the Miiverse Code of Conduct.<br>
<br>
We may update this at any time, so please refer to the latest version to stay informed of any updates.</p>
  </div>

  <div class="num3">
    <h2>Frequently Asked Questions (FAQ)</h2>
    <p>If you have any questions while using Miiverse, this is the first place to check for an answer.</p>

    <h3>I saw a post or comment that violates the Miiverse Code of Conduct. What should I do?</h3>
    <p>If you encounter a post or comment that violates the Miiverse Code of Conduct, please report the violation using the Miiverse reporting function.</p>
    <p>Reported violations will be reviewed by a Nintendo moderator.  Any post that violates the Miiverse Code of Conduct will be removed.</p>
    <p>Please note: false reporting is a violation of the Miiverse Code of Conduct and is subject to action up to and including loss of Miiverse access.</p>

    <h3>I received a comment or message that is offensive, threatening, or upsetting.</h3>
    <p>Harassment of any kind, including slander, defamation of another user, disclosure of personal information, or invasion of privacy, is a violation of the Miiverse Code of Conduct. Even in messages between friends, such exchanges violate the Miiverse Code of Conduct.</p>
    <p>Please report any posts or messages that violate the Miiverse Code of Conduct. If you are experiencing harassment, please use the Report function on the offending user’s profile screen.</p>
    <p>Furthermore, if you do not want to receive comments or friend requests from a particular user, you may use the Block function on the user’s profile screen.</p>

    <h3>I saw a comment with a spoiler in it, even though it wasn’t marked as having spoilers.</h3>
    <p>It’s up to users to mark their posts as having spoilers. However, if someone forgets to do this, you can help by reporting spoilers.</p>
    <p>Comments and posts that receive enough spoiler reports will be marked as spoilers, viewable only to people who have chosen to keep spoilers visible.</p>

    <h3>Why do some users have a green check mark? What are verified users? Can I be a verified user?</h3>
    <div class="guide-img6">
      <p>A verified user is someone whose identity has been authenticated and certified by Nintendo. Their verified status indicates that they are a legitimate source of high-quality information. Their status as a verified user is indicated on their Mii character as shown here.</p>
      <p>Verified-user status is granted only to parties when necessary as determined by Nintendo. We do not accept requests for verification from the general public.</p>
    </div>

    <h3>I can’t see comments for other regions.</h3>
    <p>Your Miiverse settings may not permit you to see posts made in other languages.</p>
    <p>In Miiverse Settings, find the option that reads “View community posts from users who are using which system language?” to control which languages you see in Miiverse communities.<br>
<br>
You may choose to view all languages (default) or only posts made by users with the same system-language setting as you.</p>

    <h3>How do I delete a Miiverse post?</h3>
    <p>If you post something you later do not want other users to see, you can delete it. Here is how:</p>
    <ol>
      <li>Select the post you wish to delete from within Miiverse. If you need assistance locating the post:
        <p>From within Miiverse, select your user icon, and then select Posts on the User Page.</p>
      </li>
      <li>Scroll through the list of posts.</li>
      <li>Select the wrench icon located in the lower-right corner of the post you want to delete.</li>
      <li>Select the drop-down menu, and select Delete.</li>
      <li>Select Submit.</li>
    </ol>
    <p>Please note that deleting a post does not ensure complete or comprehensive removal of the content or information posted by you (for example, if another user has copied your post or if you or another user previously shared the post on another website).</p>

    <h3>I found content that infringes on my copyrighted work, and I would like it removed.</h3>
    <p>Nintendo respects the intellectual property of others, and we ask users of Nintendo products and services to do the same.&nbsp;If you feel that your intellectual-property rights have been infringed, please visit www.nintendo.com/ippolicy to read our full policy and submit a takedown request.</p>
    

    
  </div>
</div>

</div>
<div id="welcome-luminous_opt_in" class="slide-page none" data-slide-number="11">
<div class="window-page">
  <div class="window welcome-window with-button">
    <h1 class="window-title">Configuring Notification Alerts</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p>Do you want to be alerted via the Miiverse icon when you receive notifications?<br>
This setting can be changed later.</p>
    </div></div>
    <div class="window-bottom-buttons">
        <div class="radio-buttons-2">
          <label>
            <input type="radio" name="luminous_opt_in" value="1" data-sound="SE_WAVE_SELECT_TAB">
            Alert
          </label>
          <label>
            <input type="radio" name="luminous_opt_in" value="0" data-sound="SE_WAVE_SELECT_TAB">
            Don't Alert
          </label>
        </div>
    </div>
  </div>
</div>
<a href="#" class="fixed-bottom-button left back-button slide-button accesskey-B" data-slide="#welcome-guideline" data-sound="SE_WAVE_BACK">Back</a>
<a href="#" class="fixed-bottom-button right next-button welcome-luminous_opt_in-button" data-slide="#welcome-finish">Next</a>
</div>
<div id="welcome-finish" class="slide-page none" data-bgm="JGL_OLV_INIT_END" data-slide-number="9">
<div class="window-page">
  <div class="window welcome-window">
    <div class="window-body"><div class="window-body-inner message">
      <p>Have fun in Miiverse!</p>
    </div></div>
  </div>
</div>
<a href="/welcome/activate?update_with_default=1" class="fixed-bottom-button right next-button welcome-finish-button" data-activate-url="/welcome/activate?update_with_default=1" data-replace="1">Start</a>
</div>


    </div>
  


<button type="button" class="accesskey-L" style="display: none;"></button><button type="button" class="accesskey-R" style="display: none;"></button></body>