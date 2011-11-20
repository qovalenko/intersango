<?php

function show_user_documents_for_user($uid)
{
    $dir = ABSPATH . "/docs/$uid";
    echo "<h3>$uid</h3>\n";

    $readme = ABSPATH . "/docs/$uid/00-README.txt";
    if (file_exists($readme)) {
        echo "<small><pre>\n";
        $fp = fopen($readme, 'r');
        while ($line = fgets($fp)) {
            $line = rtrim($line);
            // echo "    $line\n";
            $line = substr($line, 0, 25) . substr($line, 35);
            echo "  $line\n";
        }
        echo "</pre></small><br/>\n";
    }

    echo "<p>\n";
    $dp = opendir($dir);
    $candidates = array();
    while ($file = readdir($dp)) {
        if ($file == '00-README.txt' || $file == '.' || $file == '..') continue;
        echo "<form action='?page=download' method='post'>\n";
        echo "<input type='hidden' name='uid' value='$uid' />\n";
        echo "<input type='hidden' name='file' value='$file' />\n";
        echo "<input type='submit' value='$file' />\n";
        echo "</form>\n";
    }

    echo "</p>\n";
}

function show_user_documents()
{
?>
    <div class='content_box'>
    <h3>User Documents (newest first)</h3>
    <p>

<?php
    $users = array();
    $result = do_query("SELECT uid FROM users WHERE verified = 0");
    while ($row = mysql_fetch_array($result))
        array_push($users, $row['uid']);

    $dir = ABSPATH . "/docs";
    $dp = opendir($dir);
    $candidates = array();
    while ($uid = readdir($dp)) {
        if (!in_array($uid, $users)) continue;
        $path = "$dir/$uid";
        if (!is_dir($path)) continue;
        $candidates[$uid] = filemtime($path);
    }

    // newest first
    arsort($candidates);

    foreach ($candidates as $uid => $mtime)
        show_user_documents_for_user($uid);
?>

    </p>
    </div>
<?php
}

show_user_documents();

?>

