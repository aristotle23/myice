<?php
/**
 * datatabase config
 */
define("SERVERNAME","localhost");
define("DBNAME","myice");
define("USERNAME", "root");
define("PASSWORD", "developers");
/**
 * encryption config
 */
define("OPENSSLMETHOD", "AES-128-CBC");
define("OPENSSLKEY",hash('sha256', "myice"));
define("OPENSSLOPTION",0);
define("OPENSSLIV",openssl_random_pseudo_bytes(openssl_cipher_iv_length(OPENSSLMETHOD)));
/**
 * firebase default settings
 */
define("FIREBASEURL","https://myice5050.firebaseio.com/");
define("FIREBASETOKEN","Pearh4ykZdRJMxJcA9oODcqXmBr4uMs3cc1pQSFG");
define("FIREBASEPATH","/location");
