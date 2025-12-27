<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ArrowLeft, Search, Plus, X, GripVertical, Music, Play, Pause, Trash2, Pencil } from 'lucide-vue-next';
import axios from 'axios';
import SpotifyPlayer from '@/Components/SpotifyPlayer.vue';

const props = defineProps({
    playlist: Object
});

const form = useForm({
    name: props.playlist.name,
    description: props.playlist.description,
    type: props.playlist.type,
});

const deleteForm = useForm({});
const editingInfo = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const searching = ref(false);
const songs = ref([...props.playlist.songs]);
const playingPreview = ref(null);
const toast = ref({ show: false, message: '', type: '' });
let audioPlayer = null;

// DEBUG: Log existing songs on load
console.log('🎼 Loaded playlist songs:', props.playlist.songs);
props.playlist.songs.forEach((song, i) => {
    console.log(`Song ${i + 1}:`, {
        title: song.title,
        hasImage: !!song.image,
        imageUrl: song.image,
        hasPreview: !!song.preview_url,
        previewUrl: song.preview_url
    });
});

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 2500);
};

let searchTimeout = null;
const searchSongs = async () => {
    clearTimeout(searchTimeout);
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    searchTimeout = setTimeout(async () => {
        searching.value = true;
        try {
            const response = await axios.get('/client/songs/search', {
                params: { q: searchQuery.value }
            });
            searchResults.value = response.data;
            console.log('Search results:', searchResults.value); // DEBUG
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            searching.value = false;
        }
    }, 300);
};

const addSong = async (song) => {
    try {
        console.log('🎵 Adding song with data:', song); // DEBUG
        console.log('📷 Image URL:', song.image); // DEBUG
        console.log('▶️ Preview URL:', song.preview_url); // DEBUG

        const response = await axios.post(`/client/playlists/${props.playlist.id}/songs`, song);

        console.log('✅ Song added, server response:', response.data); // DEBUG
        console.log('📀 Saved song data:', response.data.song); // DEBUG

        songs.value.push(response.data.song);
        searchQuery.value = '';
        searchResults.value = [];
        showToast('Song added');
    } catch (error) {
        console.error('❌ Add song error:', error);
        console.error('Error response:', error.response?.data); // DEBUG
        showToast('Failed to add song', 'error');
    }
};

const removeSong = async (songId) => {
    try {
        await axios.delete(`/client/playlists/${props.playlist.id}/songs/${songId}`);
        songs.value = songs.value.filter(s => s.id !== songId);
        showToast('Song removed');
    } catch (error) {
        console.error('Remove song error:', error);
        showToast('Failed to remove', 'error');
    }
};

const handlePlaying = (spotifyId) => {
    // Optional: Stop other players, show now playing, etc.
    console.log('Now playing:', spotifyId);
};

const handleStopped = () => {
    console.log('Stopped');
};

const deletePlaylist = () => {
    if (!confirm(`Delete "${form.name}"?`)) return;
    deleteForm.delete(`/client/playlists/${props.playlist.id}`);
};

const playPreview = (previewUrl, songId) => {
    console.log('Playing preview:', previewUrl, songId); // DEBUG
    if (!previewUrl) {
        showToast('No preview available', 'error');
        return;
    }
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer = null;
    }
    if (playingPreview.value === songId) {
        playingPreview.value = null;
        return;
    }
    audioPlayer = new Audio(previewUrl);
    playingPreview.value = songId;
    audioPlayer.play();
    audioPlayer.onended = () => { playingPreview.value = null; };
};

