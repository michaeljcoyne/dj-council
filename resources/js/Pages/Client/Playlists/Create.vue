<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    description: '',
    type: 'general',
    booking_id: null,
});

const submit = () => {
    form.post('/client/playlists');
};
</script>

<template>
    <Head title="Create Playlist" />

    <div class="min-h-screen bg-gray-900 py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a
                    href="/client/playlists"
                    class="inline-flex items-center text-purple-400 hover:text-purple-300 mb-4"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Playlists
                </a>
                <h1 class="text-3xl font-bold text-white">Create New Playlist</h1>
                <p class="mt-2 text-gray-400">
                    Give your playlist a name and start adding songs
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-gray-800 rounded-lg p-6">
                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-white mb-2">
                        Playlist Name *
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="e.g., Wedding First Dance, Dinner Music, Do Not Play"
                        class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-white mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        placeholder="Add notes about when to play these songs..."
                        class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    ></textarea>
                </div>

                <!-- Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-white mb-2">
                        Playlist Type *
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            @click="form.type = 'must_play'"
                            class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                            :class="form.type === 'must_play' ? 'border-green-500 bg-green-900/20' : 'border-gray-700'"
                        >
                            <div class="font-medium text-white">Must Play</div>
                            <div class="text-sm text-gray-400">Essential songs for the event</div>
                        </div>

                        <div
                            @click="form.type = 'nice_to_have'"
                            class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                            :class="form.type === 'nice_to_have' ? 'border-blue-500 bg-blue-900/20' : 'border-gray-700'"
                        >
                            <div class="font-medium text-white">Nice to Have</div>
                            <div class="text-sm text-gray-400">Preferred but not required</div>
                        </div>

                        <div
                            @click="form.type = 'do_not_play'"
                            class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                            :class="form.type === 'do_not_play' ? 'border-red-500 bg-red-900/20' : 'border-gray-700'"
                        >
                            <div class="font-medium text-white">Do Not Play</div>
                            <div class="text-sm text-gray-400">Songs to avoid</div>
                        </div>

                        <div
                            @click="form.type = 'general'"
                            class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                            :class="form.type === 'general' ? 'border-purple-500 bg-purple-900/20' : 'border-gray-700'"
                        >
                            <div class="font-medium text-white">General</div>
                            <div class="text-sm text-gray-400">Mixed collection</div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4">
                    <a
                        href="/client/playlists"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg"
                    >
                        {{ form.processing ? 'Creating...' : 'Create & Add Songs' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
