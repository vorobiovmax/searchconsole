<?PHP
require_once './initialize/init.php';

/*
* Redirect page after Google OAuth
* 
* If get Google authentication code - authenticate Google Client & redirect to demonstration
*/
if($_GET['code'] )
{
    session_start();
    $wearegeek->acceptGoogleAuthCode($_GET['code']);
    header("Location: demo.php");
}
?>