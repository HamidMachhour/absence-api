@props(['user_roles'])


<ul class="flex flex-wrap items-center text-gray-600 text-xs">
    @foreach ($user_roles as $user_role)
        <li
            class="bg-gray-200 hover:opacity-80 rounded-xl p-1 m-1">
            <a href="{{request()->fullUrlWithQuery(['roleId' => $user_role->roleId,'role_name' => $user_role->roleName(),'page'=>1]) }}">{{ '#'.$user_role->roleName() }}</a>
        </li>
    @endforeach
</ul>
