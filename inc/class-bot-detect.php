<?php
/**
 *  class for detect bots.
 */
if (!class_exists('CrawlerDetect')) {

    class CrawlerDetect
    {
        /**
         * The user agent.
         *
         * @var null
         */
        protected $userAgent = null;

        /**
         * Headers that container user agent.
         *
         * @var array
         */
        protected $httpHeaders = array();

        /**
         * Store regex matches.
         *
         * @var array
         */
        protected $matches = array();

        /**
         * List of strings to remove from the user agent before running the crawler regex
         * Over a large list of user agents, this gives us about a 55% speed increase!
         *
         * @var array
         */
        protected static $ignore = array(
            'Safari.[\d\.]*',
            'Firefox.[\d\.]*',
            'Chrome.[\d\.]*',
            'Chromium.[\d\.]*',
            'MSIE.[\d\.]',
            'Opera\/[\d\.]*',
            'Mozilla.[\d\.]*',
            'AppleWebKit.[\d\.]*',
            'Trident.[\d\.]*',
            'Windows NT.[\d\.]*',
            'Android.[\d\.]*',
            'Macintosh.',
            'Ubuntu',
            'Linux',
            'Intel',
            'Mac OS X',
            'Gecko.[\d\.]*',
            'KHTML',
            'CriOS.[\d\.]*',
            'CPU iPhone OS ([0-9_])* like Mac OS X',
            'CPU OS ([0-9_])* like Mac OS X',
            'iPod',
            'like Gecko',
            'compatible',
            'x86_..',
            'i686',
            'x64',
            'X11',
            'rv:[\d\.]*',
            'Version.[\d\.]*',
            'WOW64',
            'Win64',
            'Dalvik.[\d\.]*',
            '\.NET CLR [\d\.]*',
            'Presto.[\d\.]*',
            'Media Center PC',
        );

        /**
         * Array of regular expressions to match against the user agent.
         *
         * @var array
         */
        protected static $crawlers = array(
            '008',
            'ABACHOBot',
            'Accoona-AI-Agent',
            'AddSugarSpiderBot',
            'AnyApexBot',
            'Arachmo',
            'B-l-i-t-z-B-O-T',
            'Baiduspider',
            'BecomeBot',
            'BeslistBot',
            'BillyBobBot',
            'Bimbot',
            'Bingbot',
            'BlitzBOT',
            'boitho.com-dc',
            'boitho.com-robot',
            'btbot',
            'CatchBot',
            'Cerberian Drtrs',
            'Charlotte',
            'ConveraCrawler',
            'cosmos',
            'Covario IDS',
            'DataparkSearch',
            'DiamondBot',
            'Discobot',
            'Dotbot',
            'EARTHCOM.info',
            'EmeraldShield.com WebBot',
            'envolk[ITS]spider',
            'EsperanzaBot',
            'Exabot',
            'FAST Enterprise Crawler',
            'FAST-WebCrawler',
            'FDSE robot',
            'FindLinks',
            'FurlBot',
            'FyberSpider',
            'g2crawler',
            'Gaisbot',
            'GalaxyBot',
            'genieBot',
            'Gigabot',
            'Girafabot',
            'Googlebot',
            'Googlebot-Image',
            'GurujiBot',
            'HappyFunBot',
            'hl_ftien_spider',
            'Holmes',
            'htdig',
            'iaskspider',
            'ia_archiver',
            'iCCrawler',
            'ichiro',
            'igdeSpyder',
            'IRLbot',
            'IssueCrawler',
            'Jaxified Bot',
            'Jyxobot',
            'KoepaBot',
            'L.webis',
            'LapozzBot',
            'Larbin',
            'LDSpider',
            'LexxeBot',
            'Linguee Bot',
            'LinkWalker',
            'lmspider',
            'lwp-trivial',
            'mabontland',
            'magpie-crawler',
            'Mediapartners-Google',
            'MJ12bot',
            'MLBot',
            'Mnogosearch',
            'mogimogi',
            'MojeekBot',
            'Moreoverbot',
            'Morning Paper',
            'msnbot',
            'MSRBot',
            'MVAClient',
            'mxbot',
            'NetResearchServer',
            'NetSeer Crawler',
            'NewsGator',
            'NG-Search',
            'nicebot',
            'noxtrumbot',
            'Nusearch Spider',
            'NutchCVS',
            'Nymesis',
            'obot',
            'oegp',
            'omgilibot',
            'OmniExplorer_Bot',
            'OOZBOT',
            'Orbiter',
            'PageBitesHyperBot',
            'Peew',
            'polybot',
            'Pompos',
            'PostPost',
            'Psbot',
            'PycURL',
            'Qseero',
            'Radian6',
            'RAMPyBot',
            'RufusBot',
            'SandCrawler',
            'SBIder',
            'ScoutJet',
            'Scrubby',
            'SearchSight',
            'Seekbot',
            'semanticdiscovery',
            'Sensis Web Crawler',
            'SEOChat::Bot',
            'SeznamBot',
            'Shim-Crawler',
            'ShopWiki',
            'Shoula robot',
            'silk',
            'Sitebot',
            'Snappy',
            'sogou spider',
            'Sosospider',
            'Speedy Spider',
            'Sqworm',
            'StackRambler',
            'suggybot',
            'SurveyBot',
            'SynooBot',
            'Teoma',
            'TerrawizBot',
            'TheSuBot',
            'Thumbnail.CZ robot',
            'TinEye',
            'truwoGPS',
            'TurnitinBot',
            'TweetedTimes Bot',
            'TwengaBot',
            'updated',
            'Urlfilebot',
            'Vagabondo',
            'VoilaBot',
            'Vortex',
            'voyager',
            'VYU2',
            'webcollage',
            'Websquash.com',
            'wf84',
            'WoFindeIch Robot',
            'WomlpeFactory',
            'Xaldon_WebSpider',
            'yacy',
            'Yahoo! Slurp',
            'Yahoo! Slurp China',
            'YahooSeeker',
            'YahooSeeker-Testing',
            'YandexBot',
            'YandexImages',
            'YandexMetrika',
            'Yasaklibot',
            'Yeti',
            'YodaoBot',
            'yoogliFetchAgent',
            'YoudaoBot',
            'Zao',
            'Zealbot',
            'zspider',
            'ZyBorg'
        );

        /**
         * All possible HTTP headers that represent the
         * User-Agent string.
         *
         * @var array
         */
        protected static $uaHttpHeaders = array(
            // The default User-Agent string.
            'HTTP_USER_AGENT',
            // Header can occur on devices using Opera Mini.
            'HTTP_X_OPERAMINI_PHONE_UA',
            // Vodafone specific header: http://www.seoprinciple.com/mobile-web-community-still-angry-at-vodafone/24/
            'HTTP_X_DEVICE_USER_AGENT',
            'HTTP_X_ORIGINAL_USER_AGENT',
            'HTTP_X_SKYFIRE_PHONE',
            'HTTP_X_BOLT_PHONE_UA',
            'HTTP_DEVICE_STOCK_UA',
            'HTTP_X_UCBROWSER_DEVICE_UA',
        );

        /**
         * Class constructor.
         */
        public function __construct(array $headers = null, $userAgent = null)
        {
            $this->setHttpHeaders($headers);
            $this->setUserAgent($userAgent);
        }

        /**
         * Set HTTP headers.
         *
         * @param array $httpHeaders
         */
        public function setHttpHeaders($httpHeaders = null)
        {
            // use global _SERVER if $httpHeaders aren't defined
            if (!is_array($httpHeaders) || !count($httpHeaders)) {
                $httpHeaders = $_SERVER;
            }
            // clear existing headers
            $this->httpHeaders = array();
            // Only save HTTP headers. In PHP land, that means only _SERVER vars that
            // start with HTTP_.
            foreach ($httpHeaders as $key => $value) {
                if (substr($key, 0, 5) === 'HTTP_') {
                    $this->httpHeaders[$key] = $value;
                }
            }
        }

        /**
         * Return user agent headers.
         *
         * @return array
         */
        public function getUaHttpHeaders()
        {
            return self::$uaHttpHeaders;
        }

        /**
         * Set the user agent.
         *
         * @param string $userAgent
         */
        public function setUserAgent($userAgent = null)
        {
            if (false === empty($userAgent)) {
                return $this->userAgent = $userAgent;
            } else {
                $this->userAgent = null;
                foreach ($this->getUaHttpHeaders() as $altHeader) {
                    if (false === empty($this->httpHeaders[$altHeader])) { // @todo: should use getHttpHeader(), but it would be slow.
                        $this->userAgent .= $this->httpHeaders[$altHeader].' ';
                    }
                }

                return $this->userAgent = (!empty($this->userAgent) ? trim($this->userAgent) : null);
            }
        }

        /**
         * Build the user agent regex.
         *
         * @return string
         */
        public function getRegex()
        {
            return '('.implode('|', self::$crawlers).')';
        }

        /**
         * Build the replacement regex.
         *
         * @return string
         */
        public function getIgnored()
        {
            return '('.implode('|', self::$ignore).')';
        }

        /**
         * Check user aganet string against the regex.
         *
         * @param string $userAgent
         *
         * @return bool
         */
        public function isCrawler($userAgent = null)
        {
            $agent = is_null($userAgent) ? $this->userAgent : $userAgent;

            $agent = preg_replace('/'.$this->getIgnored().'/i', '', $agent);

            $result = preg_match('/'.$this->getRegex().'/i', $agent, $matches);

            if ($matches) {
                $this->matches = $matches;
            }

            return (bool) $result;
        }

        /**
         * Return the matches.
         *
         * @return array
         */
        public function getMatches()
        {
            return $this->matches[0];
        }
    }
}