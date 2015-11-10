
<div style='margin:0 auto; width:780px'>
<?
echo $comments;

?>


</div>
<!--<center><a href='?op=clear-all'>очистить все комменты</a></center>-->
<?
      // Проверяем нужны ли стрелки назад
    $pervpage = false;
    $page2left = false;
    $page1left = false;
    $page1right = false;
    $page2right = false;
    $nextpage = false;
    if ($page != 1) $pervpage = '<a href="/demos/blog/?page=-1"><<</a>
    <a href="/demos/blog/?page='. ($page - 1).'"><</a>';
    // Проверяем нужны ли стрелки вперед
    if ($page != $total) $nextpage = '  <a href="/demos/blog/?page='. ($page + 1).'">></a>
    <a href="/demos/blog/?page='.$total.'">>></a> ';
    // Находим две ближайшие станицы с обоих краев, если они есть
    if($page - 2 > 0) $page2left = ' <a href="/demos/blog/?page='. ($page - 2) .'">'. ($page - 2) .'</a>';
    if($page - 1 > 0) $page1left = '<a href="/demos/blog/?page='. ($page - 1) .'">'. ($page - 1) .'</a>';
    if($page + 2 <= $total) $page2right = '<a href="/demos/blog/?page='. ($page + 2).'">'. ($page + 2) .'</a>';
    if($page + 1 <= $total) $page1right = '<a href="/demos/blog/?page='. ($page + 1).'">'. ($page + 1) .'</a>';
?>
      <div id="n_paginator">
        <ul>
          <?if($pervpage != false){ echo '<li>'.$pervpage.'</li>';}?>
          <?if($page2left != false){ echo '<li>'.$page2left.'</li>';}?>
          <?if($page1left != false){ echo '<li>'.$page1left.'</li>';}?>
          <?if($page1right != false){ echo '<li>'.$page1right.'</li>';}?>
          <?if($page2right != false){ echo '<li>'.$page2right.'</li>';}?>
          <?if($nextpage != false){ echo '<li>'.$nextpage.'</li>';}?>
        </ul>
      </div>