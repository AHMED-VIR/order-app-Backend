<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    public function create(User $user){
        return true;
    }

    public function modify(User $user,Cart $cart){
        return $user->id == $cart->user_id ?
        Response::allow():
        Response::deny('you dont own this cart');
    }

}
