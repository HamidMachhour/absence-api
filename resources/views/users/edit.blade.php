<x-layout>
    <x-card class="p-10 max-w-lg mx-auto text-right">
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">تغيير الحساب الشخصي</h2>
        </header>

        <form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6 ">
                <label for="firstName" class="inline-block text-lg  mb-2"> الاسم العائلي </label>
                <input type="text" class="text-right border border-gray-200 rounded p-2 w-full" name="firstName"
                    value="{{ $user->firstName }}" id="firstName" />

                @error('firstName')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6 ">
                <label for="lastName" class="inline-block text-lg mb-2"> الاسم الشخصي </label>
                <input type="text" class="text-right border border-gray-200 rounded p-2 w-full" name="lastName"
                    value="{{ $user->lastName }}" id="lastName" />

                @error('lastName')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <input type="checkbox" id="change_password" name="change_password" class="mr-2" />
                <label for="change_password">تغيير القن السري</label>
            </div>
            <div class="password-fields mb-6" style="display: none;">
                <div class="mb-6">
                    <label for="old_password" class="inline-block text-lg mb-2">
                        القن السري القديم
                    </label>
                    <input type="password" class="border border-gray-200 rounded p-2 w-full" name="old_password"
                        value="{{ old('old_password') }}" />

                    @error('old_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 ">
                    <label for="password" class="inline-block text-lg mb-2">
                        القن السري الجديد
                    </label>
                    <input type="password" class="border border-gray-200 rounded p-2 w-full" name="password"
                        value="{{ old('password') }}" />

                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 ">
                    <label for="password_confirmation" class="inline-block text-lg mb-2">
                        تاكيد القن السري
                    </label>
                    <input type="password" class="border border-gray-200 rounded p-2 w-full" name="password_confirmation"
                        value="{{ old('password_confirmation') }}" />

                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6 text-left">
                <button class="bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700">
                    حفظ
                </button>
                <a href="javascript:history.back()" class="text-orange-600 hover:text-green-700">رجوع</a>
            </div>
        </form>
    </x-card>

    <script>
        const checkbox = document.getElementById('change_password');
        const passwordFields = document.querySelector('.password-fields');

        checkbox.addEventListener('change', function() {
            passwordFields.style.display = this.checked ? 'block' : 'none';
        });
    </script>
</x-layout>
