<div class="list-group-item">
    {{--头像--}}
    <img class="mr-3" src="{{ $user->gravatar() }}" alt="{{ $user->name }}" width=32>
    {{--名称--}}
    <a href="{{ route('users.show', $user) }}">
        {{ $user->name }}
    </a>
</div>