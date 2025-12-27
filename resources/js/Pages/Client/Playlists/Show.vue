<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ArrowLeft, Search, Plus, X, GripVertical, Music, ChevronDown, ChevronUp, Trash2, Play } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    playlist: Object
});

// Form for playlist details
const form = useForm({
    name: props.playlist.name,
    description: props.playlist.description,
    type: props.playlist.type,
});

const deleteForm = useForm({});

// UI state
const editingInfo = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const searching = ref(false);
const songs = ref([...props.playlist.songs]);
const playingPreview = ref(null);

// Audio for preview
let audioPlayer = null;

// Search with debounce
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
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            searching.value = false;
        }
    }, 300);
};

// Add song
const addSong = async (song) => {
    try {
        const response = await axios.post(`/client/playlists/${props.playlist.id}/songs`, song);
        songs.value.push(response.data.song);
        searchQuery.value = '';
        searchResults.value = [];
    } catch (error) {
        console.error('Add song error:', error);
        alert('Failed to add song');
    }
};

// Remove song
const removeSong = async (songId) => {
    if (!confirm('Remove this song?')) return;
    try {
        await axios.delete(`/client/playlists/${props.playlist.id}/songs/${songId}`);
        songs.value = songs.value.filter(s => s.id !== songId);
    } catch (error) {
        console.error('Remove song error:', error);
    }
};

// Delete playlist
const deletePlaylist = () => {
    if (!confirm(`Delete "${form.name}"? This cannot be undone.`)) return;
    deleteForm.delete(`/client/playlists/${props.playlist.id}`);
};

// Play preview
const playPreview = (previewUrl, songId) => {
    if (!previewUrl) {
        alert('No preview available for this song');
        return;
    }

    // Stop current playing
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer = null;
    }

    // If clicking same song, just stop
    if (playingPreview.value === songId) {
        playingPreview.value = null;
        return;
    }

    // Play new preview
    audioPlayer = new Audio(previewUrl);
    playingPreview.value = songId;
    audioPlayer.play();

    audioPlayer.onended = () => {
        playingPreview.value = null;
    };
};

// Format duration
const formatDuration = (seconds) => {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Total duration
const totalDuration = computed(() => {
    const total = songs.value.reduce((sum, song) => sum + (song.duration || 0), 0);
    const hours = Math.floor(total / 3600);
    const mins = Math.floor((total % 3600) / 60);
    if (hours > 0) return `${hours}h ${mins}m`;
    return `${mins} min`;
});

// Update playlist
const saveInfo = () => {
    form.put(`/client/playlists/${props.playlist.id}`, {
        onSuccess: () => {
            editingInfo.value = false;
        }
    });
};

// Drag and drop
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
        'must_play': 'bg-green-900/20 text-green-400 border-green-500/30',
        'nice_to_have': 'bg-blue-900/20 text-blue-400 border-blue-500/30',
        'do_not_play': 'bg-red-900/20 text-red-400 border-red-500/30',
        'general': 'bg-purple-900/20 text-purple-400 border-purple-500/30'
    };
    return colors[type] || colors.general;
};
</script>

