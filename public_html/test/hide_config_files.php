<?php
// hides specified config files
$hidden_config_files = array(
    "hide_config_files.php",
    "owa-config-dist.php",
    "owa-config.php",
);
foreach ($hidden_config_files as $x) {
    if (@is_file($x)) {
        chmod($x, 0600);
    }
}
?>
