## Devtools [![Build Status](https://codeship.com/projects/e802aba0-bc45-0131-50bf-127f5dbe26ea/status?branch=master)]

Devtools is a collection of PHP libraries designed for rapid development and debugging.

* **Dbg**:
Debuging class that manages exceptions and debugging output

* **Git**:
Class to interface with the GitHub API

* **Markdown**:
Class to translate Markdown into prper HTML code

* **Model**:
Data access class to be used in MVC type architectures

* **RandData**:
Generates random test data of specific type to debug with

* **Unit**
Unit tester

#### Installation and reference
Clone the repo to /lib/Devtools in your project file

Copy dep/autoloader.php to the Document Root and reference it in your project files as below:

    require_once $_SERVER['DOCUMENT_ROOT'].'/autoloader.php';

##### Usage
Calls to namespaces must be Fully-Qualified (must begin with a leading backspace)

    $dbg = new \Devtools\Dbg();
