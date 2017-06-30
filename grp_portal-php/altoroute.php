<?php
require_once('../altorouter.php');

$router = new AltoRouter();

$router->addRoutes(array(
['GET','/communities', 'communities.php', 'Communities-show'],
['GET','/titles/[i:title_id]', 'titles.php', 'Title-retrieve'],
['GET','/titles/[i:title_id]/[i:community_id]', 'titles.php', 'Community-retrieve'],
['GET','/titles/[i:title_id]/[i:community_id]/[new|hot:mode]', 'titles.php', 'Community-retrieve'],
['GET','/titles/[i:title_id]/[i:olive_community_id]/favorite.json','','Community-favorite'],
['GET','/titles/[i:title_id]/[i:olive_community_id]/unfavorite.json','','Community-unfavorite'],
));


// Match the current request
$match = $router->match();
if($match) {
  foreach($param as &$match['params']) {
  $_GET[key($param)] = $param;
  }
  require_once $match['target'];
}
else {
  require '404.php';
}