<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AuthPageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitCategoryController;
use App\Http\Controllers\TextToSpeechController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\AIStudioController;
use App\Http\Controllers\VoiceController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\CreateVideoController;
use App\Http\Controllers\StudioController;


use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/', [HomeController::class,'index'])->middleware(
    [

        'XSS',
    ]
);
Route::get('home', [HomeController::class,'index'])->name('home')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard', [HomeController::class,'index'])->name('dashboard')->middleware(
    [

        'XSS',
    ]
);

//-------------------------------User-------------------------------------------

Route::resource('users', UserController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);



Route::get('login/otp', [OTPController::class, 'show'])->name('otp.show')->middleware(
    [

        'XSS',
    ]
);
Route::post('login/otp', [OTPController::class, 'check'])->name('otp.check')->middleware(
    [

        'XSS',
    ]
);
Route::get('login/2fa/disable', [OTPController::class, 'disable'])->name('2fa.disable')->middleware(['XSS',]);

//-------------------------------Subscription-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){

    Route::resource('subscriptions', SubscriptionController::class);
    Route::get('coupons/history', [CouponController::class,'history'])->name('coupons.history');
    Route::delete('coupons/history/{id}/destroy', [CouponController::class,'historyDestroy'])->name('coupons.history.destroy');
    Route::get('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
    Route::resource('coupons', CouponController::class);
    Route::get('subscription/transaction', [SubscriptionController::class,'transaction'])->name('subscription.transaction');
}
);

//-------------------------------Subscription Payment-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){

    Route::post('subscription/{id}/stripe/payment', [SubscriptionController::class,'stripePayment'])->name('subscription.stripe.payment');
}
);
//-------------------------------Settings-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('settings', [SettingController::class,'index'])->name('setting.index');

    Route::post('settings/account', [SettingController::class,'accountData'])->name('setting.account');
    Route::delete('settings/account/delete', [SettingController::class,'accountDelete'])->name('setting.account.delete');
    Route::post('settings/password', [SettingController::class,'passwordData'])->name('setting.password');
    Route::post('settings/general', [SettingController::class,'generalData'])->name('setting.general');
    Route::post('settings/smtp', [SettingController::class,'smtpData'])->name('setting.smtp');
    Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
    Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
    Route::post('settings/payment', [SettingController::class,'paymentData'])->name('setting.payment');
    Route::post('settings/site-seo', [SettingController::class,'siteSEOData'])->name('setting.site.seo');
    Route::post('settings/google-recaptcha', [SettingController::class,'googleRecaptchaData'])->name('setting.google.recaptcha');
    Route::post('settings/company', [SettingController::class,'companyData'])->name('setting.company');
    Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');

    Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
    Route::post('settings/footer', [SettingController::class,'footerData'])->name('setting.footer');

    Route::get('language/{lang}', [SettingController::class,'lanquageChange'])->name('language.change');
    Route::post('theme/settings', [SettingController::class,'themeSettings'])->name('theme.settings');
}
);


