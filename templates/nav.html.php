<?php
/** @var $router \App\Service\Router */

?>
<ul>
    <li><a href="<?= $router->generatePath('search') ?>">Wyszukiwarka</a></li>
    <li><a href="<?= $router->generatePath('search-random') ?>">Losowy film</a></li>
</ul>
<?php
