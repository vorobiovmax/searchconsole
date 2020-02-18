<?PHP
/**
* Siteinfo class 
*/

class Siteinfo
{
    /*
    * Construct(client id, client secret, site url, redirect uri)
    * Set Google client's id, secret, redirect, scopes
    * Initialize google webmaster, site url variable.
    */
    function __construct($client_id , $client_secret, $site_url, $callback)
    {
        $this->client = new Google_Client;
        $this->webmaster = new \Google_Service_Webmasters($this->client);
        $this->siteurl = $site_url; 
        
        $this->client->setClientId($client_id);  
        $this->client->setClientSecret($client_secret);
        $this->client->setRedirectUri($callback);
        $this->client->setScopes([  
                                    'https://www.googleapis.com/auth/webmasters.readonly',
                                    'https://www.googleapis.com/auth/webmasters' 
                                ]);
    }

    /*
    * getGoogleAuthUrl
    * returns Google OAuth url.
    */
    public function getGoogleAuthUrl()
    {
        return $this->client->createAuthUrl();        
    }

    /*
    * acceptGoogleAuthCode
    * @param $code - Google OAuth autorization code
    * Authenticates google client, get tokens & put them in session
    */
    public function acceptGoogleAuthCode($code)
    {
        $this->client->authenticate($code);
        $tokens = $this->client->getAccessToken();
        $this->setTokenInSession($tokens);
        $this->client->setAccessToken($tokens);
    }

    /*
    * acceptGoogleAuthCode
    *
    * @param $tokens - Google client access tokens
    *
    * Put tokens in session
    */
    protected function setTokenInSession($tokens)
    {
        $_SESSION['access_token'] = $tokens['access_token'];
    }

    /*
    * getTokensFromSession
    *
    * Take tokens from session
    */
    protected function getTokenFromSession(){
        if ($_SESSION['access_token']) {
            $token['access_token'] = $_SESSION['access_token'];
           
            return $token;
        }
        return false;
    }

    /*
    * fetch
    *
    * @params $startDate, $endDate - string, format: 'YYYY-MM-DD'
    *
    * Returns array [
    *                   [query] => [
    *                       clicks,
    *                       shows,
    *                       position
    *                       ],
    *                    ... 
    *               ]
    */
    public function fetch($startDate, $endDate)
    {
        $this->client->setAccessToken($this->getTokenFromSession());

        $searchRequest = new Google_Service_Webmasters_SearchAnalyticsQueryRequest( [
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'dimensions' => ['query']
        ]);

        $getObjects =  $this->webmaster->searchanalytics->query($this->siteurl, $searchRequest);

        foreach($getObjects as $key => $value)
        {
            $siteInfo[$value->keys[0]] =[
                'clicks' => $value->clicks,
                'shows' => $value->impressions,
                'position' => $value->position
            ];
        }
        return $siteInfo;
    }
}
?>