<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function create(){
        return view('users.create');
    }

    public function show(User $user){
        return view('users.show',compact('user'));
    }

    /**
     * required是否空
     * unique唯一性：模型表
     * max、min 最大、最小
     * confirmed验证两次密码是否一致
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required|unique:users|max:50',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
    }

    /**
     * 利用了 Laravel 的『隐性路由模型绑定』功能，直接读取对应 ID 的用户实例 $user，未找到则报错；
     * 将查找到的用户实例 $user 与编辑视图进行绑定；
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user);
    }

}
