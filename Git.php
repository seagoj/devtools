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
    private $_debug;

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
    public function __construct($user=null,$host='github')
    {
        $this->_debug = false;
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
        if ($user!=null) {
            $this->user($user);
            $this->_setHash();
        }
        $this->_redis = new \Predis\Client();
        $this->host($host);
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
        switch($this->_host) {
        case 'github':
            $this->_classHash = array('repos_url'=>'https://api.github.com/users/'.$this->_user.'/repos');
            break;
        default:
            die("Host $host is not implemented.");
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
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
        $this->_debug ? print "<div>".__METHOD__."</div>" : print "";
        $raw = json_decode(file_get_contents($this->_classHash['repos_url']));

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
