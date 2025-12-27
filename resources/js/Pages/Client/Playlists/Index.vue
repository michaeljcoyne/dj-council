<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Music, Plus, Calendar, Trash2, Edit } from 'lucide-vue-next';

const props = defineProps({
    playlists: Array
});

const getTypeColor = (type) => {
    const colors = {
        'must_play': 'bg-green-900 text-green-400',
        'nice_to_have': 'bg-blue-900 text-blue-400',
        'do_not_play': 'bg-red-900 text-red-400',
        'general': 'bg-gray-700 text-gray-300'
    };
    return colors[type] || colors.general;
};

const getTypeLabel = (type) => {
    const labels = {
        'must_play': 'Must Play',
        'nice_to_have': 'Nice to Have',
        'do_not_play': 'Do Not Play',
        'general': 'General'
    };
    return labels[type] || 'General';
};
</script>

<template>
    <Head title="My Playlists" />

    <div class="min-h-screen bg-gray-900 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">My Playlists</h1>
                    <p class="mt-2 text-gray-400">
                        Create custom playlists for your events and share with DJs
                    </p>
                </div>
                <Link
                    href="/client/playlists/create"
                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg inline-flex items-center"
                >
                    <Plus class="w-5 h-5 mr-2" />
                    Create Playlist
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="playlists.length === 0" class="bg-gray-800 rounded-lg p-12 text-center">
                <Music class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                <h3 class="text-xl font-semibold text-white mb-2">No playlists yet</h3>
                <p class="text-gray-400 mb-6">
                    Create your first playlist to start organizing music for your events
                </p>
                <Link
                    href="/client/playlists/create"
                    class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg"
                >
                    <Plus class="w-5 h-5 mr-2" />
                    Create Your First Playlist
                </Link>
            </div>

            <!-- Playlists Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="playlist in playlists"
                    :key="playlist.id"
                    class="bg-gray-800 rounded-lg overflow-hidden hover:ring-2 hover:ring-purple-500 transition-all"
                >
                    <!-- Playlist Header -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white mb-1">
                                    {{ playlist.name }}
                                </h3>
                                <span
                                    class="inline-block px-2 py-1 text-xs font-medium rounded-full"
                                    :class="getTypeColor(playlist.type)"
                                >
                                    {{ getTypeLabel(playlist.type) }}
                                </span>
                            </div>
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <Music class="w-6 h-6 text-white" />
                            </div>
                        </div>

                        <p v-if="playlist.description" class="text-sm text-gray-400 mb-4 line-clamp-2">
                            {{ playlist.description }}
                        </p>

                        <!-- Stats -->
                        <div class="flex items-center gap-4 text-sm text-gray-400 mb-4">
                            <div class="flex items-center">
                                <Music class="w-4 h-4 mr-1" />
                                {{ playlist.songs_count }} songs
                            </div>
                            <div v-if="playlist.booking" class="flex items-center">
                                <Calendar class="w-4 h-4 mr-1" />
                                Linked to booking
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <Link
                                :href="`/client/playlists/${playlist.id}`"
                                class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded-lg text-center"
                            >
                                View
                            </Link>
                            <Link
                                :href="`/client/playlists/${playlist.id}/edit`"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg"
                            >
                                <Edit class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
