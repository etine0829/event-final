<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/tres.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-yellow-dark">
        <div>
            <div class="flex justify-end mt-5 mr-4 px-4 sm:mr-10 sm:px-8">
            {{ $slot }}
            </div>
            <div class="flex justify-center items-center h-screen">                       
                    <div class="relative">
                        <div class="flex flex-col md:flex-row items-center justify-center text-center md:text-left">
                            <div class="w-[150px] h-[150px] md:w-[300px] md:h-[300px]">
                                <img src="{{asset('assets/img/laptop.gif')}}" alt="Laptop GIF" class="w-full h-full object-contain">
                            </div>
                            <h1 class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mt-4 md:mt-0 md:ml-8">
                                Event Tabulation System
                            </h1>
                        </div>
                    </div>
                </div>

        </div>
        
        <!-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0  dark:bg-gray-900">

            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div> -->
            <!-- <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            </div> -->
        <!-- </div> -->
    </body>
</html>
