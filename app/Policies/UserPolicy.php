<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * 删除用户动作相关的授权。
     * 如果是管理员且删除id不是自己（不能自己删自己）
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function destroy(User $currentUser,User $user){
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }

    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