const formatDuration = (seconds) => {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const totalDuration = computed(() => {
    const total = songs.value.reduce((sum, song) => sum + (song.duration || 0), 0);
    const hours = Math.floor(total / 3600);
    const mins = Math.floor((total % 3600) / 60);
    if (hours > 0) return `${hours}h ${mins}m`;
    return `${mins} min`;
});

const saveInfo = () => {
    form.put(`/client/playlists/${props.playlist.id}`, {
        onSuccess: () => { editingInfo.value = false; showToast('Saved'); }
    });
};

const draggedSong = ref(null);
const onDragStart = (song) => { draggedSong.value = song; };
const onDrop = async (targetSong) => {
    if (!draggedSong.value || draggedSong.value.id === targetSong.id) return;
    const draggedIndex = songs.value.findIndex(s => s.id === draggedSong.value.id);
    const targetIndex = songs.value.findIndex(s => s.id === targetSong.id);
    songs.value.splice(draggedIndex, 1);
    songs.value.splice(targetIndex, 0, draggedSong.value);
    try {
        await axios.put(`/client/playlists/${props.playlist.id}/songs/reorder`, {
            song_ids: songs.value.map(s => s.id)
        });
    } catch (error) {
        console.error('Reorder error:', error);
    }
    draggedSong.value = null;
};

const getTypeLabel = (type) => ({
    'must_play': 'Must Play',
    'nice_to_have': 'Nice to Have',
    'do_not_play': 'Do Not Play',
    'general': 'General'
}[type] || 'General');

const getTypeColor = (type) => ({
    'must_play': 'border-green-500/50 text-green-400',
    'nice_to_have': 'border-blue-500/50 text-blue-400',
    'do_not_play': 'border-red-500/50 text-red-400',
    'general': 'border-purple-500/50 text-purple-400'
}[type] || 'border-purple-500/50 text-purple-400');
</script>

<template>
    <Head :title="`Edit: ${playlist.name}`" />

    <!-- Toast -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="toast.show" class="fixed top-4 right-4 z-50 px-3 py-2 rounded-lg shadow-lg text-xs font-medium"
             :class="toast.type === 'error' ? 'bg-red-500 text-white' : 'bg-white text-gray-900'">
            {{ toast.message }}
        </div>
    </Transition>

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
                        <Link href="/client/dashboard" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">Dashboard</Link>
                        <Link href="/client/djs" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">Find DJs</Link>
                        <Link href="/client/bookings" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">My Bookings</Link>
                        <Link href="/client/playlists" class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-lg">My Playlists</Link>
                    </nav>
                </div>
            </aside>

            <!-- Main -->
            <main class="flex-1 bg-gradient-to-b from-gray-900 to-black">
                <div class="max-w-6xl mx-auto p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <Link href="/client/playlists" class="inline-flex items-center text-gray-400 hover:text-white transition-colors">
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            <span class="text-sm">Back to My Playlists</span>
                        </Link>
                        <button @click="deletePlaylist" :disabled="deleteForm.processing" class="inline-flex items-center text-sm text-red-400 hover:text-red-300 transition-colors disabled:opacity-50">
                            <Trash2 class="w-4 h-4 mr-1.5" />
                            Delete Playlist
                        </button>
                    </div>

                    <!-- Info Panel - YOUR ORIGINAL STYLE -->
                    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-4 mb-4">
                        <div v-if="!editingInfo" @click="editingInfo = true" class="cursor-pointer hover:bg-gray-700/30 -m-4 p-4 rounded-lg transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <h1 class="text-xl font-bold text-white">{{ form.name }}</h1>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full border" :class="getTypeColor(form.type)">{{ getTypeLabel(form.type) }}</span>
                                    <span class="text-sm text-gray-400">{{ songs.length }} songs • {{ totalDuration }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <Pencil class="w-3.5 h-3.5" />
                                    <span class="text-xs">Edit</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <input v-model="form.name" type="text" placeholder="Playlist name" class="px-3 py-2 bg-black/50 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-pink-500/50" />
                                <select v-model="form.type" class="px-3 py-2 bg-black/50 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-pink-500/50">
                                    <option value="must_play">Must Play</option>
                                    <option value="nice_to_have">Nice to Have</option>
                                    <option value="do_not_play">Do Not Play</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                            <textarea v-model="form.description" rows="2" placeholder="Add notes..." class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500/50"></textarea>
                            <div class="flex justify-end gap-2">
                                <button @click="editingInfo = false" class="px-3 py-1.5 text-sm text-gray-400 hover:text-white transition-colors">Cancel</button>
                                <button @click="saveInfo" :disabled="form.processing" class="px-4 py-1.5 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg">
                                    {{ form.processing ? 'Saving...' : 'Save' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Search - YOUR ORIGINAL STYLE -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-4">
                            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Add Songs</h2>

                            <div class="relative mb-3">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <Search class="w-4 h-4 text-gray-500" />
                                </div>
                                <input
                                    v-model="searchQuery"
                                    @input="searchSongs"
                                    type="text"
                                    placeholder="Search for songs..."
                                    class="w-full h-10 pl-10 pr-3 bg-black/50 border border-gray-700 rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500/50 focus:border-pink-500/50"
                                />
                            </div>

                            <div v-if="searching" class="text-center py-8">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-500 mx-auto"></div>
                            </div>

                            <div v-else-if="searchResults.length > 0" class="space-y-1 max-h-96 overflow-y-auto">
                                <div v-for="result in searchResults" :key="result.spotify_id" @click="addSong(result)" class="flex items-center gap-3 p-2 bg-black/30 hover:bg-gray-800/50 rounded-lg cursor-pointer transition-colors group">
                                    <img v-if="result.image" :src="result.image" :alt="result.title" class="w-10 h-10 rounded object-cover flex-shrink-0" />
                                    <div v-else class="w-10 h-10 bg-gray-700/50 rounded flex items-center justify-center flex-shrink-0">
                                        <Music class="w-5 h-5 text-gray-500" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-white truncate">{{ result.title }}</div>
                                        <div class="text-xs text-gray-400 truncate">{{ result.artist }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ formatDuration(result.duration) }}</div>
                                    <div class="opacity-0 group-hover:opacity-100">
                                        <div class="w-8 h-8 bg-pink-600 hover:bg-pink-700 rounded-full flex items-center justify-center">
                                            <Plus class="w-4 h-4 text-white" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="searchQuery && !searching" class="text-center py-8 text-gray-500 text-sm">No results</div>
                            <div v-else class="text-center py-10 text-gray-500 text-sm">
                                <Music class="w-8 h-8 mx-auto mb-2 text-gray-600" />
                                Search to add songs
                            </div>
                        </div>

                        <!-- Songs - YOUR ORIGINAL STYLE WITH VISIBLE BUTTONS -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-4">
                            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Songs ({{ songs.length }})</h2>

                            <div v-if="songs.length === 0" class="text-center py-10">
                                <Music class="w-8 h-8 text-gray-600 mx-auto mb-2" />
                                <p class="text-gray-500 text-sm">No songs yet</p>
                            </div>

                            <div v-else class="space-y-1 max-h-96 overflow-y-auto">
                                <div
                                    v-for="(song, index) in songs"
                                    :key="song.id"
                                    draggable="true"
                                    @dragstart="onDragStart(song)"
                                    @dragover.prevent
                                    @drop="onDrop(song)"
                                    class="flex items-center gap-2 p-2 bg-black/30 hover:bg-gray-800/40 rounded-lg cursor-move transition-all group"
                                    :class="{ 'opacity-40 scale-95': draggedSong && draggedSong.id === song.id }"
                                >
                                    <GripVertical class="w-3.5 h-3.5 text-gray-600 flex-shrink-0" />
                                    <div class="w-4 text-center text-xs text-gray-500 font-medium">{{ index + 1 }}</div>

                                    <!-- Debug: Show if image exists -->
                                    <img v-if="song.image" :src="song.image" :alt="song.title" class="w-10 h-10 rounded object-cover flex-shrink-0" />
                                    <div v-else class="w-10 h-10 bg-gray-700/50 rounded flex items-center justify-center flex-shrink-0">
                                        <Music class="w-5 h-5 text-gray-500" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-white truncate">{{ song.title }}</div>
                                        <div class="text-xs text-gray-400 truncate">{{ song.artist }}</div>
                                    </div>

                                    <div class="text-xs text-gray-500 tabular-nums mr-1">{{ formatDuration(song.duration) }}</div>

                                    <!-- Smaller, tighter buttons -->
                                    <!--<button
                                        @click.stop="playPreview(song.preview_url, song.id)"
                                        class="w-7 h-7 rounded-full flex items-center justify-center transition-all flex-shrink-0"
                                        :class="playingPreview === song.id ? 'bg-pink-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-gray-300'"
                                        :title="song.preview_url ? 'Play preview' : 'No preview available'"
                                    >
                                        <Play v-if="playingPreview !== song.id" class="w-3 h-3 ml-0.5" />
                                        <Pause v-else class="w-3 h-3" />
                                    </button>-->
                                    <SpotifyPlayer
                                        :spotify-id="song.spotify_id"
                                        :title="song.title"
                                        :artist="song.artist"
                                        :preview-url="song.preview_url"
                                        @playing="handlePlaying"
                                        @stopped="handleStopped"
                                    />

                                    <button
                                        @click="removeSong(song.id)"
                                        class="w-7 h-7 rounded-full bg-gray-700 hover:bg-gray-600 text-gray-400 hover:text-red-400 flex items-center justify-center transition-all flex-shrink-0"
                                    >
                                        <X class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
