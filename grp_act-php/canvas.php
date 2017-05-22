<?php
require_once 'lib/htm.php';
$pagetitle = 'img';
printHeader(false);
?><?=$GLOBALS['div_body_head']?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
        <script>
            window.takeScreenShot = function() {
                html2canvas(document.getElementById("target"), {
                    onrendered: function (canvas) {
                        document.body.appendChild(canvas);
                    },
                width:320,
                height:220
                });
            }
        </script>
        <div id="target"><span class="icon-container official-user"><a href="/users/ariankordi"><img src="https://mii-secure.cdn.nintendo.net/h8cwfxypgnj6_happy_face.png" class="icon"></a></span></div>
        <button onclick="takeScreenShot()">image</button>
		<?=$GLOBALS['div_body_head_end']?>