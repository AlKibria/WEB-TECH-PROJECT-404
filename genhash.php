<?php
// Run this ONCE at http://localhost/author_module/genhash.php
// Then delete this file after use
$hash = password_hash('password123', PASSWORD_BCRYPT);
echo "<pre style='font-size:18px;padding:20px;'>";
echo "Hash for 'password123':\n\n";
echo $hash;
echo "\n\nCopy the line above and paste it into the SQL query to replace PASTE_HASH_HERE</pre>";
?>
