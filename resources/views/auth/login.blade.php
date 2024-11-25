
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{ open: false }">
         <!-- login click -->
         <a @click="open = true" class="font-bold tracking-wider cursor-pointer bg-blue-500 text-white text-sm px-4 py-3 rounded hover:bg-blue-700">
            LOGIN
         </a>

        <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">  
            <div  class="w-[90%] sm:w-[70%] md:w-[50%] lg:w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto">    
                <div class="flex items-start pb-3">
                    <img src="{{ asset('assets/img/login.png') }}" alt="login" class="w-[80px] h-[80px] sm:w-[100px] sm:h-[100px] mx-auto">
                    <a @click="open = false" class="flex justify-end cursor-pointer text-black text-sm py-2 rounded hover:text-red-500">X</a>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative">
                            <x-text-input 
                                id="password" 
                                class="block mt-1 w-full pr-10" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password" 
                            />
                            <!-- Button to toggle password visibility -->
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <!-- Eye icon for Show password -->
                                <i id="togglePasswordIcon" class="fa fa-eye"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>


                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        

                        <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-400">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        // Check the current type of the password input field
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text'; // Show password
            toggleIcon.classList.remove('fa-eye-slash'); // Remove eye icon
            toggleIcon.classList.add('fa-eye'); // Add eye-slash icon
        } else {
            passwordInput.type = 'password'; // Hide password
            toggleIcon.classList.remove('fa-eye'); // Remove eye icon
            toggleIcon.classList.add('fa-eye-slash'); // Add eye-slash icon
        }
    }
</script>