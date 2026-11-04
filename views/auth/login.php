<?php
use App\Helpers\Csrf;
$pageTitle = "Sign In | " . ($appName ?? 'Pulse AI Blog');
require __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center text-white mx-auto mb-4 shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-lock text-xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Welcome back</h2>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Sign in to your account to create and manage your blogs.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-semibold flex items-center gap-2 border border-red-200 dark:border-red-900/50">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (($_GET['msg'] ?? '') === 'password_reset'): ?>
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-semibold flex items-center gap-2 border border-emerald-200 dark:border-emerald-900/50">
                <i class="fa-solid fa-circle-check"></i> Your password has been successfully updated! You can now sign in with your new password.
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-5" action="?route=login.post" method="POST">
            <?= Csrf::getField() ?>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                    Email Address
                </label>
                <div class="relative">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="email" name="email" required autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <a href="?route=forgot-password" class="text-xs font-medium text-brand-600 dark:text-brand-400 hover:underline">
                        Forgot password?
                    </a>
                </div>
                <div class="relative">
                    <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 shadow-md shadow-brand-500/25 transition-all">
                    Sign In
                </button>
            </div>

            <div class="text-center text-xs text-gray-500">
                Don't have an account? 
                <a href="?route=register" class="font-bold text-brand-600 dark:text-brand-400 hover:underline">Create an account</a>
            </div>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
