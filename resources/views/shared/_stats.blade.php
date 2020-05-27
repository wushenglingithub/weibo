<a href="#">
    <strong id="following" class="stat">
        {{ count($user->followings) }}
    </strong>
    关注
</a>
<a href="#">
    <strong id="followers" class="stat">
        {{ count($user->followers) }}
    </strong>
    粉丝
</a>
<a href="#">
    <strong id="statuses" class="stat">
        {{--最佳实践应采用的方法是在数据库中添加一个模型计数器字段，
        在每次对模型进行创建或删除时对该字段进行更新--}}
        {{ $user->statuses()->count() }}
    </strong>
    微博
</a>