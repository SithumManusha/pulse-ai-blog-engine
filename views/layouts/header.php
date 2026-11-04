<?php
use App\Helpers\Auth;
use App\Helpers\Env;
$appName = Env::get('APP_NAME', 'Pulse AI Blog');
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $appName, ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- Tailwind CSS with Typography plugin -->
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
        .dark .glass { background: rgba(17, 24, 39, 0.85); backdrop-filter: blur(12px); }
    </style>
    
    <script>
        // Inline theme initialization to prevent flash
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-200">

    <!-- Global Toast Container -->
    <div id="toast-container" class="fixed top-20 right-5 z-50 flex flex-col gap-3"></div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 glass border-b border-gray-200 dark:border-gray-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="?route=home" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-gray-900 via-brand-600 to-indigo-600 dark:from-white dark:via-brand-400 dark:to-indigo-300">
                            <?= htmlspecialchars($appName) ?>
                        </span>
                    </a>
                </div>

                <!-- Center Search Form -->
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <form action="index.php" method="GET" class="w-full relative">
                        <input type="hidden" name="route" value="home">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                                placeholder="Search articles, topics or AI tags..." 
                                class="w-full pl-10 pr-4 py-2 text-sm rounded-full bg-gray-100 dark:bg-gray-800/80 border-transparent focus:border-brand-500 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-gray-900 dark:text-gray-100">
                        </div>
                    </form>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    
                    <!-- Dark Mode Toggle Button -->
                    <button id="theme-toggle" type="button" aria-label="Toggle theme" 
                        class="p-2.5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors">
                        <i id="theme-icon-sun" class="fa-solid fa-sun text-lg hidden dark:block text-amber-400"></i>
                        <i id="theme-icon-moon" class="fa-solid fa-moon text-lg block dark:hidden text-indigo-600"></i>
                    </button>

                    <?php if ($currentUser): ?>
                        <!-- Write Blog Button with AI badge -->
                        <a href="?route=posts.create" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 shadow-md shadow-brand-500/25 transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-feather-pointed"></i>
                            <span class="hidden sm:inline">Write Post</span>
                            <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded-full uppercase tracking-wider font-bold">AI</span>
                        </a>

                        <!-- User Profile Dropdown Button -->
                        <div class="relative group">
                            <?php 
                            $avatarUrl = (!empty($currentUser['avatar']) && !str_contains($currentUser['avatar'], 'unsplash') && !str_contains($currentUser['avatar'], 'photo-') && !str_contains($currentUser['avatar'], 'pravatar')) 
                                ? $currentUser['avatar'] 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['username']) . '&background=4f46e5&color=fff&bold=true';
                            ?>
                            <button class="flex items-center gap-2 p-1 rounded-full ring-2 ring-transparent group-hover:ring-brand-500/30 transition-all">
                                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                                <span class="hidden lg:inline text-sm font-semibold text-gray-700 dark:text-gray-300"><?= htmlspecialchars($currentUser['username']) ?></span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 py-2 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-800 opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-all transform origin-top-right">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($currentUser['email']) ?></p>
                                    <span class="inline-block mt-1 text-[10px] uppercase font-bold px-2 py-0.5 bg-brand-50 dark:bg-brand-950 text-brand-600 dark:text-brand-400 rounded-md"><?= htmlspecialchars($currentUser['role']) ?></span>
                                </div>
                                <a href="?route=posts.create" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">New Article</a>
                                <div class="border-t border-gray-100 dark:border-gray-800 my-1"></div>
                                <a href="?route=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 font-medium">Log out</a>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="?route=login" class="px-3.5 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            Sign In
                        </a>
                        <a href="?route=register" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-all shadow-sm">
                            Get Started
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="flex-grow">
