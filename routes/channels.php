<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    
    \Log::info('Broadcast channel auth attempt', [
        'user_id' => $user->id,
        'user_role' => $user->role,
        'order_id' => $orderId,
        'order_exists' => $order !== null,
        'order_student_profile_id' => $order?->student_profile_id,
        'order_client_profile_id' => $order?->client_profile_id,
        'user_student_profile_id' => $user->studentProfile?->id,
        'user_client_profile_id' => $user->clientProfile?->id,
    ]);
    
    if (!$order) {
        \Log::error('Order not found for broadcasting auth', ['order_id' => $orderId]);
        return false;
    }
    
    // Check if user is either the student or client for this order
    if ($user->role === 'student') {
        $userStudentProfileId = $user->studentProfile?->id;
        $authorized = $userStudentProfileId && $order->student_profile_id === $userStudentProfileId;
        \Log::info('Student auth result', ['authorized' => $authorized]);
        return $authorized;
    }
    
    if ($user->role === 'client') {
        $userClientProfileId = $user->clientProfile?->id;
        $authorized = $userClientProfileId && $order->client_profile_id === $userClientProfileId;
        \Log::info('Client auth result', ['authorized' => $authorized]);
        return $authorized;
    }
    
    \Log::warning('User role not recognized', ['role' => $user->role]);
    return false;
});
