<?php
/**
 * General testing for Autoload class
 *
 * @name      index.php
 * @category  Devtools
 * @package   Seagoj\NewProject
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/lib.autoload
 */

namespace NewProject;

print "<div>BoF</div>";
require_once '../lib/Devtools/Autoload.php';
\Devtools\Autoload::register();

$dbg = new \Devtools\Dbg(new Autoload());
$redis = new \Predis\Client();

print "<div>EoF</div>";