//-------------------------------Role & Permissions-------------------------------------------
Route::resource('permission', PermissionController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('role', RoleController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Note-------------------------------------------
Route::resource('note', NoticeBoardController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Contact-------------------------------------------
Route::resource('contact', ContactController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------logged History-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {

    Route::get('logged/history', [UserController::class,'loggedHistory'])->name('logged.history');
    Route::get('logged/{id}/history/show', [UserController::class,'loggedHistoryShow'])->name('logged.history.show');
    Route::delete('logged/{id}/history', [UserController::class,'loggedHistoryDestroy'])->name('logged.history.destroy');
});

//-------------------------------Category-------------------------------------------
Route::resource('visit-category', VisitCategoryController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Visitor-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {

    Route::get('visitor/today', [VisitorController::class,'todayVisitor'])->name('visitor.today');
    Route::get('visitor/pre-register', [VisitorController::class,'visitorPreRegister'])->name('visitor.pre-register');
    Route::delete('visitor/pre-register/{id}', [VisitorController::class,'visitorPreRegisterDestroy'])->name('visitor.pre-register.destroy');
    Route::get('visitor/{id}/pass-print', [VisitorController::class,'visitorPassPrint'])->name('visitor.pass.print');
    Route::resource('visitor', VisitorController::class);
});




//-------------------------------Plan Payment-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::post('subscription/{id}/bank-transfer', [PaymentController::class, 'subscriptionBankTransfer'])->name('subscription.bank.transfer');
    Route::get('subscription/{id}/bank-transfer/action/{status}', [PaymentController::class, 'subscriptionBankTransferAction'])->name('subscription.bank.transfer.action');
    Route::post('subscription/{id}/paypal', [PaymentController::class, 'subscriptionPaypal'])->name('subscription.paypal');
    Route::get('subscription/{id}/paypal/{status}', [PaymentController::class, 'subscriptionPaypalStatus'])->name('subscription.paypal.status');
    Route::post('subscription/{id}/{user_id}/manual-assign-package', [PaymentController::class, 'subscriptionManualAssignPackage'])->name('subscription.manual_assign_package');
    Route::get('subscription/flutterwave/{sid}/{tx_ref}', [PaymentController::class, 'subscriptionFlutterwave'])->name('subscription.flutterwave');
}
);


Route::get('pre-register/{code}', [VisitorController::class,'preRegister'])->name('pre-register');
Route::post('pre-register/{id}/store', [VisitorController::class,'preRegisterStore'])->name('pre-register.store');

//-------------------------------Notification-------------------------------------------
Route::resource('notification', NotificationController::class)->middleware(
    [
        'auth',
        'XSS',

    ]
 );

 Route::get('email-verification/{token}', [VerifyEmailController::class, 'verifyEmail'])->name('email-verification')->middleware(
    [
        'XSS',
    ]
);

//-------------------------------FAQ-------------------------------------------
Route::resource('FAQ', FAQController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Home Page-------------------------------------------
Route::resource('homepage', HomePageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('pages', PageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Auth page-------------------------------------------
Route::resource('authPage', AuthPageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('page/{slug}', [PageController::class, 'page'])->name('page');
//-------------------------------FAQ-------------------------------------------
Route::impersonate();






//Custom Application

//TTS
Route::get('/text-to-speech', [TextToSpeechController::class, 'index'])->name('text_to_speech.index');
Route::post('/text-to-speech/generate', [TextToSpeechController::class, 'generate'])->name('text_to_speech.generate');


//File Manager
Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file_manager.index');
Route::post('/file-manager/upload', [FileManagerController::class, 'store'])->name('file_manager.upload');



Route::post('/file-manager/save-from-tts', [FileManagerController::class, 'saveFromTTS'])
    ->name('file_manager.save_from_tts');

Route::get('/ai-studio', [\App\Http\Controllers\AIStudioController::class, 'index'])->name('ai_studio.index');
Route::post('/ai-studio/generate', [\App\Http\Controllers\AIStudioController::class, 'generate'])->name('ai_studio.generate');


Route::get('/ai-studio/timeline', [AIStudioController::class, 'timeline'])->name('ai_studio.timeline');
Route::get('/voices', [VoiceController::class, 'index'])->name('voices.index');
Route::post('/voices/preview', [VoiceController::class, 'preview'])->name('voices.preview');
Route::post('/voices/rename', [VoiceController::class, 'rename'])->name('voices.rename');
Route::post('/voices/favorite', [VoiceController::class, 'favorite'])->name('voices.favorite');


Route::get('/avatar', [AvatarController::class, 'index'])->name('avatar.index');
Route::get('/avatar/create', [AvatarController::class, 'create'])->name('avatar.create');
Route::get('/avatar/{avatar}', [AvatarController::class, 'show'])->name('avatar.show');
Route::get('/avatar/{avatar}/edit', [AvatarController::class, 'edit'])->name('avatar.edit');
Route::get('/avatar/purchase', [AvatarController::class, 'purchase'])->name('avatar.purchase');


Route::prefix('videos')->group(function () {
    Route::get('/create', [CreateVideoController::class, 'index'])->name('videos.index');

    // This is the route your modal "Create portrait video" and "Create landscape video" buttons will hit
    Route::get('/start', [CreateVideoController::class, 'start'])->name('videos.create');

    // Other menu items from the modal
    Route::get('/translate', [CreateVideoController::class, 'translate'])->name('videos.translate');
    Route::get('/templates', [CreateVideoController::class, 'templates'])->name('videos.templates');
    Route::get('/ppt', [CreateVideoController::class, 'ppt'])->name('videos.ppt');
    Route::get('/script', [CreateVideoController::class, 'script'])->name('videos.script');
    Route::get('/pdf2video', [CreateVideoController::class, 'pdf2video'])->name('videos.pdf2video');
});




Route::get('/studio', [StudioController::class, 'index'])->name('studio.index');
Route::post('/studio/project/save', [StudioController::class, 'save'])->name('studio.save');
Route::post('/studio/scene/upload', [StudioController::class, 'upload'])->name('studio.upload'); // bg video / avatar image
Route::post('/studio/export', [StudioController::class, 'export'])->name('studio.export'); // stub

Route::prefix('tts')->group(function () {
    Route::get('/voices', [VoiceController::class, 'index'])->name('voices.index');
    Route::get('/voices/sync', [VoiceController::class, 'sync'])->name('voices.sync');
});
