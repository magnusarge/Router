# Router
Easy setup of friendly urls in small static site.

1. Put the Router.php to the same folder where main index.php is.
2. Include Router.php to the index.php.
3. Configure routes in Router.php.

Script generates .htaccess automatically on the first run.
If you set up or change location of site, delete .htaccess, Router will (re)generate it only if there is no .htaccess file.

If you want execute code after Route class is called, change parameters $exitAfterSuccess and/or $exitAfterNotFound to false.
Browser's 404 error is shown only if $exitAfterNotFound is true. Otherwise, custom 404 (parameter $error404file) is displayed.
