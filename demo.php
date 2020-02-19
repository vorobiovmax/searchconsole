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
    $dates = parse_ini_file('dates.ini');

    $fetch_start = $dates['fetch_start'];
    $fetch_end = $dates['fetch_end'];
    
    $fetchMonth_month = (int)$dates['fetchMonth_month'];
    $fetchMonth_year = (int)$dates['fetchMonth_year'];

    /*
    * fetch & fetchMonth demonstration 
    */
    $info = $wearegeek->fetch($fetch_start, $fetch_end);
    //$info = $wearegeek->fetchMonth($fetchMonth_month, $fetchMonth_year);

    ?><pre><?=print_r($info);?></pre><?PHP

}
else
{
    header('Location: index.php');
}


?>