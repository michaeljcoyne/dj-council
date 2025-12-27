<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Music, CheckCircle, XCircle, Settings } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    spotifyConnected: Boolean,
});

const disconnectForm = useForm({});

const disconnectSpotify = () => {
    if (confirm('Disconnect Spotify? You will no longer be able to preview songs in-app.')) {
        disconnectForm.post('/spotify/client/disconnect');
    }
};
</script>

<template>
    <Head title="Settings" />

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
                        <Link href="/client/playlists" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            My Playlists
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main -->
            <main class="flex-1 bg-gradient-to-b from-gray-900 to-black">
                <div class="p-6">
                    <div class="max-w-3xl mx-auto">
                        <h1 class="text-3xl font-bold text-white mb-6">Settings</h1>

                        <!-- Spotify Integration -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6 mb-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h2 class="text-xl font-semibold text-white mb-2">Spotify Integration</h2>
                                    <p class="text-sm text-gray-400">
                                        Connect your Spotify Premium account to preview songs directly in DJ Council
                                    </p>
                                </div>
                                <Music class="w-8 h-8 text-green-500" />
                            </div>

                            <div v-if="spotifyConnected" class="space-y-4">
                                <div class="flex items-center gap-3 p-4 bg-green-900/20 border border-green-500/30 rounded-lg">
                                    <CheckCircle class="w-5 h-5 text-green-400 flex-shrink-0" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-white">Spotify Connected</p>
                                        <p class="text-xs text-gray-400 mt-0.5">You can now play 30-second previews of any song</p>
                                    </div>
                                </div>

                                <button
                                    @click="disconnectSpotify"
                                    :disabled="disconnectForm.processing"
                                    class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg text-sm transition-colors disabled:opacity-50"
                                >
                                    Disconnect Spotify
                                </button>
                            </div>

                            <div v-else class="space-y-4">
                                <div class="flex items-start gap-3 p-4 bg-gray-700/30 border border-gray-600 rounded-lg">
                                    <XCircle class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-white mb-2">Spotify Not Connected</p>
                                        <p class="text-xs text-gray-400 mb-3">Without Spotify, you can only open songs in the Spotify app. Connect Spotify Premium to:</p>
                                        <ul class="text-xs text-gray-400 space-y-1 ml-4 list-disc">
                                            <li>Play 30-second previews directly in DJ Council</li>
                                            <li>Preview every song in your playlists</li>
                                            <li>Better music discovery experience</li>
                                        </ul>
                                    </div>
                                </div>

                                <a
                                    href="/spotify/client/connect"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors"
                                >
                                    <Music class="w-5 h-5" />
                                    Connect Spotify Premium
                                </a>

                                <p class="text-xs text-gray-500">
                                    Requires Spotify Premium subscription. Free Spotify accounts cannot play music via API.
                                </p>
                            </div>
                        </div>

                        <!-- Account Info -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-white mb-4">Account Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-400 uppercase">Name</label>
                                    <p class="text-white">{{ user.name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-400 uppercase">Email</label>
                                    <p class="text-white">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
