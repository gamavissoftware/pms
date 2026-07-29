<?php
exec ( 'sudo crontab -u apache -r' );
file_put_contents ( '/home/prestomitr80/public_html/crontab.txt',"25 15 * * * php /home/prestomitr80/public_html/index.php Testcontroller sendmail/1".PHP_EOL,FILE_APPEND);
exec ( 'crontab /home/prestomitr80/public_html/crontab.txt' );
?>