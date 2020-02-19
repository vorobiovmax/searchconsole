<?PHP 
require_once 'initialize/init.php';

/*
* Check: is access token exists
* true -> redirect to demonstration
* false -> create link for Google OAuth
*/
session_start();

if (array_key_exists('access_token',$_SESSION))
{
   header('Location: demo.php');
}
else
{
    $info = $wearegeek->getGoogleAuthUrl();
?>
<a href="<?=$info;?>">login</a><?PHP
}
?>