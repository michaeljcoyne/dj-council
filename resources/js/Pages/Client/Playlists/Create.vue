<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
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

    <div class="min-h-screen bg-black">
        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-900 min-h-screen border-r border-gray-800">
                <div class="px-4 py-6">
                    <div class="flex items-center space-x-2 mb-6 px-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">DJ</span>
                        </div>
                        <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">DJ Council</span>
                    </div>
                    <nav class="space-y-1">
                        <Link href="/client/dashboard" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            Dashboard
                        </Link>
                        <Link href="/client/djs" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            Find DJs
                        </Link>
                        <Link href="/client/bookings" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            My Bookings
                        </Link>
                        <Link href="/client/playlists" class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-lg">
                            My Playlists
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main -->
            <main class="flex-1 bg-gradient-to-b from-gray-900 to-black">
                <div class="p-6">
                    <div class="max-w-3xl mx-auto">
                        <!-- Header -->
                        <div class="mb-6">
                            <Link href="/client/playlists" class="inline-flex items-center text-gray-400 hover:text-white mb-4 transition-colors">
                                <ArrowLeft class="w-4 h-4 mr-2" />
                                <span class="text-sm">Back to Playlists</span>
                            </Link>
                            <h1 class="text-3xl font-bold text-white">Create New Playlist</h1>
                            <p class="mt-1 text-sm text-gray-400">
                                Give your playlist a name and start adding songs
                            </p>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
                            <!-- Name -->
                            <div class="mb-6">
                                <label for="name" class="block text-xs font-medium text-gray-400 uppercase mb-2">
                                    Playlist Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="e.g., Wedding First Dance, Dinner Music, Do Not Play"
                                    class="w-full px-4 py-3 bg-black/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500/50"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <label for="description" class="block text-xs font-medium text-gray-400 uppercase mb-2">
                                    Description (Optional)
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Add notes about when to play these songs..."
                                    class="w-full px-4 py-3 bg-black/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500/50"
                                ></textarea>
                            </div>

                            <!-- Type -->
                            <div class="mb-6">
                                <label class="block text-xs font-medium text-gray-400 uppercase mb-2">
                                    Playlist Type *
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div
                                        @click="form.type = 'must_play'"
                                        class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                                        :class="form.type === 'must_play' ? 'border-green-500/50 bg-green-900/20' : 'border-gray-700'"
                                    >
                                        <div class="font-medium text-white text-sm">Must Play</div>
                                        <div class="text-xs text-gray-400 mt-1">Essential songs</div>
                                    </div>

                                    <div
                                        @click="form.type = 'nice_to_have'"
                                        class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                                        :class="form.type === 'nice_to_have' ? 'border-blue-500/50 bg-blue-900/20' : 'border-gray-700'"
                                    >
                                        <div class="font-medium text-white text-sm">Nice to Have</div>
                                        <div class="text-xs text-gray-400 mt-1">Preferred songs</div>
                                    </div>

                                    <div
                                        @click="form.type = 'do_not_play'"
                                        class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                                        :class="form.type === 'do_not_play' ? 'border-red-500/50 bg-red-900/20' : 'border-gray-700'"
                                    >
                                        <div class="font-medium text-white text-sm">Do Not Play</div>
                                        <div class="text-xs text-gray-400 mt-1">Songs to avoid</div>
                                    </div>

                                    <div
                                        @click="form.type = 'general'"
                                        class="cursor-pointer p-4 border-2 rounded-lg hover:border-gray-600 transition-colors"
                                        :class="form.type === 'general' ? 'border-purple-500/50 bg-purple-900/20' : 'border-gray-700'"
                                    >
                                        <div class="font-medium text-white text-sm">General</div>
                                        <div class="text-xs text-gray-400 mt-1">Mixed collection</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end gap-3 pt-2">
                                <Link
                                    href="/client/playlists"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg text-sm transition-colors"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 disabled:from-gray-600 disabled:to-gray-600 text-white font-medium rounded-lg text-sm transition-all"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create & Add Songs' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
