<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Music, Edit } from 'lucide-vue-next';

const props = defineProps({
    playlist: Object
});

const formatDuration = (seconds) => {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const totalDuration = () => {
    const total = props.playlist.songs.reduce((sum, song) => sum + (song.duration || 0), 0);
    const hours = Math.floor(total / 3600);
    const mins = Math.floor((total % 3600) / 60);
    if (hours > 0) return `${hours}h ${mins}m`;
    return `${mins} min`;
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

const getTypeColor = (type) => {
    const colors = {
        'must_play': 'border-green-500/50 text-green-400',
        'nice_to_have': 'border-blue-500/50 text-blue-400',
        'do_not_play': 'border-red-500/50 text-red-400',
        'general': 'border-purple-500/50 text-purple-400'
    };
    return colors[type] || colors.general;
};
</script>

<template>
    <Head :title="playlist.name" />

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
                    <div class="max-w-4xl mx-auto">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <Link href="/client/playlists" class="inline-flex items-center text-gray-400 hover:text-white transition-colors">
                                <ArrowLeft class="w-4 h-4 mr-2" />
                                <span class="text-sm">Back to My Playlists</span>
                            </Link>
                            <Link :href="`/client/playlists/${playlist.id}/edit`" class="px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium rounded-lg inline-flex items-center text-sm">
                                <Edit class="w-4 h-4 mr-2" />
                                Edit Playlist
                            </Link>
                        </div>

                        <!-- Playlist Info -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6 mb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Music class="w-10 h-10 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h1 class="text-3xl font-bold text-white mb-2">{{ playlist.name }}</h1>
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full border" :class="getTypeColor(playlist.type)">
                                            {{ getTypeLabel(playlist.type) }}
                                        </span>
                                        <span class="text-sm text-gray-400">
                                            {{ playlist.songs.length }} songs • {{ totalDuration() }}
                                        </span>
                                    </div>
                                    <p v-if="playlist.description" class="text-gray-400">
                                        {{ playlist.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Songs List -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
                            <h2 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                Songs ({{ playlist.songs.length }})
                            </h2>

                            <div v-if="playlist.songs.length === 0" class="text-center py-12">
                                <Music class="w-12 h-12 text-gray-600 mx-auto mb-3" />
                                <p class="text-gray-400 text-sm mb-4">No songs in this playlist</p>
                                <Link :href="`/client/playlists/${playlist.id}/edit`" class="inline-flex items-center text-sm text-purple-400 hover:text-purple-300">
                                    <Edit class="w-4 h-4 mr-1.5" />
                                    Add songs
                                </Link>
                            </div>

                            <div v-else class="space-y-1">
                                <div
                                    v-for="(song, index) in playlist.songs"
                                    :key="song.id"
                                    class="flex items-center gap-3 p-3 bg-black/30 rounded-lg"
                                >
                                    <div class="w-6 text-center text-sm text-gray-500 font-medium">{{ index + 1 }}</div>

                                    <img v-if="song.image" :src="song.image" :alt="song.title" class="w-10 h-10 rounded object-cover flex-shrink-0" />
                                    <div v-else class="w-10 h-10 bg-gray-700/50 rounded flex items-center justify-center flex-shrink-0">
                                        <Music class="w-5 h-5 text-gray-500" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-white truncate">{{ song.title }}</div>
                                        <div class="text-xs text-gray-400 truncate">{{ song.artist }}</div>
                                    </div>

                                    <div class="text-xs text-gray-500 tabular-nums">{{ formatDuration(song.duration) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
