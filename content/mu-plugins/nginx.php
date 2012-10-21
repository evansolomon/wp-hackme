<?php
/*
Plugin name: nginx Pretty Permalinks
Description: Remove index.php from permalinks
*/

add_filter( 'got_rewrite', '__return_true', 999 );
