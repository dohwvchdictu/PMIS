<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OpCache cleared successfully!";
} else {
    echo "OpCache is not enabled";
}
phpinfo(INFO_GENERAL);
