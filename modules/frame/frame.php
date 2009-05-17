<?php

echo '<iframe style="border-style:none;" id="iframe" height="600" width="100%" src="' . urldecode($_GET['url']) . '" name="iframe">
<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
Sie können die eingebettete Seite über den folgenden Verweis
aufrufen: <a href="' . urldecode($_GET['url']) . '">' . urldecode($_GET['url']) . '</a></p>
</iframe>';

?>