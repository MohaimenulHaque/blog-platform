<?php

use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/blog',                             [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}',                      [BlogController::class, 'show'])->name('blog.show');

Route::get('/categories',                       [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{category:slug}',         [CategoryController::class, 'show'])->name('categories.show');

Route::get('/tags',                             [TagController::class, 'index'])->name('tags.index');
Route::get('/tag/{tag:slug}',                   [TagController::class, 'show'])->name('tags.show');

Route::get('/authors',                          [AuthorController::class, 'index'])->name('authors.index');
Route::get('/author/{username}',                [AuthorController::class, 'show'])->name('authors.show');

Route::get('/contact',                          [ContactController::class, 'show'])->name('contact');

Route::view('/about', 'pages.about')->name('about');
Route::get('/search', SearchController::class)->name('search');


Route::get('/newsletter/unsubscribe/{token}',   [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}',  [NewsletterController::class, 'unsubscribeConfirm'])->name('newsletter.unsubscribe.confirm');
Route::post('/newsletter/subscribe',            [NewsletterController::class, 'subscribe'])->middleware('throttle:5,10')->name('newsletter.subscribe');

Route::post('/contact',                         [ContactController::class, 'submit'])->middleware('throttle:10,1')->name('contact.submit');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::post('/posts/{post}/like',           [PostLikeController::class, 'toggle'])->middleware('throttle:60,1')->name('posts.like');
    Route::post('/posts/{post}/bookmark',       [BookmarkController::class, 'toggle'])->middleware('throttle:60,1')->name('posts.bookmark');
    Route::post('/posts/{post}/comments',       [CommentController::class, 'store'])->middleware('throttle:20,1')->name('posts.comments.store');
    Route::patch('/comments/{comment}',         [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}',        [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/like',     [CommentLikeController::class, 'toggle'])->middleware('throttle:60,1')->name('comments.like');

    Route::get('/bookmarks',                    [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::get('/notifications',                [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',      [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/profile',                      [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',                    [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',                   [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/**************************************************
 * Admin Controller Start Here
 **************************************************/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/',     [DashboardController::class, 'index'])->name('admin.dashboard');
});


/*--------------
 * AUTHOR
 --------------*/

Route::middleware(['auth', 'verified', 'author'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('posts/trashed',                 [AdminPostController::class, 'trashed'])->name('posts.trashed');
    Route::patch('posts/{post}/restore',        [AdminPostController::class, 'restore'])->withTrashed()->name('posts.restore');
    Route::post('posts/{post}/publish',         [AdminPostController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/unpublish',       [AdminPostController::class, 'unpublish'])->name('posts.unpublish');
    Route::post('posts/{post}/schedule',        [AdminPostController::class, 'schedule'])->name('posts.schedule');
    Route::post('posts/{post}/archive',         [AdminPostController::class, 'archive'])->name('posts.archive');
    Route::post('posts/{post}/draft',           [AdminPostController::class, 'draft'])->name('posts.draft');
    Route::resource('posts', AdminPostController::class);
});


/*--------------
 * EDITOR 
 --------------*/

Route::middleware(['auth', 'verified', 'editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('categories/{category}/restore',   [AdminCategoryController::class, 'restore'])->withTrashed()->name('categories.restore');
    Route::resource('categories', AdminCategoryController::class);
    Route::patch('tags/{tag}/restore',              [AdminTagController::class, 'restore'])->withTrashed()->name('tags.restore');
    Route::resource('tags', AdminTagController::class);
    Route::post('uploads',                          [ImageUploadController::class, 'store'])->name('uploads.store');
    Route::delete('uploads',                        [ImageUploadController::class, 'destroy'])->name('uploads.destroy');

    Route::get('comments',                          [AdminCommentController::class, 'index'])->name('comments.index');
    Route::get('comments/trashed',                  [AdminCommentController::class, 'trashed'])->name('comments.trashed');
    Route::patch('comments/{comment}/restore',      [AdminCommentController::class, 'restore'])->withTrashed()->name('comments.restore');
    Route::delete('comments/{comment}/force',       [AdminCommentController::class, 'forceDestroy'])->withTrashed()->name('comments.force-destroy');
    Route::patch('comments/{comment}/status',       [AdminCommentController::class, 'status'])->name('comments.status');
    Route::delete('comments/{comment}',             [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});


/*--------------
 * ADMIN
 --------------*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('authors',                           [AdminAuthorController::class, 'index'])->name('authors.index');
    Route::get('authors/create',                    [AdminAuthorController::class, 'create'])->name('authors.create');
    Route::post('authors',                          [AdminAuthorController::class, 'store'])->name('authors.store');
    Route::get('authors/{user}/edit',               [AdminAuthorController::class, 'edit'])->name('authors.edit');
    Route::patch('authors/{user}',                  [AdminAuthorController::class, 'update'])->name('authors.update');
    Route::delete('authors/{user}',                 [AdminAuthorController::class, 'destroy'])->name('authors.destroy');

    Route::get('newsletter',                        [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('newsletter/export',                 [AdminNewsletterController::class, 'export'])->name('newsletter.export');
    Route::delete('newsletter/{subscriber}',        [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users',                             [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit',                 [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}',                    [AdminUserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/activate',            [AdminUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate',          [AdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::delete('users/{user}',                   [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('media',                             [MediaController::class, 'index'])->name('media.index');
    Route::post('media',                            [MediaController::class, 'store'])->name('media.store');
    Route::patch('media/{media}',                   [MediaController::class, 'update'])->name('media.update');
    Route::delete('media/{media}',                  [MediaController::class, 'destroy'])->name('media.destroy');

    Route::get('settings',                          [SettingController::class, 'index'])->name('settings.index');
    Route::patch('settings',                        [SettingController::class, 'update'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile',                          [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',                        [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',                       [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
