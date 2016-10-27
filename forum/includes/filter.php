<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');

require('../system/head.php');

if (!$id) {
    echo functions::display_error(_t('Wrong data'), '<a href="index.php">' . _t('Forum') . '</a>');
    require('../incfiles/end.php');
    exit;
}

switch ($do) {
    case 'unset':
        // Удаляем фильтр
        unset($_SESSION['fsort_id']);
        unset($_SESSION['fsort_users']);
        header("Location: index.php?id=$id");
        break;

    case 'set':
        // Устанавливаем фильтр по авторам
        $users = isset($_POST['users']) ? $_POST['users'] : '';

        if (empty($_POST['users'])) {
            echo '<div class="rmenu"><p>' . _t('You have not selected any author') . '<br /><a href="index.php?act=filter&amp;id=' . $id . '&amp;start=' . $start . '">' . _t('Back') . '</a></p></div>';
            require('../incfiles/end.php');
            exit;
        }

        $array = [];

        foreach ($users as $val) {
            $array[] = intval($val);
        }

        $_SESSION['fsort_id'] = $id;
        $_SESSION['fsort_users'] = serialize($array);
        header("Location: index.php?id=$id");
        break;

    default :
        /** @var PDO $db */
        $db = App::getContainer()->get(PDO::class);

        // Показываем список авторов темы, с возможностью выбора
        $req = $db->query("SELECT *, COUNT(`from`) AS `count` FROM `forum` WHERE `refid` = '$id' GROUP BY `from` ORDER BY `from`");
        $total = $req->rowCount();

        if ($total) {
            echo '<div class="phdr"><a href="index.php?id=' . $id . '&amp;start=' . $start . '"><b>' . _t('Forum') . '</b></a> | ' . _t('Filter by author') . '</div>' .
                '<form action="index.php?act=filter&amp;id=' . $id . '&amp;start=' . $start . '&amp;do=set" method="post">';
            $i = 0;

            while ($res = $req->fetch()) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<input type="checkbox" name="users[]" value="' . $res['user_id'] . '"/>&#160;' .
                    '<a href="../profile/?user=' . $res['user_id'] . '">' . $res['from'] . '</a> [' . $res['count'] . ']</div>';
                ++$i;
            }

            echo '<div class="gmenu"><input type="submit" value="' . _t('Filter') . '" name="submit" /></div>' .
                '<div class="phdr"><small>' . _t('Filter will be display posts from selected authors only') . '</small></div>' .
                '</form>';
        } else {
            echo functions::display_error(_t('Wrong data'));
        }
}

echo '<p><a href="index.php?id=' . $id . '&amp;start=' . $start . '">' . _t('Back to topic') . '</a></p>';
