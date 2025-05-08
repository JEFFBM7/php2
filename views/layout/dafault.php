<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/Router.php';
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <title><?= $title ?></title>
</head>

<body class="font-sans bg-gray-10 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors ">
    <header>
        <nav class="bg-white dark:bg-gray-800 shadow rounded-b-[30px]">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <a href="/" class="flex items-center mr-0">
                    <img src="/images/logo1.png" alt="TrucsPasChers Logo" class="h-11 w-auto"/> 
                </a>
              
                <div class="hidden md:flex items-center">
                    <div class="flex space-x-6">
                        <?php foreach ($navItems as $path => $label): ?>
                            <?php if ($path === '/login') continue; ?>
                            <a href="<?= $path ?>" class="text-gray-700 dark:text-gray-200 hover:text-primary transition-colors duration-300 <?= $currentPath === $path ? 'font-semibold' : '' ?> uppercase"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="ml-6">
                        <a href="/login" class="text-gray-700 dark:text-gray-200 hover:text-primary transition-colors duration-300 <?= $currentPath === '/login' ? 'font-semibold' : '' ?> uppercase"><?= $navItems['/login'] ?></a>
                    </div>
                </div>
                <button id="menu-btn" class="md:hidden text-gray-700 dark:text-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-800">
                <div class="px-4 pt-2 pb-4 space-y-1">
                    <?php foreach ($navItems as $path => $label): ?>
                        <a
                            href="<?= $path ?>"
                            class="block text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 px-3 py-2 rounded transition-colors duration-300 <?= $currentPath === $path ? 'font-semibold bg-gray-200 dark:bg-gray-700' : '' ?> uppercase"
                        >
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
                const btn = document.getElementById('menu-btn');
                const menu = document.getElementById('mobile-menu');
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            </script>
        </nav>
        <br>
    </header>
    <?= $content ?>

    <br>

    <footer class="bg-gray-100 dark:bg-gray-800 mt-12 rounded-t-[30px]">
        <div class="w-full max-w-screen-xl mx-auto p-6 md:py-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <a href="/" class="flex items-center mb-4 md:mb-0 space-x-2 rtl:space-x-reverse">
                    <span class="self-center text-2xl font-bold text-primary dark:text-primary-light">TrucsPasChers</span>
                </a>
                <ul class="flex space-x-6 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="/about" class="hover:text-primary transition-colors">About</a></li>
                    <li><a href="/contact" class="hover:text-primary transition-colors">Contact</a></li>
                </ul>
            </div>
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                © 2025 TrucsPasChers. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>

</html>