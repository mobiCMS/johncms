<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

use Johncms\UserProperties;

defined('_IN_JOHNCMS') || die('Error: restricted access');

$title = __('Incoming messages');
$nav_chain->add($title);

/** @var Johncms\System\Legacy\Bbcode $bbcode */
$bbcode = di(Johncms\System\Legacy\Bbcode::class);

$total = $db->query(
    "
	SELECT COUNT(DISTINCT `cms_mail`.`user_id`)
	FROM `cms_mail`
	LEFT JOIN `cms_contact`
	ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
	AND `cms_contact`.`user_id`='" . $user->id . "'
	WHERE `cms_mail`.`from_id`='" . $user->id . "'
	AND `cms_mail`.`sys`='0' AND `cms_mail`.`delete`!='" . $user->id . "'
	AND `cms_contact`.`ban`!='1' AND `spam`='0'"
)->fetchColumn();

if ($total) {
    $req = $db->query(
        "SELECT `users`.`id`, `users`.`name`, `users`.`rights`, `users`.`lastdate`, `users`.`ip`, MAX(`cms_mail`.`time`) AS `time`
		FROM `cms_mail`
		LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id`
		LEFT JOIN `cms_contact` ON `cms_mail`.`user_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='" . $user->id . "'
		WHERE `cms_mail`.`from_id`='" . $user->id . "'
		AND `cms_mail`.`delete`!='" . $user->id . "'
		AND `cms_mail`.`sys`='0'
		AND `cms_contact`.`ban`!='1'
		GROUP BY `users`.`id`, `users`.`name`, `users`.`rights`, `users`.`lastdate`, `users`.`ip`
		ORDER BY MAX(`cms_mail`.`time`) DESC
		LIMIT " . $start . ',' . $user->config->kmess
    );

    while ($row = $req->fetch()) {
        $count_message = $db->query(
            "SELECT COUNT(*) FROM `cms_mail`
            WHERE `user_id`='{$row['id']}'
            AND `from_id`='" . $user->id . "'
            AND `delete`!='" . $user->id . "'
            AND `sys`!='1'
        "
        )->fetchColumn();

        $last_msg = $db->query(
            "SELECT *
            FROM `cms_mail`
            WHERE `from_id`='" . $user->id . "'
            AND `user_id` = '{$row['id']}'
            AND `delete` != '" . $user->id . "'
            ORDER BY `id` DESC
            LIMIT 1"
        )->fetch();

        if (mb_strlen($last_msg['text']) > 500) {
            $text = mb_substr($last_msg['text'], 0, 500);
            $text = $tools->checkout($text, 1, 1);
            $text = $tools->smilies($text, $row['rights'] ? 1 : 0);
            $text = $bbcode->notags($text);
            $text .= '...<a href="?act=write&amp;id=' . $row['id'] . '">' . __('Continue') . ' &gt;&gt;</a>';
        } else {
            // Или, обрабатываем тэги и выводим весь текст
            $text = $tools->checkout($last_msg['text'], 1, 1);
            $text = $tools->smilies($text, $row['rights'] ? 1 : 0);
        }

        $row['count_message'] = $count_message;
        $row['display_date'] = $tools->displayDate($last_msg['time']);
        $row['preview_text'] = $text;
        $row['unread'] = ! $last_msg['read'];

        $row['user_id'] = $row['id'];
        $user_properties = new UserProperties();
        $user_data = $user_properties->getFromArray($row);
        $row = array_merge($row, $user_data);

        $row['write_url'] = '?act=write&amp;id=' . $row['id'];
        $row['buttons'] = [
            [
                'url'  => '?act=write&amp;id=' . $row['id'],
                'name' => __('Correspondence'),
            ],
            [
                'url'  => '?act=deluser&amp;id=' . $row['id'],
                'name' => __('Delete'),
            ],
            [
                'url'  => '?act=ignor&amp;id=' . $row['id'] . '&amp;add',
                'name' => __('Block User'),
            ],
        ];

        $items[] = $row;
    }
}

$data['back_url'] = '../profile/?act=office';

$data['total'] = $total;
$data['pagination'] = $tools->displayPagination('?act=input&amp;', $start, $total, $user->config->kmess);
$data['items'] = $items ?? [];

echo $view->render(
    'mail::conversations',
    [
        'title'      => $title,
        'page_title' => $title,
        'data'       => $data,
    ]
);
