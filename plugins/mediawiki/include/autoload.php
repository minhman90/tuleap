<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoloade1c01b9c38c2de62f2184f2a77fa3096($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'mediawikidao' => '/MediawikiDao.class.php',
            'mediawikiinstantiater' => '/MediawikiInstantiater.class.php',
            'mediawikiplugin' => '/mediawikiPlugin.class.php',
            'mediawikiplugindescriptor' => '/MediaWikiPluginDescriptor.class.php',
            'mediawikiplugininfo' => '/MediaWikiPluginInfo.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoloade1c01b9c38c2de62f2184f2a77fa3096');
// @codeCoverageIgnoreEnd