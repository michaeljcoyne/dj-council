<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Music, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    playlists: [Object, Array]
});

// Handle both paginated object and array
const playlistsData = computed(() => {
    if (Array.isArray(props.playlists)) {
        return props.playlists;
    }
    return props.playlists?.data || [];
});

const getTypeColor = (type) => {
    const colors = {
        'must_play': 'border-green-500/50 text-green-400',
        'nice_to_have': 'border-blue-500/50 text-blue-400',
        'do_not_play': 'border-red-500/50 text-red-400',
        'general': 'border-purple-500/50 text-purple-400'
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
                    <div class="max-w-7xl mx-auto">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-white">My Playlists</h1>
                                <p class="mt-1 text-sm text-gray-400">
                                    Create custom playlists for your events
                                </p>
                            </div>
                            <Link
                                href="/client/playlists/create"
                                class="px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium rounded-lg inline-flex items-center text-sm"
                            >
                                <Plus class="w-4 h-4 mr-2" />
                                Create Playlist
                            </Link>
                        </div>

                        <!-- Empty State -->
                        <div v-if="playlistsData.length === 0" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-12 text-center">
                            <Music class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                            <h3 class="text-xl font-semibold text-white mb-2">No playlists yet</h3>
                            <p class="text-gray-400 mb-6">
                                Create your first playlist to start organizing music
                            </p>
                            <Link
                                href="/client/playlists/create"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium rounded-lg"
                            >
                                <Plus class="w-5 h-5 mr-2" />
                                Create Your First Playlist
                            </Link>
                        </div>

                        <!-- Playlists Grid -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <Link
                                v-for="playlist in playlistsData"
                                :key="playlist.id"
                                :href="`/client/playlists/${playlist.id}/edit`"
                                class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-5 hover:bg-gray-800/70 transition-colors"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-white mb-2">
                                            {{ playlist.name }}
                                        </h3>
                                        <span
                                            class="inline-block px-2 py-0.5 text-xs font-medium rounded-full border"
                                            :class="getTypeColor(playlist.type)"
                                        >
                                            {{ getTypeLabel(playlist.type) }}
                                        </span>
                                    </div>
                                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <Music class="w-5 h-5 text-white" />
                                    </div>
                                </div>

                                <p v-if="playlist.description" class="text-sm text-gray-400 mb-3 line-clamp-2">
                                    {{ playlist.description }}
                                </p>

                                <div class="text-sm text-gray-500">
                                    {{ playlist.songs_count }} songs
                                </div>
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="playlistsData.length > 0 && playlists.prev_page_url || playlists.next_page_url" class="mt-6 flex justify-center gap-2">
                            <Link
                                v-if="playlists.prev_page_url"
                                :href="playlists.prev_page_url"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="playlists.next_page_url"
                                :href="playlists.next_page_url"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
