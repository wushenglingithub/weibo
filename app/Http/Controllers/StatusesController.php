<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        session()->flash('success', '发布成功！');
        return redirect()->back();
    }

    /**
     * @param Status $status
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Status $status)
    {
        //自动查找并注入对应 ID 的实例对象 $status，
        //如果找不到就会抛出异常
        $this->authorize('destroy', $status);
        //删除授权的检测，不通过会抛出 403 异常
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}