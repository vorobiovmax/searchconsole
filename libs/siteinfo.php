<?PHP
/**
* Siteinfo class 
*/

class Siteinfo
{
    /*
    * Construct
    *
    * @param string $site_url - Site url 
    *
    * Set Google client's Auth data from json.
    * Initialize google webmaster, site url variable.
    */
    function __construct($site_url)
    {
        $this->client = new Google_Client;
        $this->webmaster = new \Google_Service_Webmasters($this->client);
        $this->siteurl = $site_url; 
        $this->client->setAuthConfig('client_secret.json');
        $this->client->setScopes([  
                                    'https://www.googleapis.com/auth/webmasters.readonly',
                                    'https://www.googleapis.com/auth/webmasters' 
                                ]);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt("force");
    }

    /*
    * getGoogleAuthUrl
    *
    * @return string - Google OAuth url.
    */
    public function getGoogleAuthUrl()
    {
        return $this->client->createAuthUrl();        
    }

    /*
    * acceptGoogleAuthCode
    *
    * @param string $code - Google OAuth autorization code
    *
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
    * @param array $tokens - Google client tokens
    *
    * Put tokens in session
    */
    protected function setTokenInSession($tokens)
    {
        $_SESSION['access_token'] = $tokens['access_token'];
        $_SESSION['token_type'] = $tokens['token_type'];
        $_SESSION['expires_in'] = $tokens['expires_in'];
        $_SESSION['refresh_token'] = $tokens['refresh_token'];
        $_SESSION['created'] = $tokens['created'];
    }

    /*
    * getTokensFromSession
    * 
    * @return array $tokens
    *
    * Take tokens from session
    */
    protected function getTokenFromSession(){
        if ($_SESSION['access_token']) {
            $tokens['access_token'] = $_SESSION['access_token'];
            $tokens['token_type'] = $_SESSION['token_type'];
            $tokens['expires_in'] = $_SESSION['expires_in'];
            $tokens['refresh_token'] = $_SESSION['refresh_token'];
            $tokens['created'] = $_SESSION['created'];
            return $tokens;
        }
        return false;
    }

    /*
    * fetch
    *
    * @param string $startDate - date to start, format: 'YYYY-MM-DD'
    * @param string $endDate - date to end, format: 'YYYY-MM-DD'
    * @return array [
    *                   [query] => [
    *                       clicks,
    *                       shows,
    *                       position
    *                       ],
    *                    ... 
    *               ]
    */
    public function fetch(string $startDate, string $endDate)
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

    /*
    * dateValid
    *
    * @param int $month - Month
    * @param int $year - Year
    * @return bool - is date correct
    *
    * Checking the valid of month and year
    */
    protected function dateValid(int $month, int $year)
    {
        if($month <= 12 && $month >= 1 && $year <= (int)date('Y'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
    * usingCurrectDate
    *
    * @param int $month - Month
    * @param int $year - Year
    * @return bool - is using currect date.
    *
    * Checking the use of currect month
    */
    protected function usingCurrectMonth(int $month, int $year)
    {
        if($year == (int)date('Y') && $month == (int)date('m'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

     /*
    * fetchMonth
    *
    * @param int $month - Month
    * @param int $year - Year
    * @return array [
    *                   [query] => [
    *                       clicks,
    *                       shows,
    *                       position
    *                       ],
    *                    ... 
    *               ]
    *
    */
    public function fetchMonth(int $month, int $year)
    {
        if($this->dateValid($month, $year))
        {
            if($this->usingCurrectMonth($month, $year))
            {
                $daysInMonth = (int)date('d');
            }
            else
            {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            }

            if($month < 10)
            {
                $month = '0'.$month;
            }

            $startDate = $year.'-'.$month.'-01';
            $endDate = $year.'-'.$month.'-'.$daysInMonth;

            return $this->fetch($startDate, $endDate);
        }
    }
}
?>