<?php
define('APP_LIBRARY', realpath(__DIR__ . DIRECTORY_SEPARATOR . 'library'));
define('TEMPLATE_DIR', __DIR__.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR);
define('CSSPATH', 'public'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR);
define('IMAGEPATH', 'public'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR);
define('JSPATH', 'public'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR);
define('IMAGE_DIR', 'public'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR);
define('TITLE_PAGE', 'EHA - Economic House Application');
define('PAGENAMEAPP', 'Economic House Application');
require_once(APP_LIBRARY . DIRECTORY_SEPARATOR . 'Classloader.php');
$loader = new \StoredLibrary\Classloader(APP_LIBRARY);
$loader->registerNamespace('StoredLibrary', __DIR__ . DIRECTORY_SEPARATOR . 'library');
$loader->register();
try {
    \StoredLibrary\Application::getInstance()->run();
} catch (\Exception $e) {
    $configs = array(
        'ERRO' => $e->getMessage(),
        'TRACE' => $e->getTraceAsString()
    );
    die(\StoredLibrary\Template::display(TEMPLATE_DIR.'pageTrace.tpl.html', $configs));
}