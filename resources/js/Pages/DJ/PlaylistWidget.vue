<script setup>
import { ref, onMounted } from 'vue';
import { Music } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    djId: {
        type: Number,
        required: true
    }
});

const playlists = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get(`/api/dj/${props.djId}/playlists`);
        playlists.value = response.data;
    } catch (err) {
        error.value = 'Failed to load playlists';
        console.error(err);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="dj-council-widget bg-gray-900 rounded-lg p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto mb-4"></div>
            <p class="text-gray-400">Loading playlists...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-8">
            <p class="text-red-400">{{ error }}</p>
        </div>

        <!-- Playlists -->
        <div v-else-if="playlists.length > 0">
            <h3 class="text-xl font-bold text-white mb-4">My Playlists</h3>

            <div class="space-y-4">
                <div
                    v-for="playlist in playlists"
                    :key="playlist.id"
                    class="bg-gray-800 rounded-lg overflow-hidden"
                >
                    <!-- Spotify Embed -->
                    <iframe
                        :src="`https://open.spotify.com/embed/playlist/${playlist.id}`"
                        width="100%"
                        height="380"
                        frameborder="0"
                        allowtransparency="true"
                        allow="encrypted-media"
                        class="w-full"
                    ></iframe>
                </div>
            </div>

            <!-- Powered by badge -->
            <div class="mt-4 text-center">
                <a
                    href="/"
                    target="_blank"
                    class="text-xs text-gray-500 hover:text-purple-400 inline-flex items-center"
                >
                    <Music class="w-3 h-3 mr-1" />
                    Powered by DJ Council
                </a>
            </div>
        </div>

        <!-- No Playlists -->
        <div v-else class="text-center py-8">
            <Music class="w-12 h-12 text-gray-600 mx-auto mb-2" />
            <p class="text-gray-400">No playlists available</p>
        </div>
    </div>
</template>

<style scoped>
.dj-council-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    max-width: 100%;
}
</style>
