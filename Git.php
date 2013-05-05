<?php
/**
 * Class to interface with the Github API
 * 
 * @category Seagoj
 * @package  Git
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/portfolio/LICENSE MIT
 * @link     http://github.com/seagoj/portfolio
 * 
 * Flow:    1) check redis first
 *          2) if redis is not populated
 *          3) get information through github API
 *          4) send response
 *          5) populate redis
 **/

namespace Devtools;

/**
 * Class to interface with the Github API
 * 
 * @category Seagoj
 * @package  Git
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/portfolio/LICENSE MIT
 * @link     http://github.com/seagoj/portfolio
 * 
 **/
class Git
{
    private $_user;
    private $_host;
    private $_classHash;
    private $_reposHash;
    private $_reposList;
    private $_redis;
    private $_log;
//    private $_debug;

    /**
     * public Git.__construct()
     *
     * Constructor for Git class
     * 
     * @param string $user OPTIONAL: sets user for host
     * @param string $host OPTIONAL: sets host for git server
     * 
     * @return void
     */
    public function __construct($options)
    {
        $defaults = array(
            'user'=>null,
            'host'=>'github'
        );

        $config = array_merge($defaults, $options);

        $logOpt = array('type'=>'html');
        $this->_log = new \Devtools\Log($logOpt);

        $this->host($config['host']); 
        if ($config['user']!=null) {
            $this->user($config['user']);
            $this->_setHash();
        }
        $this->_redis = new \Predis\Client();
    }

    /**
     * public Git.user()
     *
     * Sets user for Repo access
     * 
     * @param string $user User for repo access.
     *
     * @return void 
     */
    public function user($user)
    {
        $this->_user = $user;
        $this->_setHash();
        return $this->_user == $user;
    }

    /**
     * public Git.host()
     * 
     * @param string $host String signifying which git host to connect to. Defaults to 'github' in Git.__construct
     * 
     * @return void
     */
    public function host($host)
    {
        $this->_host = $host;
        if(isset($this->_user))
            $this->_setHash();
        return $this->_host == $host;
    }

    /**
     * private Git._setHash()
     *
     * Sets variable hash for class Git
     *
     * @todo reorganize Git.$_classHash and Git.$_repos into something more intuitive
     * 
     * @return void
     */
    private function _setHash()
    {
        switch($this->_host) {
        case 'github':
            $this->_classHash = array('repos_url'=>'https://api.github.com/users/'.$this->_user.'/repos');
            break;
        default:
            die("Host $this->_host is not implemented.");
            break;
        }

        $this->_populate();
    }

    /**
     * private Git.checkRedis()
     * 
     * Checks redis for the passed values
     * 
     * @param string $hash Repo to search through; Null means all repos
     * @param string $key  Key for return value
     *
     * @return string or array
     */
    private function _checkRedis($hash, $key=null)
    {
        if ($key==null) {
            $ret = $this->_redis->hgetall($hash);
        } else {
            $ret = $this->_redis->hget($hash, $key);
        }

        return $ret;
    }

    /**
     * public Git.listRepos()
     *
     * Returns an array of repos found in polling Git.host
     * 
     * @return array $ret Array of all repos found in polling Git.host
     */
    public function listRepos()
    {
        if (!isset($this->reposList)) {
            $this->_populate();
        }

        return $this->_reposList;
    }

    /**
     * public Git.get()
     *
     * Returns value based on hash and key
     *
     * @param string $hash Hash to look for $key
     B
     * @param string $key  Key for returned value
     * 
     * @return string
     */
    public function get($hash, $key)
    {
        if ($ret = $this->_checkRedis($hash, $key)) {
            return $ret;
        } else {
            $this->_populate();
            if ($ret = $this->_checkRedis($hash, $key)) {
                return $ret;
            } else {
                die("key not found");
            }
        }
    }

    /**
     * private Git._populate()
     *
     * Populate redis hashes from Git API
     *
     * @return void
     */
    private function _populate()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_classHash['repos_url']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'seagoj@gmail.com');
        $raw = curl_exec($ch);
        curl_close($ch);

        $limitMessage = 'API Rate Limit Exceeded';
        $raw = json_decode($raw);
        var_dump($raw);
        

        if ($raw['message'] && substr($raw['message'], 0 , strlen($limitMessage)-1) == $limitMessage) {
            throw new \Exception($raw['message']); 
        }

        $this->_log->write($raw);

     /*   $postdata = http_build_query(
            array(
                'user'=>'seagoj',
                'u'=>'seagoj'
            )
        );

        $opts = array(
            'https'=>array(
                'method'=> 'POST',
                'header'=>'Content-type: application/x-www-form-urlencoded',
                'content'=>$postdata
            )
        );

        $context = stream_context_create($opts);

        $raw = json_decode(file_get_contents($this->_classHash['repos_url'], false, $context));
        */

        $list = array();
        foreach ($raw AS $data) {
            $hash = $data->full_name;
            array_push($list, $hash);
            $this->_redis->expire($hash, 1800);
            //print $hash;
            foreach ($data AS $key=>$value) {
                if (is_object($value)) {
                    foreach ($value AS $subkey=>$subvalue) {
                        $this->_redis->hset($hash, $key.'.'.$subkey, $subvalue);
                    }
                } else {
                    $this->_redis->hset($hash, $key, $value);
                }
            }
            $this->_reposHash[$hash] = $this->_redis->hgetall($hash);
        }
        $this->_reposList = $list;
    }
}
