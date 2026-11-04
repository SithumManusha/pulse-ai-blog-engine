<?php
$pageTitle = "Explore Articles | " . ($appName ?? 'Pulse AI Blog');
require __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Hero / Announcement Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-900 via-indigo-900 to-purple-950 text-white p-8 sm:p-12 mb-12 shadow-2xl">
        <div class="absolute -right-10 -bottom-10 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-300 border border-brand-500/30 mb-4">
                <i class="fa-solid fa-sparkles"></i> Powered by Google Gemini AI
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-4">
                Ideas that shape the future of tech.
            </h1>
            <p class="text-gray-300 text-base sm:text-lg mb-8 leading-relaxed">
                Discover deep engineering insights, AI breakdowns, and architectural tutorials with real-time AI summaries and writing assistance.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="?route=posts.create" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-brand-900 font-bold hover:bg-gray-100 shadow-lg shadow-black/20 transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-pen-nib"></i> Start Writing Now
                </a>
                <a href="#articles" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 backdrop-blur border border-white/10 transition-all">
                    Explore Articles <i class="fa-solid fa-arrow-down text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Category Filters and Controls -->
    <div id="articles" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        
        <!-- Category Pill Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="?route=home" 
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap <?= empty($_GET['category']) ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' ?>">
                All Topics
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="?route=home&category=<?= urlencode($cat['slug']) ?>" 
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap <?= ($_GET['category'] ?? '') === $cat['slug'] ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search Result Counter (if searched) -->
        <?php if (!empty($_GET['q'])): ?>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Search results for: <strong class="text-gray-900 dark:text-white">"<?= htmlspecialchars($_GET['q']) ?>"</strong></span>
                <a href="?route=home" class="text-xs text-brand-600 hover:underline">Clear</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Posts Grid -->
    <?php if (empty($posts)): ?>
        <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 my-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                <i class="fa-solid fa-newspaper text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No articles found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Be the first to publish a groundbreaking story!</p>
            <a href="?route=posts.create" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-500 shadow-md">
                <i class="fa-solid fa-plus"></i> Write an Article
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
                <article class="flex flex-col bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    <!-- Cover Image -->
                    <a href="?route=posts.show&slug=<?= urlencode($post['slug']) ?>" class="block relative aspect-video overflow-hidden bg-gray-100 dark:bg-gray-800">
                        <img src="<?= htmlspecialchars($post['cover_image']) ?>" 
                            alt="<?= htmlspecialchars($post['title']) ?>" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy">
                        
                        <?php if (!empty($post['category_name'])): ?>
                            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-bold rounded-lg bg-black/60 backdrop-blur-md text-white">
                                <?= htmlspecialchars($post['category_name']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($post['ai_summary'])): ?>
                            <span class="absolute bottom-3 right-3 px-2 py-0.5 text-[11px] font-bold rounded-md bg-brand-600/90 backdrop-blur-md text-white shadow-sm flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i> AI Summary
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Card Body -->
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <!-- Meta: Reading Time & Views -->
                            <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mb-3 font-medium">
                                <span><i class="fa-regular fa-clock mr-1"></i><?= (int)$post['reading_time'] ?> min read</span>
                                <span>•</span>
                                <span><i class="fa-regular fa-eye mr-1"></i><?= (int)$post['views'] ?> views</span>
                            </div>

                            <!-- Post Title -->
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors line-clamp-2 mb-3">
                                <a href="?route=posts.show&slug=<?= urlencode($post['slug']) ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed mb-4">
                                <?= htmlspecialchars($post['excerpt'] ?? '') ?>
                            </p>
                        </div>

                        <!-- Footer: Author & AI Tags -->
                        <div>
                            <?php if (!empty($post['ai_tags'])): ?>
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <?php 
                                    $tags = explode(',', $post['ai_tags']);
                                    foreach (array_slice($tags, 0, 3) as $tag): 
                                    ?>
                                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                            #<?= htmlspecialchars(trim($tag)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <?php 
                                    $authorAvatar = (!empty($post['author_avatar']) && !str_contains($post['author_avatar'], 'unsplash') && !str_contains($post['author_avatar'], 'photo-') && !str_contains($post['author_avatar'], 'pravatar')) 
                                        ? $post['author_avatar'] 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=4f46e5&color=fff&bold=true';
                                    ?>
                                    <img src="<?= htmlspecialchars($authorAvatar) ?>" alt="Author" class="w-7 h-7 rounded-full object-cover">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300"><?= htmlspecialchars($post['author_name']) ?></span>
                                </div>
                                <span class="text-xs text-gray-400"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                            </div>
                        </div>

                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center items-center gap-2 mt-12">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?route=home&page=<?= $i ?><?= !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '' ?><?= !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>" 
                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-all <?= $i === $currentPage ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
