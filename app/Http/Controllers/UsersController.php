<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;

class UsersController extends Controller
{
    /**
     * 中间件路径：app/Http/Controllers/UsersController.php
     * 过滤未登录用户 edit,update
     * UsersController constructor.
     */
    public function __construct()
    {
        /**
         * 中间件
         */
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','index', 'confirmEmail']
        ]);
        //只让未登录的用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function create(){
        return view('users.create');
    }

    public function show(User $user){
        $statuses = $user->statuses()
            ->orderBy('created_at')
            ->paginate(10);
        return view('users.show',compact('user','statuses'));
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('./');

//        Auth::login($user);
//        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('users.show',[$user]);
    }

    /**
     * 利用了 Laravel 的『隐性路由模型绑定』功能，直接读取对应 ID 的用户实例 $user，未找到则报错；
     * 将查找到的用户实例 $user 与编辑视图进行绑定；
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        //authorize 方法，它可以被用于快速授权一个指定的行为，
        //当无权限运行该行为时会抛出 HttpException。
        //authorize 方法接收两个参数，
        //第一个为授权策略的名称，第二个为进行授权验证的数据。
        $this->authorize('update', $user);
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
        $this->authorize('update', $user);
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

    public function index(){
        //模型全取，影响性能，待优化
//        $users = User::all();
        //优化如下,一次查询10条数据
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    public function destroy(User $user){
//        删除授权策略
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token)
    {
        //firstOrFail
        //查询一个用户，查询不到返回404
        $user = User::where('activation_token', $token)->firstOrFail();

        //改变用户激活状态，清空激活，防止重用
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
