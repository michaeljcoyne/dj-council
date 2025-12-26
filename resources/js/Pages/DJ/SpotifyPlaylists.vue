<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Music, Check, X, ExternalLink, Copy, Code } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    djProfile: Object,
    isConnected: Boolean,
});

const playlists = ref([]);
const selectedPlaylists = ref(props.djProfile?.spotify_playlists || []);
const loading = ref(false);
const widgetCode = ref('');
const showWidgetModal = ref(false);
const copiedWidget = ref(false);

onMounted(() => {
    if (props.isConnected) {
        fetchPlaylists();
    }
});

const connectSpotify = () => {
    window.location.href = '/spotify/redirect';
};

const disconnectSpotify = () => {
    if (confirm('Are you sure you want to disconnect your Spotify account?')) {
        useForm({}).post('/spotify/disconnect');
    }
};

const fetchPlaylists = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/spotify/playlists');
        playlists.value = response.data;
    } catch (error) {
        console.error('Error fetching playlists:', error);
        alert('Failed to load playlists. Please try reconnecting your Spotify account.');
    } finally {
        loading.value = false;
    }
};

const togglePlaylist = (playlistId) => {
    const index = selectedPlaylists.value.indexOf(playlistId);
    if (index > -1) {
        selectedPlaylists.value.splice(index, 1);
    } else {
        selectedPlaylists.value.push(playlistId);
    }
};

const savePlaylists = async () => {
    loading.value = true;
    try {
        await axios.post('/spotify/playlists/save', {
            playlist_ids: selectedPlaylists.value
        });
        alert('Playlists saved successfully!');
    } catch (error) {
        console.error('Error saving playlists:', error);
        alert('Failed to save playlists. Please try again.');
    } finally {
        loading.value = false;
    }
};

const generateWidget = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/spotify/widget/generate');
        widgetCode.value = response.data.embed_code;
        showWidgetModal.value = true;
    } catch (error) {
        console.error('Error generating widget:', error);
        alert('Failed to generate widget. Please select at least one playlist.');
    } finally {
        loading.value = false;
    }
};

const copyWidgetCode = () => {
    navigator.clipboard.writeText(widgetCode.value);
    copiedWidget.value = true;
    setTimeout(() => {
        copiedWidget.value = false;
    }, 2000);
};
</script>

<template>
    <Head title="Spotify Playlists" />

    <div class="min-h-screen bg-gray-900 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Spotify Integration</h1>
                        <p class="mt-2 text-gray-400">
                            Connect your Spotify account and select playlists to showcase on your profile
                        </p>
                    </div>
                    <Link href="/dj/dashboard" class="text-purple-400 hover:text-purple-300">
                        ← Back to Dashboard
                    </Link>
                </div>
            </div>

            <!-- Not Connected State -->
            <div v-if="!isConnected" class="bg-gray-800 rounded-lg p-8 text-center">
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Music class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Connect Your Spotify Account</h2>
                <p class="text-gray-400 mb-6 max-w-2xl mx-auto">
                    Link your Spotify account to import your playlists and showcase them on your DJ profile.
                    This helps clients preview your music style and makes booking decisions easier.
                </p>
                <button
                    @click="connectSpotify"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg inline-flex items-center"
                >
                    <Music class="w-5 h-5 mr-2" />
                    Connect Spotify
                </button>
            </div>

            <!-- Connected State -->
            <div v-else>
                <!-- Connection Status -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mr-4">
                            <Check class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Spotify Connected</h3>
                            <p class="text-sm text-gray-400">Your Spotify account is linked</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="generateWidget"
                            :disabled="selectedPlaylists.length === 0 || loading"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg inline-flex items-center"
                        >
                            <Code class="w-4 h-4 mr-2" />
                            Get Widget Code
                        </button>
                        <button
                            @click="disconnectSpotify"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg"
                        >
                            Disconnect
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading && playlists.length === 0" class="bg-gray-800 rounded-lg p-12 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto mb-4"></div>
                    <p class="text-gray-400">Loading your playlists...</p>
                </div>

                <!-- Playlists Grid -->
                <div v-else>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">Your Playlists</h2>
                        <p class="text-sm text-gray-400">
                            {{ selectedPlaylists.length }} selected
                        </p>
                    </div>

                    <div v-if="playlists.length === 0" class="bg-gray-800 rounded-lg p-12 text-center">
                        <p class="text-gray-400">No playlists found in your Spotify account</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="playlist in playlists"
                            :key="playlist.id"
                            @click="togglePlaylist(playlist.id)"
                            class="bg-gray-800 rounded-lg overflow-hidden cursor-pointer transition-all hover:ring-2 hover:ring-purple-500"
                            :class="{ 'ring-2 ring-purple-500': selectedPlaylists.includes(playlist.id) }"
                        >
                            <!-- Playlist Image -->
                            <div class="relative h-48 bg-gray-700">
                                <img
                                    v-if="playlist.image"
                                    :src="playlist.image"
                                    :alt="playlist.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <Music class="w-12 h-12 text-gray-500" />
                                </div>

                                <!-- Selection Indicator -->
                                <div
                                    v-if="selectedPlaylists.includes(playlist.id)"
                                    class="absolute top-2 right-2 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center"
                                >
                                    <Check class="w-5 h-5 text-white" />
                                </div>
                            </div>

                            <!-- Playlist Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-white mb-1 truncate">{{ playlist.name }}</h3>
                                <p v-if="playlist.description" class="text-sm text-gray-400 mb-2 line-clamp-2">
                                    {{ playlist.description }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ playlist.tracks_count }} tracks</span>
                                    <a
                                        :href="playlist.url"
                                        target="_blank"
                                        @click.stop
                                        class="text-purple-400 hover:text-purple-300 inline-flex items-center"
                                    >
                                        Open in Spotify
                                        <ExternalLink class="w-3 h-3 ml-1" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div v-if="playlists.length > 0" class="mt-8 flex justify-end">
                        <button
                            @click="savePlaylists"
                            :disabled="loading"
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg"
                        >
                            {{ loading ? 'Saving...' : 'Save Selected Playlists' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget Code Modal -->
        <div
            v-if="showWidgetModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showWidgetModal = false"
        >
            <div class="bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Widget Embed Code</h3>
                    <button
                        @click="showWidgetModal = false"
                        class="text-gray-400 hover:text-white"
                    >
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <p class="text-gray-400 mb-4">
                    Copy this code and paste it into your website where you want the playlist widget to appear:
                </p>

                <div class="relative">
                    <pre class="bg-gray-900 p-4 rounded-lg text-green-400 text-sm overflow-x-auto">{{ widgetCode }}</pre>
                    <button
                        @click="copyWidgetCode"
                        class="absolute top-2 right-2 px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded flex items-center"
                    >
                        <Copy class="w-4 h-4 mr-1" />
                        {{ copiedWidget ? 'Copied!' : 'Copy' }}
                    </button>
                </div>

                <div class="mt-4 p-4 bg-blue-900/20 border border-blue-500/30 rounded-lg">
                    <p class="text-sm text-blue-400">
                        💡 This widget will display your selected Spotify playlists with embedded players that visitors can use to preview your music.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
