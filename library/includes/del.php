<?php
/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */
 
defined('_IN_JOHNCMS') or die('Error: restricted access');
$adm ?: redir404();

$type = isset($_GET['type']) && in_array($_GET['type'], array('dir', 'article', 'image')) ? $_GET['type'] : redir404();
$change = ($type == 'dir' ? mysql_result(mysql_query("SELECT count(*) FROM `library_cats` WHERE `parent`=" . $id) , 0) > 0 || mysql_result(mysql_query("SELECT count(*) FROM `library_texts` WHERE `cat_id`=" . $id) , 0) > 0 ? 0 : 1 : '');

switch ($type) {
case 'dir':
  if (mysql_result(mysql_query("SELECT count(*) FROM `library_cats` WHERE `id`=" . $id) , 0) == 0) {
    echo functions::display_error('Категории не существует');
  }
  elseif (!$change) {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($do) ? $do : false);
    $dirtype = mysql_result(mysql_query("SELECT `dir` FROM `library_cats` WHERE `id` = " . $id . " LIMIT 1"), 0);
    switch($mode) {
        case 'moveaction':
            if (!isset($_GET['movedeny'])) {
                echo '<div class="alarm"><div>Вы дейсвительно хотите переместить содержимое?</div><div><a href="?act=del&amp;type=' . $type . '&amp;id=' . $id . '&amp;movedeny&amp;do=moveaction&amp;move=' . intval($_POST['move']) . '">' . $lng['move'] . '</a> | <a href="?">' . $lng['cancel'] . '</a></div></div>';
            } else {
                $move = intval($_GET['move']);
                if ($dirtype) {
                    mysql_query("UPDATE `library_cats` SET `parent`=" . $move . " WHERE `parent` = " . $id);
                } else {
                    mysql_query("UPDATE `library_texts` SET `cat_id` = " . $move . " WHERE `cat_id` = " . $id);
                }
                
                if (mysql_affected_rows()) {
                    mysql_query("DELETE FROM `library_cats` WHERE `id` = " . $id);
                    if (mysql_affected_rows()) {
                        echo '<div class="gmenu">Успешно перенесено</div><div><a href="?do=dir&amp;id=' . $move . '">' . $lng['back'] . '</a></div>' . PHP_EOL;
                    }
                }
            }        
        break;
        
        case 'delmove':                 
            $child_dir = new Tree($id);
            $childrens = $child_dir->get_childs_dir()->result();
            $list = mysql_query("SELECT `id`, `name` FROM `library_cats` WHERE `dir`=" . $dirtype . " AND " . ($dirtype && sizeof($childrens) ? '`id` not in(' . implode(', ', $childrens) . ', ' . $id . ')' : '`id`  != ' . $id));
            if (mysql_num_rows($list)) {
            echo '<div class="menu">' 
            . '<h3>' . $lng_lib['move_dir'] . '</h3>'
            . '<form action="?act=del&amp;type=dir&amp;id=' . $id . '" method="post">'
            . '<div><select name="move">';
            while($rm = mysql_fetch_assoc($list)) {
                echo '<option value="' . $rm['id'] . '">' . $rm['name'] . '</option>';
            }
            echo '</select></div>'
            . '<div><input type="hidden" name="mode" value="moveaction" /></div>' 
            . '<div class="bmenu"><input type="submit" name="submit" value="' . $lng['approve'] . '" /></div>'
            . '</form>'
            . '</div>'; 
            } else {
                echo '<div class="rmenu">Нет разделов для перемещения</div><div class="bmenu"><a href="?">' . $lng['back'] . '</a></div>';
            }
        break;
        
        case 'delall':
            if (!isset($_GET['deldeny'])) {
                echo '<div class="alarm"><div>Вы дейсвительно хотите удалить содержимое?</div><div><a href="?act=del&amp;type=' . $type . '&amp;id=' . $id . '&amp;deldeny&amp;do=delall">' . $lng['delete'] . '</a> | <a href="?">' . $lng['cancel'] . '</a></div></div>';
            } else {
                $childs = new Tree($id);
                $deleted = $childs->get_all_childs_id()->clean_dir();
                echo '<div class="gmenu">Успешно удалено директорий (' . $deleted['dirs'] . ') , статей(' . $deleted['texts'] . '), тегов(' . $deleted['tags'] . '), коментариев(' . $deleted['comments'] . '), изображений(' . $deleted['images'] . ')</div><div><a href="?">' . $lng['back'] . '</a></div>' . PHP_EOL;
            }
        break;
        
        default:
            echo '<div class="alarm">Каталог не пустой</div>' 
            . '<div class="menu"><h3>Выбрать действие</h3>'
            . '<form action="?act=del&amp;type=dir&amp;id=' . $id . '" method="post">'
            . '<div><input type="radio" name="mode" value="delmove" checked="checked" /> Удалить с перемещением</div>'
            . '<div><input type="radio" name="mode" value="delall" /> <span style="color: red;">Удалить все вложенные каталоги и статьи</span></div>'
            . '<div class="bmenu"><input type="submit" name="submit" value="' . $lng['do'] . '" /></div>'
            . '</form>'
            . '</div>';
        break;
    }
  }
  else {
    $sql = "DELETE FROM `library_cats` WHERE `id`=" . $id;
    if (!isset($_GET['yes'])) {
      echo '<div class="alarm"><div>' . $lng['delete_confirmation'] . '</div><div><a href="?act=del&amp;type=' . $type . '&amp;id=' . $id . '&amp;yes">' . $lng['delete'] . '</a> | <a href="?do=dir&amp;id=' . $id . '">' . $lng['cancel'] . '</a></div></div>';
    }
  }
  break;

