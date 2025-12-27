<script setup>
import { ref } from 'vue';
import { Play, Pause, ExternalLink } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    spotifyId: String,
    title: String,
    artist: String,
    previewUrl: String, // Spotify preview_url from database
});

const emit = defineEmits(['playing', 'stopped']);

const isPlaying = ref(false);
const isLoading = ref(false);
const currentSource = ref(null); // 'preview', 'youtube', or null
const youtubeVideoId = ref(null);
const showYouTubePlayer = ref(false);
let audioPlayer = null;

const play = async () => {
    isLoading.value = true;

    // Try 1: Spotify preview_url (40% success rate, instant)
    // Check for actual URL, not just truthy value
    if (props.previewUrl && props.previewUrl !== 'null' && props.previewUrl.startsWith('http')) {
        console.log('✅ Using Spotify preview_url:', props.previewUrl);
        playPreview();
        isLoading.value = false;
        return;
    }

    console.log('❌ No valid preview_url, trying YouTube...', props.previewUrl);

    // Try 2: YouTube (50% success rate, slower)
    try {
        await playYouTube();
        isLoading.value = false;
        return;
    } catch (error) {
        console.log('❌ YouTube failed:', error);
    }

    // Try 3: Open in Spotify app (always works)
    console.log('⚠️ All previews failed, opening Spotify app');
    isLoading.value = false;
    openInSpotify();
};

const playPreview = () => {
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer = null;
    }

    audioPlayer = new Audio(props.previewUrl);
    currentSource.value = 'preview';
    isPlaying.value = true;

    audioPlayer.play().catch(err => {
        console.error('Preview playback failed:', err);
        // If preview fails, try YouTube
        playYouTube().catch(() => openInSpotify());
    });

    audioPlayer.onended = () => {
        stop();
    };

    emit('playing', props.spotifyId);

    // Auto-stop after 30 seconds
    setTimeout(() => {
        if (currentSource.value === 'preview') {
            stop();
        }
    }, 30000);
};

const playYouTube = async () => {
    const searchQuery = `${props.artist} ${props.title}`;

    try {
        const response = await axios.get('/youtube/search', {
            params: { q: searchQuery }
        });

        if (!response.data.video_id) {
            throw new Error('No YouTube video found');
        }

        youtubeVideoId.value = response.data.video_id;
        showYouTubePlayer.value = true;
        currentSource.value = 'youtube';
        isPlaying.value = true;
        emit('playing', props.spotifyId);

        // Auto-stop after 30 seconds
        setTimeout(() => {
            if (currentSource.value === 'youtube') {
                stop();
            }
        }, 30000);
    } catch (error) {
        console.error('YouTube failed:', error);
        throw error;
    }
};

const stop = () => {
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer = null;
    }

    showYouTubePlayer.value = false;
    isPlaying.value = false;
    youtubeVideoId.value = null;
    currentSource.value = null;
    emit('stopped');
};

const openInSpotify = () => {
    window.open(`spotify:track:${props.spotifyId}`, '_blank');
};
</script>

<template>
    <!-- YouTube Player Modal -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="showYouTubePlayer" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center" @click="stop">
            <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 max-w-md w-full mx-4" @click.stop>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="font-medium text-white text-sm">{{ title }}</div>
                        <div class="text-xs text-gray-400">{{ artist }}</div>
                    </div>
                    <button @click="stop" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- YouTube Embed -->
                <div class="aspect-video bg-black rounded overflow-hidden">
                    <iframe
                        v-if="youtubeVideoId"
                        :src="`https://www.youtube.com/embed/${youtubeVideoId}?autoplay=1&enablejsapi=1`"
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen
                        class="w-full h-full"
                    ></iframe>
                </div>

                <div class="mt-3 text-xs text-gray-500 text-center">
                    Playing 30s preview via YouTube
                </div>
            </div>
        </div>
    </Transition>

    <!-- Play Button -->
    <div class="flex items-center gap-1">
        <button
            v-if="!isPlaying"
            @click="play"
            :disabled="isLoading"
            class="p-2 hover:bg-purple-900/20 rounded-lg transition-all disabled:opacity-50"
            :title="previewUrl ? 'Play preview (Spotify)' : 'Play preview (YouTube)'"
        >
            <Play v-if="!isLoading" class="w-4 h-4 text-gray-500" />
            <svg v-else class="w-4 h-4 animate-spin text-purple-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>

        <button
            v-else
            @click="stop"
            class="p-2 bg-purple-900/20 hover:bg-purple-900/30 rounded-lg transition-all"
            title="Stop preview"
        >
            <Pause class="w-4 h-4 text-purple-400" />
        </button>

        <!-- Open in Spotify button -->
        <button
            @click="openInSpotify"
            class="p-2 hover:bg-gray-700/50 rounded-lg transition-all"
            title="Open in Spotify app"
        >
            <ExternalLink class="w-3.5 h-3.5 text-gray-500" />
        </button>
    </div>

    <!-- Debug badge (remove in production) -->
    <div v-if="isPlaying && currentSource" class="absolute top-0 right-0 text-[8px] bg-green-500 text-white px-1 rounded">
        {{ currentSource === 'preview' ? 'Spotify' : 'YT' }}
    </div>
</template>
