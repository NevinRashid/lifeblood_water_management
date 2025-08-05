<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Jobs\SendPeriodicWaterReportJob;
use Modules\UsersAndTeams\Http\Controllers\Api\VerificationController;
use Modules\UsersAndTeams\Http\Controllers\Api\ResetPasswordController;
use Modules\UsersAndTeams\Http\Controllers\Api\ForgotPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
    ->middleware(['auth:sanctum'])
    ->name('verification.send');



Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');


Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.reset');

Route::middleware('auth:sanctum')->post('/send-water-report', function () {
    $user = Auth::user();

    SendPeriodicWaterReportJob::dispatch($user);

    return response()->json([
        'message' => '✅ تم جدولة إرسال التقرير للمستخدم الحالي.',
    ]);
});
