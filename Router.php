<?php
$routes = [
    ["route"=>"/", "file"=>"/sample_pages/home.html"],
    ["route"=>"/contacts", "file"=>"/sample_pages/contacts.html"]
];

$router = new Router($routes);

class Router
{
    private string $scriptRoot;
    private string $routeUrl;
    private bool $siteIsInSubfolder;
    private $exitAfterSuccess = true;
    private $exitAfterNotFound = true;
    private $error404file = "/sample_pages/404.html";

    public function __construct($routes=[])
    {

        $this->scriptRoot = dirname($_SERVER['PHP_SELF']);
        $this->siteIsInSubfolder = $this->scriptRoot != '/';

        $routeUrl = parse_url($_SERVER['REQUEST_URI']);
        $this->routeUrl = $routeUrl['path'];
        if ( $this->siteIsInSubfolder ) {
            $this->routeUrl = substr($this->routeUrl, strlen($this->scriptRoot));
        }

        if ( !file_exists(__DIR__ . '/.htaccess') ) $this->createHtaccess();

        $this->route($routes);

    }

    private function route($routes): void
    {
        foreach ($routes as $route) {
            if ( isset($route['route']) && $route['route'] === $this->routeUrl ) {
                if ( isset($route['file']) && file_exists(__DIR__ . $route['file']) ) {
                    include __DIR__ . $route['file'];
                    if ( $this->exitAfterSuccess ) exit();
                    return;
                }
            }
        }
        $this->notFound();     
    }

    private function notFound(): void
    {
        header(header: "HTTP/1.0 404 Not Found");
        if ( $this->exitAfterNotFound ) exit();
        
        if ( file_exists(__DIR__ . $this->error404file) ) {
            include __DIR__ . $this->error404file;
        }
    }

    private function createHtaccess(): void
    {
        $htaccess  = sprintf(
            "RewriteEngine On\n".
            "RewriteBase %s\n".
            "RewriteCond %%{REQUEST_FILENAME} !-d\n".
            "RewriteCond %%{REQUEST_FILENAME} !-f\n".
            "RewriteRule ^(.*)$ index.php [QSA,L]",
            $this->scriptRoot);
        file_put_contents(__DIR__ . '/.htaccess', $htaccess);
    }
}