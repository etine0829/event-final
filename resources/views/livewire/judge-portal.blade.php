<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Profile In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="">
<div class="flex">
    <!-- Sidebar for categories -->
    <div class="w-1/4 p-4 bg-gray-200">
        <h3>Categories</h3>
        <ul>
            @foreach($categories as $category)
                <li class="cursor-pointer" wire:click="selectCategory({{ $category->id }})">
                    {{ $category->name }}
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Main content for participants and criteria -->
    <div class="w-3/4 p-4">
        @if ($selectedCategory)
            <h2>{{ $selectedCategory->name }}</h2>
            @foreach ($selectedCategory->participants as $participant)
                <div class="flex mb-4 p-4 bg-gray-100">
                    <!-- Placeholder for participant image -->
                    <div class="w-1/4 bg-red-300 h-32"></div>
                    
                    <!-- Participant details -->
                    <div class="w-3/4 pl-4">
                        <h3>Participant No. {{ $participant->id }}</h3>
                        <p>{{ $participant->location }}</p>
                        
                        <!-- Criteria scores input -->
                        <div>
                            @foreach ($selectedCategory->criteria as $criterion)
                                <div class="flex items-center my-2">
                                    <label class="w-1/2">{{ $criterion->name }} ({{ $criterion->weight }}%)</label>
                                    <input type="number" class="w-1/2 border p-1" placeholder="Score">
                                </div>
                            @endforeach
                        </div>

                        <!-- Comment box -->
                        <textarea placeholder="Comment" class="w-full border p-1 mt-2"></textarea>
                    </div>
                </div>
            @endforeach
        @else
            <p>Select a category to view participants and criteria.</p>
        @endif
    </div>
</div>
</body>




