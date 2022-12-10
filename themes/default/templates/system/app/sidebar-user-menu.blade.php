<?php
/**
 * @var string $locale
 * @var Johncms\System\Legacy\Tools $tools
 * @var Johncms\Users\User $user
 */

?>
<div class="accordion sidebar__user" id="accordion">
    <div class="border-bottom"></div>
    <a class="nav-link user__link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false">
        <div class="sidebar_user_avatar d-flex align-items-center">
            <div class="position-relative">
                <?php if (! empty($notifications['all'])): ?>
                    <div class="sidebar__notifications badge bg-danger rounded-pill"><?= $notifications['all'] ?></div>
                <?php endif ?>
                <div class="user_photo border rounded-circle me-2 overflow-hidden">
                    <?php /*= $this->avatar($user?->avatar_url, $user?->displayName() ?? d__('system', 'Log In')) */?>
                </div>
            </div>
            <div>
                <?= ($user ? $user->displayName() : d__('system', 'Log In')) ?>
            </div>
        </div>
        <div>
            <svg class="icon icon-chevron-bottom">
                <use xlink:href="<?= asset('icons/sprite.svg') ?>#chevron-bottom"/>
            </svg>
        </div>
    </a>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
        <ul class="nav nav__vertical ps-2 pt-0">
            <!-- Выпадающее меню для пользователей -->
            <?php if ($user): ?>
                <li>
                    <a href="/notifications/">
                        <svg class="icon text-info">
                            <use xlink:href="<?= asset('icons/sprite.svg') ?>#messages"/>
                        </svg>
                        <span class="flex-grow-1 text-info"><?= d__('system', 'Notifications') ?></span>
                        <?php if (! empty($notifications['all'])): ?>
                            <span class="badge bg-danger rounded-pill"><?= $notifications['all'] ?></span>
                            </span>
                        <?php endif ?>
                    </a>
                </li>
                <li>
                    <a href="<?= route('personal.index') ?>">
                        <svg class="icon text-info">
                            <use xlink:href="<?= asset('icons/sprite.svg') ?>#user"/>
                        </svg>
                        <span class="flex-grow-1 text-info"><?= d__('system', 'Personal') ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?= route('logout.index') ?>">
                        <svg class="icon text-info">
                            <use xlink:href="<?= asset('icons/sprite.svg') ?>#log-out"/>
                        </svg>
                        <span class="flex-grow-1 text-info"><?= d__('system', 'Exit') ?></span>
                    </a>
                </li>
            <?php else: ?>
                <!-- Выпадающее меню для гостей -->
                <li>
                    <a href="<?= route('login.index') ?>">
                        <svg class="icon text-info">
                            <use xlink:href="<?= asset('icons/sprite.svg') ?>#log-in"/>
                        </svg>
                        <span class="flex-grow-1 text-info"><?= d__('system', 'Login') ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?= route('registration.index') ?>">
                        <svg class="icon text-info">
                            <use xlink:href="<?= asset('icons/sprite.svg') ?>#users"/>
                        </svg>
                        <span class="flex-grow-1 text-info"><?= d__('system', 'Registration') ?></span>
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
    <div class="border-bottom"></div>
</div>
