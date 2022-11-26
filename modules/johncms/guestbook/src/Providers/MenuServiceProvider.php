<?php

declare(strict_types=1);

namespace Johncms\Guestbook\Providers;

use Johncms\ServiceProvider;
use Johncms\View\Menu\Menu;
use Johncms\View\Menu\MenuFactory;
use Johncms\View\Menu\MenuItem;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        MenuFactory::create(Menu::PUBLIC_SIDEBAR)
            ->add(
                new MenuItem(
                    code:    'guestbook',
                    url:     route('guestbook.switch_type'),
                    name:    __('Guestbook'),
                    icon:    'chat',
                    counter: ['counters', 'guestbookCounters'],
                    sort:    30
                )
            );

        MenuFactory::create(Menu::PUBLIC_ADMIN)
            ->add(
                new MenuItem(
                    code:    'adminClub',
                    url:     route('guestbook.switch_type', queryParams: ['do' => 'set']),
                    name:    __('Admin Chat'),
                    icon:    'forum',
                    counter: ['counters', 'guestbookCounters']
                )
            );
    }
}
