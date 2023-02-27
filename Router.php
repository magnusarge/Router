<?php
$routes = [
    ["route"=>"/", "file"=>"/sample_pages/home.html"],
    ["route"=>"/contacts", "file"=>"/sample_pages/contacts.html"]
];

$router = new Router(__DIR__, $_SERVER['PHP_SELF'], $routes);

class Router
{
    private string $diskDir;
    private string $scriptRoot;
    private string $routeUrl;
    private bool $siteIsInSubfolder;
    private $exitAfterSuccess = true;
    private $exitAfterNotFound = true;
    private $error404file = "/pages/404.html";

    public function __construct($diskDir, $phpself, $routes=[])
    {

        $this->diskDir = $diskDir;
        $this->scriptRoot = dirname($phpself);
        $this->siteIsInSubfolder = $this->scriptRoot != '/';

        $routeUrl = parse_url($_SERVER['REQUEST_URI']);
        $this->routeUrl = $routeUrl['path'];
        if ( $this->siteIsInSubfolder ) {
            $this->routeUrl = substr($this->routeUrl, strlen($this->scriptRoot));
        }

        if ( !file_exists($this->diskDir . '/.htaccess') ) $this->createHtaccess();

        $this->route($routes);

    }

    private function route($routes): void
    {
        foreach ($routes as $route) {
            if ( isset($route['route']) && $this->compareRoutes($route['route'], $this->routeUrl) ) {
                if ( isset($route['file']) && file_exists($this->diskDir . $route['file']) ) {
                    include $this->diskDir . $route['file'];
                    if ( $this->exitAfterSuccess ) exit();
                    return;
                }
            }
        }
        $this->notFound();     
    }

    private function compareRoutes($route1, $route2): bool
    {
        return $this->formatRouteString($route1) === $this->formatRouteString($route2);
    }

    private function formatRouteString($route): string
    {
        return strtoupper($this->removeEndingSlash($route));
    }

    private function removeEndingSlash($string): string
    {
        if ( strlen($string) > 1 && str_ends_with($string, '/') ) {
            return substr($string, 0, -1);
        }
        return $string;
    }

    private function notFound(): void
    {
        header(header: "HTTP/1.0 404 Not Found");
        if ( $this->exitAfterNotFound ) exit();
        
        if ( file_exists($this->diskDir . $this->error404file) ) {
            include $this->diskDir. $this->error404file;
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
        file_put_contents($this->diskDir . '/.htaccess', $htaccess);
    }
}