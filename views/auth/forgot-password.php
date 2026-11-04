<?php
use App\Helpers\Csrf;
$pageTitle = "Forgot Password | " . ($appName ?? 'Pulse AI Blog');
require __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center text-white mx-auto mb-4 shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Reset Password</h2>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Enter your registered email address to receive a secure, 15-minute one-time password reset link.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-semibold flex items-center gap-2 border border-red-200 dark:border-red-900/50">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold space-y-3 border border-emerald-200 dark:border-emerald-900/50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
                
                <?php if (!empty($resetLink)): ?>
                    <div class="pt-2 border-t border-emerald-200 dark:border-emerald-900/50">
                        <p class="text-[11px] text-emerald-800 dark:text-emerald-300 mb-2">
                            <strong>Instant Demo Verification:</strong> Click below to proceed with resetting your password:
                        </p>
                        <a href="<?= htmlspecialchars($resetLink) ?>" 
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition-all">
                            <i class="fa-solid fa-shield-halved mr-1.5"></i> Click Here to Reset Password Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-5" action="?route=forgot-password.post" method="POST">
            <?= Csrf::getField() ?>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                    Your Registered Email
                </label>
                <div class="relative">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="email" name="email" required autocomplete="email"
                        placeholder="you@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 shadow-md shadow-brand-500/25 transition-all">
                    Generate Reset Link
                </button>
            </div>

            <div class="text-center text-xs text-gray-500">
                Remembered your password? 
                <a href="?route=login" class="font-bold text-brand-600 dark:text-brand-400 hover:underline">
                    Back to Sign In
                </a>
            </div>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