<template>
    <Head :title="`Edit: ${playlist.name}`" />

    <div class="min-h-screen bg-gray-900">
        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-800 min-h-screen">
                <div class="px-4 py-6">
                    <div class="flex items-center space-x-2 mb-6 px-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">DJ</span>
                        </div>
                        <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
                            DJ Council
                        </span>
                    </div>

                    <nav class="space-y-1">
                        <Link href="/client/dashboard" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            Dashboard
                        </Link>
                        <Link href="/client/djs" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            Find DJs
                        </Link>
                        <Link href="/client/bookings" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            My Bookings
                        </Link>
                        <Link href="/client/playlists" class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg">
                            My Playlists
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Back Navigation & Delete -->
                    <div class="flex items-center justify-between mb-6">
                        <Link
                            href="/client/playlists"
                            class="inline-flex items-center text-purple-400 hover:text-purple-300 transition-colors"
                        >
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            Back to My Playlists
                        </Link>
                        <button
                            @click="deletePlaylist"
                            :disabled="deleteForm.processing"
                            class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 hover:text-red-300 rounded-lg inline-flex items-center transition-colors disabled:opacity-50"
                        >
                            <Trash2 class="w-4 h-4 mr-2" />
                            Delete Playlist
                        </button>
                    </div>

                    <!-- Info Panel -->
                    <div class="bg-gray-800 rounded-lg mb-6">
                        <!-- Collapsed View -->
                        <div
                            v-if="!editingInfo"
                            @click="editingInfo = true"
                            class="p-6 cursor-pointer hover:bg-gray-700/50 transition-colors rounded-lg"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h1 class="text-2xl font-bold text-white">{{ form.name }}</h1>
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full border"
                                            :class="getTypeColor(form.type)"
                                        >
                                            {{ getTypeLabel(form.type) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-400 text-sm mb-2">{{ songs.length }} songs • {{ totalDuration }}</p>
                                    <p v-if="form.description" class="text-gray-500 text-sm">{{ form.description }}</p>
                                </div>
                                <div class="text-purple-400">
                                    <ChevronDown class="w-5 h-5" />
                                </div>
                            </div>
                            <p class="text-xs text-purple-400 mt-3">Click to edit playlist details</p>
                        </div>

                        <!-- Expanded Edit View -->
                        <div v-else class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-semibold text-white">Edit Playlist Details</h2>
                                <button
                                    @click="editingInfo = false"
                                    class="text-gray-400 hover:text-white"
                                >
                                    <ChevronUp class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2 uppercase">Playlist Name</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    />
                                </div>

                                <!-- Type -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2 uppercase">Type</label>
                                    <select
                                        v-model="form.type"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    >
                                        <option value="must_play">Must Play</option>
                                        <option value="nice_to_have">Nice to Have</option>
                                        <option value="do_not_play">Do Not Play</option>
                                        <option value="general">General</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2 uppercase">Notes</label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        placeholder="Add notes for your DJ..."
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    ></textarea>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end gap-3 pt-2">
                                    <button
                                        @click="editingInfo = false"
                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="saveInfo"
                                        :disabled="form.processing"
                                        class="px-6 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 disabled:from-gray-600 disabled:to-gray-600 text-white font-medium rounded-lg transition-all"
                                    >
                                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Panel -->
                    <div class="bg-gray-800 rounded-lg p-6 mb-6">
                        <h2 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Add Songs</h2>

                        <div class="relative mb-4">
                            <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                @input="searchSongs"
                                type="text"
                                placeholder="Search for songs..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Search Results -->
                        <div v-if="searching" class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto"></div>
                        </div>

                        <div v-else-if="searchResults.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
                            <div
                                v-for="result in searchResults"
                                :key="result.spotify_id"
                                class="flex items-center gap-3 p-3 bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors group"
                            >
                                <!-- Album Image -->
                                <img
                                    v-if="result.image"
                                    :src="result.image"
                                    :alt="result.title"
                                    class="w-12 h-12 rounded object-cover flex-shrink-0"
                                />
                                <div v-else class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center flex-shrink-0">
                                    <Music class="w-6 h-6 text-gray-500" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">{{ result.title }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ result.artist }}</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ formatDuration(result.duration) }}</div>
                                <button
                                    @click="addSong(result)"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <div class="w-8 h-8 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center">
                                        <Plus class="w-4 h-4 text-white" />
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div v-else-if="searchQuery && !searching" class="text-center py-8 text-gray-500 text-sm">
                            No results found
                        </div>

                        <div v-else class="text-center py-8 text-gray-500 text-sm">
                            <Music class="w-10 h-10 mx-auto mb-2 text-gray-600" />
                            Search for songs to add to your playlist
                        </div>
                    </div>

                    <!-- Playlist Panel -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h2 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                            Songs ({{ songs.length }})
                        </h2>

                        <div v-if="songs.length === 0" class="text-center py-12">
                            <Music class="w-12 h-12 text-gray-600 mx-auto mb-3" />
                            <p class="text-gray-400 text-sm">No songs yet. Search above to add some!</p>
                        </div>

                        <div v-else class="space-y-1">
                            <div
                                v-for="(song, index) in songs"
                                :key="song.id"
                                draggable="true"
                                @dragstart="onDragStart(song)"
                                @dragover.prevent
                                @drop="onDrop(song)"
                                class="flex items-center gap-3 p-3 bg-gray-900 hover:bg-gray-700 rounded-lg cursor-move group transition-colors"
                            >
                                <GripVertical class="w-5 h-5 text-gray-600 group-hover:text-gray-400 flex-shrink-0" />
                                <div class="w-6 text-center text-sm text-gray-500 font-medium">{{ index + 1 }}</div>

                                <!-- Album Image -->
                                <img
                                    v-if="song.image"
                                    :src="song.image"
                                    :alt="song.title"
                                    class="w-10 h-10 rounded object-cover flex-shrink-0"
                                />
                                <div v-else class="w-10 h-10 bg-gray-700 rounded flex items-center justify-center flex-shrink-0">
                                    <Music class="w-5 h-5 text-gray-500" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">{{ song.title }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ song.artist }}</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ formatDuration(song.duration) }}</div>

                                <!-- Play Button -->
                                <button
                                    v-if="song.preview_url"
                                    @click.stop="playPreview(song.preview_url, song.id)"
                                    class="opacity-0 group-hover:opacity-100 p-2 hover:bg-purple-900/20 rounded-lg transition-all"
                                    :class="{ 'opacity-100 bg-purple-900/20': playingPreview === song.id }"
                                >
                                    <Play class="w-4 h-4" :class="playingPreview === song.id ? 'text-purple-400' : 'text-gray-500'" />
                                </button>

                                <button
                                    @click="removeSong(song.id)"
                                    class="opacity-0 group-hover:opacity-100 p-2 hover:bg-red-900/20 rounded-lg transition-all"
                                >
                                    <X class="w-4 h-4 text-gray-500 hover:text-red-400" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
