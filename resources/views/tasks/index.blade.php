<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestionnaire de Projets et Tâches
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Fonctionnalité 10 : Tableau de bord statistique -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-sm text-gray-500">Total Tâches</p>
                <p class="text-2xl font-bold">{{ $totalTasks }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-sm text-gray-500">Terminées</p>
                <p class="text-2xl font-bold text-green-600">{{ $completedTasks }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-sm text-gray-500">En cours</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingTasks }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-sm text-gray-500">Total Catégories</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalCategories }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Colonne Gauche : Gestion des Catégories -->
            <div class="bg-white p-6 shadow rounded-lg space-y-4">
                <h3 class="text-lg font-bold">Catégories (Fonctionnalités 1, 2, 3)</h3>
                <form action="{{ route('categories.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nouvelle catégorie..." required class="border rounded px-3 py-2 w-full">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Ajouter</button>
                </form>

                <ul class="divide-y">
                    @foreach($categories as $category)
                        <li class="py-2 flex justify-between items-center">
                            <span>{{ $category->name }}</span>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm">Supprimer</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Colonne Droite : Création et Liste des Tâches -->
            <div class="md:col-span-2 space-y-6">
                <!-- Fonctionnalité 4 : Créer une tâche -->
                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Nouvelle Tâche (Fonctionnalité 4)</h3>
                    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="title" placeholder="Titre de la tâche" required class="border rounded px-3 py-2 w-full">
                            <select name="category_id" required class="border rounded px-3 py-2 w-full">
                                <option value="">-- Choisir une catégorie --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <textarea name="description" placeholder="Description (optionnelle)" class="border rounded px-3 py-2 w-full"></textarea>
                        <div class="flex justify-between items-center">
                            <input type="date" name="due_date" class="border rounded px-3 py-2">
                            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Créer la tâche</button>
                        </div>
                    </form>
                </div>

                <!-- Fonctionnalité 9 : Recherche et Filtre -->
                <div class="bg-white p-6 shadow rounded-lg">
                    <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-wrap gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une tâche..." class="border rounded px-3 py-2 flex-1">
                        <select name="category_id" class="border rounded px-3 py-2">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filtrer</button>
                        <a href="{{ route('tasks.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Réinitialiser</a>
                    </form>
                </div>

                <!-- Fonctionnalités 5, 6, 7, 8 : Liste des Tâches et Actions -->
                <div class="bg-white p-6 shadow rounded-lg space-y-4">
                    <h3 class="text-lg font-bold">Liste des Tâches</h3>
                    @forelse($tasks as $task)
                        <div class="border p-4 rounded flex justify-between items-center {{ $task->is_completed ? 'bg-gray-50' : '' }}">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold {{ $task->is_completed ? 'line-through text-gray-500' : '' }}">
                                        {{ $task->title }}
                                    </span>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        {{ $task->category->name }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $task->description }}</p>
                                @if($task->due_date)
                                    <p class="text-xs text-gray-400">Échéance : {{ $task->due_date }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Basculer le statut -->
                                <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-3 py-1 rounded {{ $task->is_completed ? 'bg-yellow-500 text-white' : 'bg-green-600 text-white' }}">
                                        {{ $task->is_completed ? 'En cours' : 'Terminer' }}
                                    </button>
                                </form>

                                <!-- Modifier -->
                                <a href="{{ route('tasks.edit', $task) }}" class="text-xs bg-blue-600 text-white px-3 py-1 rounded">Modifier</a>

                                <!-- Supprimer -->
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1 rounded">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Aucune tâche trouvée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>