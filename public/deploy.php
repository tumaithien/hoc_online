<?php
chdir('..');
$output = shell_exec('git pull'); 
$output = shell_exec('git status'); 
echo "<pre>$output</pre>";
echo "finish";
die();
