<?PHP
require_once './initialize/init.php';

/*
* Demonstration page
*
* Prints site info
*/

session_start();

if (array_key_exists('access_token',$_SESSION))
{
    $info = $wearegeek->fetch('2020-01-01','2020-02-01');
    ?><pre><?=print_r($info);?></pre><?PHP
}
else
{
    session_destroy();
    header('Location: index.php');
}


?>