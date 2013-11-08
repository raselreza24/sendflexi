<?php

if (!defined("_APP_RUN"))

    die('Direct access to this location is not allowed.');

define("BASEPATH", dirname(__FILE__));



$configFile = BASEPATH .DIRECTORY_SEPARATOR. "AppConfig.php";



if (file_exists($configFile)) {

    include($configFile);

} else {

    header("Location: ../install/");

}

try {

    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);

}

catch(PDOException $e)

{

    echo $e->getMessage();

}



$stmt = $dbh->prepare("select * from appconfig"); $stmt->execute(); $result = $stmt->fetchAll();

foreach($result as $value){$config[$value['setting']]=$value['value'];}

function prf(){

    $htref= extract(parse_url($_SERVER['HTTP_REFERER']));

    $htref= $host;

    if ("$htref" != $_SERVER['HTTP_HOST']){

        exit("Error: 1001 prf ") ;

    }

}

function safedata($value){

    $value = trim($value);

    $value=htmlentities($value, ENT_QUOTES, 'utf-8');



    return $value;

}

//Extend

function _post($param,$defvalue = '') {

    if(!isset($_POST[$param])) 	{

        return $defvalue;

    }

    else {

        return safedata($_POST[$param]);

    }

}



function _get($param,$defvalue = '')

{

    if(!isset($_GET[$param])) {

        return $defvalue;

    }

    else {

        return safedata($_GET[$param]);

    }

}

function lc($v){

    global $config;

    $c = $config[$v];

    return $c;

}

function r2($to,$ntype='e',$msg=''){

    if($msg==''){

        header("location: $to"); exit;

    }

   $_SESSION['ntype']=$ntype ; $_SESSION['notify']=$msg ; header("location: $to"); exit;

}

function notify(){

    if(isset($_SESSION['notify'])) {

        $notify = $_SESSION['notify'];

        $ntype = $_SESSION['ntype'];

        if ($ntype=='s') {

            echo "<div class=\"alert alert-success\">

  <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>

   $notify 

</div>";

        }

        else {



            echo "<div class=\"alert alert-error\">

  <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>

  <strong>Error: </strong> $notify 

</div>";

        }

        unset($_SESSION['notify']);

        unset($_SESSION['ntype']);

    }

}



function _authenticate() {

    if(!isset($_SESSION['cid'])) {

        r2('../login.php');

    }



}

function _log($type,$details,$userid='0') {

    global $dbh;

    $ip=$_SERVER['REMOTE_ADDR'];

    $stmt = $dbh->prepare("INSERT INTO  logs (date ,type ,description ,userid ,ip)

    VALUES (now(),  ?,  ?,  ?,  ?);");

    $stmt->execute(array($type,$details,$userid,$ip));

    return true;

}

//uid

function _raid($l){

  $r=  substr(str_shuffle(str_repeat('0123456789',$l)),0,$l);

    return $r;

    

}

//

//simple template engine

function _render($template,$data)

{



    foreach($data as $key => $value)

    {

        $template = str_replace('{{'.$key.'}}', $value, $template);

    }



    return $template;

}

function _encode ($string){

    $encoded = base64_encode(uniqid().$string);

    echo $encoded;



}

function _decode ($string){

    $decoded = base64_decode($string);

    $orig = substr($decoded, 13);

    return $orig;

}

//

$xstage = lc('appStage');

require 'lib/d.f.php';

//$xdatetime = date('Y-m-d H:i:s');

function emailLog($userid,$email,$subject,$message){

  $date = date('Y-m-d H:i:s');

  $d = ORM::for_table('email_logs')->create();

  $d->userid = $userid;

  $d->email = $email;

  $d->subject = $subject;

  $d->message = $message;

  $d->date = $date;

  $d->save();

  $id = $d->id();

  return $id;

}



//

$ext = EXT;

$xheader='';

$xfooter='';



#AppINIT V 1.3

#Load Language File

$lan_file = 'apps/lan/'.lc('defaultclientlanguage').'.php';

require ($lan_file);





#

