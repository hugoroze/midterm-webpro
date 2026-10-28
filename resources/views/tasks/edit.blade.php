<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier la Tâche
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow rounded-lg">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium">Titre</label>
                    <input type="text" name="title" value="{{ $task->title }}" required class="border rounded px-3 py-2 w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium">Catégorie</label>
                    <select name="category_id" required class="border rounded px-3 py-2 w-full">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $task->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="description" class="border rounded px-3 py-2 w-full">{{ $task->description }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium">Date d'échéance</label>
                    <input type="date" name="due_date" value="{{ $task->due_date }}" class="border rounded px-3 py-2 w-full">
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('tasks.index') }}" class="bg-gray-300 px-4 py-2 rounded">Annuler</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>