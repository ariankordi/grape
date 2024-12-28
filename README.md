# grape_rv3_edition #
Based off of the uncanny grape. This code is absolute garbage but it's what the server runs on.

I try to add documentation to the API code so it works. If you don't understand something, ASK. Don't just modify it like that...

# Can I modify this directly?
Please do not, only I will. Create a new branch and submit a PR. You have to keep your branch up to date yourself.

# If you share this code, prepare to be choked to death, no exceptions.
I will literally find your home address. Don't share it. Keep it to yourself.

# PR and issue info
I'm not going to actually fix any issues made on the repo please send them in the discord.
If you send pull requests to try to make the code better please test the code yourself before you do anything. Don't modify static code such as
```
if($_SESSION["pid"] == "1738294576" || $_SESSION["pid"] == 1738294576){
	exit("Absolutely not.");
}
if($_SESSION["pid"] == "1739044112" || $_SESSION["pid"] == 1739044112){
	session_destroy();
}
```
If you modify it, you will be requested to make changes or I'm denying the PR/Not merging with the server. Don't accept the PR yourself either, I'll roll it back.

# grp_portal #
This is portal, or the Wii U mode.
# grp_offdevice #
The offdevice version. This was written when I knew more PHP then when I had written portal. It's way better written, and I'm trying to rewrite portal now.
# grp_act #
The account login and profile system shared across all of these.
# grplib #
Some shared libraries to be used across all of these. None of these are seen by the user.
# grp_image_processor #
Supposed to process TGAs submitted by in-game posts. Returns base64 images. Never finished. Not finishing it. It's hacky and horrible.

# How to install??? #
tl;dr setup two virtual hosts, one goes to the grp_portal-php dir, and one goes to the grp_offdevice-php dir. grp_n3ds-php is unfinished. but it partially works





























\


