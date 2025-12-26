<template>
    <div class="min-h-screen bg-gray-900 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white">Create Your DJ Profile</h1>
                <p class="mt-2 text-lg text-gray-400">
                    Set up your professional DJ profile to start getting bookings.
                </p>
            </div>

            <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                <form class="p-8">
                    <div class="space-y-6">
                        <!-- Basic Info -->
                        <div>
                            <h2 class="text-xl font-medium text-white mb-4">Basic Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="stage_name" class="block text-sm font-medium text-gray-300 mb-1">
                                        Stage Name *
                                    </label>
                                    <input
                                        id="stage_name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                        placeholder="DJ Name"
                                    />
                                </div>

                                <div>
                                    <label for="specialty" class="block text-sm font-medium text-gray-300 mb-1">
                                        Specialty
                                    </label>
                                    <input
                                        id="specialty"
                                        type="text"
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                        placeholder="e.g. House & EDM Specialist"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="bio" class="block text-sm font-medium text-gray-300 mb-1">
                                    Bio *
                                </label>
                                <textarea
                                    id="bio"
                                    rows="4"
                                    required
                                    class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                    placeholder="Tell potential clients about yourself, your style, and your experience..."
                                ></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6 flex items-center justify-between">
                            <p class="text-sm text-gray-400">
                                Fields marked with * are required
                            </p>
                            <button
                                type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg text-white font-medium"
                            >
                                Create DJ Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        genres: Array
    },
    data() {
        return {
            form: {
                stage_name: '',
                specialty: '',
                bio: '',
                hourly_rate: 100
            }
        }
    }
}
</script>
