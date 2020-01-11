<?php 
return array (
  'URL_MODEL' => 0,
  'URL_HTML_SUFFIX' => '.html',
  'URL_PATHINFO_DEPR' => '/',
  'URL_ROUTER_ON' => true,
  'URL_ROUTE_RULES' => 
  array (
    '/^jobfair\/(?!admin)(\w+)$/' => 'jobfair/index/:1',
    '/^mall\/(?!admin)(\w+)$/' => 'mall/index/:1',
  ),
  'QSCMS_VERSION' => '5.0.1',
  'QSCMS_RELEASE' => '2019-03-19 00:00:00',
  'SESSION_OPTIONS' => 
  array (
    'domain' => '.localhost',
    'path' => 'D:\php\PHPTutorial\WWW\qichen\data\session',
  ),
  'COOKIE_DOMAIN' => '.localhost',
);