<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        //只让未登录的用户访问登录页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function create()
    {
        return view('sessions.create');
    }

    /**
     * validate验证可以在任何地方验证
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials,$request->has('remember'))){
            //验证是否已经邮箱激活
            if (Auth::user()->activated) {
                //登陆成功
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', [Auth::user()]);
                //该方法可将页面重定向到上一次请求尝试访问的页面上，并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
            }

        }else{
            //登陆失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

    }

    public function destroy(){
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}