case 'article':
  if (mysql_result(mysql_query("SELECT count(*) FROM `library_texts` WHERE `id`=" . $id) , 0) == 0) {
    echo functions::display_error('Статьи не существует');
  }
  else {
    $sql = "DELETE FROM `library_texts` WHERE `id`=" . $id;
    if (!isset($_GET['yes'])) {
      echo '<div class="alarm"><div>' . $lng['delete_confirmation'] . '</div><div><a href="?act=del&amp;type=' . $type . '&amp;id=' . $id . '&amp;yes">' . $lng['delete'] . '</a> | <a href="?do=text&amp;id=' . $id . '">' . $lng['cancel'] . '</a></div></div>';
    }
  }
  break;
case 'image':
    if (!isset($_GET['yes'])) {
      echo '<div class="alarm"><div>' . $lng['delete_confirmation'] . '</div><div><a href="?act=del&amp;type=' . $type . '&amp;id=' . $id . '&amp;yes">' . $lng['delete'] . '</a> | <a href="?act=moder&amp;type=article&amp;id=' . $id . '">' . $lng['cancel'] . '</a></div></div>';
    }  
  break; 
}
if (isset($_GET['yes']) && $type == 'image') {
    if (file_exists('../files/library/images/small/' . $id . '.png')) {
      unlink('../files/library/images/big/' . $id . '.png'); 
      unlink('../files/library/images/orig/' . $id . '.png');
      unlink('../files/library/images/small/' . $id . '.png');
    }
    echo '<div class="">Удалено</div><div><a href="?act=moder&amp;type=article&amp;id=' . $id . '">' . $lng['back'] . '</a></div>' . PHP_EOL;
} elseif (isset($_GET['yes'])) {
  if (mysql_query($sql)) {
    if (file_exists('../files/library/images/small/' . $id . '.png')) {
      unlink('../files/library/images/big/' . $id . '.png'); 
      unlink('../files/library/images/orig/' . $id . '.png');
      unlink('../files/library/images/small/' . $id . '.png');
    }
    echo '<div>Удалено</div><div><a href="?">' . $lng['back'] . '</a></div>' . PHP_EOL;
  }
}