## Devtools
Devtools is a collection of PHP libraries designed for rapid development and debugging.

### Installation and reference
Clone the repo to /lib/Devtools in your project file

Copy dep/autoloader.php to the Document Root and reference it in your project files as below:
    
    require_once $_SERVER['DOCUMENT_ROOT'].'/autoload.php';

#### Usage
Calls to namespaces must be Fully-Qualified (must begin with a leading backspace)

    $dbg = new \Devtools\Dbg();
