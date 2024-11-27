<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WishList;
use Illuminate\Auth\Access\Response;

class WishListPolicy
{
   public function modify(User $user,WishList $wishList){
    return $user->id == $wishList->user_id ?
    Response::allow() :
    Response::deny('you dont own this wishlist');
   }
}
