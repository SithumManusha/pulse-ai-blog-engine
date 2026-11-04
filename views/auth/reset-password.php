<?php
use App\Helpers\Csrf;
$pageTitle = "Set New Password | " . ($appName ?? 'Pulse AI Blog');
require __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center text-white mx-auto mb-4 shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-lock-open text-xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Choose New Password</h2>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Enter your new password below to securely regain access to your account.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-semibold space-y-3 border border-red-200 dark:border-red-900/50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <div class="pt-2 border-t border-red-200 dark:border-red-900/50">
                    <a href="?route=forgot-password" class="inline-flex items-center text-xs font-bold text-red-700 dark:text-red-300 hover:underline">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i> Request a fresh reset link
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($error) || !str_contains($error, 'expired')): ?>
            <form class="mt-8 space-y-5" action="?route=reset-password.post" method="POST">
                <?= Csrf::getField() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        New Password (min 6 characters)
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password" required minlength="6" autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-check-double absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password_confirmation" required minlength="6" autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="w-full py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 shadow-md shadow-brand-500/25 transition-all">
                        Update Password & Sign In
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <div class="text-center text-xs text-gray-500 pt-2">
            <a href="?route=login" class="font-bold text-brand-600 dark:text-brand-400 hover:underline">
                Back to Sign In
            </a>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
