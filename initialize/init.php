<?PHP
require_once './vendor/autoload.php';
require_once './googleclient/clientparams.php';
require_once './libs/siteinfo.php';

/*
* Initializing Siteinfo class;
*/

$wearegeek = new Siteinfo($client_id, $client_secret, $url, $redirect);

?>

