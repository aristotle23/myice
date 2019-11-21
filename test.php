<?php
namespace Firebase;
require_once "vendor/ktamas77/firebase-php/src/firebaseLib.php";

const DEFAULT_URL = 'https://myice5050.firebaseio.com/';
const DEFAULT_TOKEN = 'Pearh4ykZdRJMxJcA9oODcqXmBr4uMs3cc1pQSFG';
const DEFAULT_TODO_PATH = '/location';

$firebase = new FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
$arr = $firebase->get(DEFAULT_TODO_PATH."/61");
$arr = json_decode($arr,true);

$arr = array_values($arr);

foreach ($arr as $a){
    print $a['date'];
}
