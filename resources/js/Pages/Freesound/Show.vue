<template>
    <Layout>
        <Head :title="sound?.name || 'Sound Details'" />

        <Link :href="route('freesound.index')" class="btn btn-outline-secondary mb-3">
            ← Back to Search
        </Link>

        <div class="row">
            <div class="col-lg-8">
                <h1>{{ sound.name }}</h1>

                <p class="text-muted">
                    by <strong>{{ sound.username }}</strong>
                    <span v-if="sound.created"> · uploaded {{ formatDate(sound.created) }}</span>
                </p>

                <!-- Preview -->
                <div v-if="sound.previews?.['preview-lq-mp3']" class="mb-4">
                    <h5>Preview</h5>
                    <audio :src="sound.previews['preview-lq-mp3']" controls class="w-100"></audio>
                </div>

                <div v-if="sound.previews?.['preview-hq-mp3']" class="mb-4">
                    <h6>High Quality Preview</h6>
                    <audio :src="sound.previews['preview-hq-mp3']" controls class="w-100"></audio>
                </div>

                <!-- Description -->
                <div v-if="sound.description" class="mb-4">
                    <h5>Description</h5>
                    <p>{{ sound.description }}</p>
                </div>

                <!-- Tags -->
                <div v-if="sound.tags?.length" class="mb-4">
                    <h5>Tags</h5>
                    <span
                        v-for="tag in sound.tags"
                        :key="tag"
                        class="badge bg-secondary me-1 fs-6"
                    >{{ tag }}</span>
                </div>

                <!-- Details Table -->
                <div class="mb-4">
                    <h5>Details</h5>
                    <table class="table table-sm">
                        <tbody>
                        <tr>
                            <th>Duration</th>
                            <td>{{ formatDuration(sound.duration) }}</td>
                        </tr>
                        <tr v-if="sound.type">
                            <th>File Type</th>
                            <td>{{ sound.type.toUpperCase() }}</td>
                        </tr>
                        <tr v-if="sound.avg_rating">
                            <th>Rating</th>
                            <td>⭐ {{ sound.avg_rating.toFixed(1) }} / 5</td>
                        </tr>
                        <tr v-if="sound.num_downloads !== undefined">
                            <th>Downloads</th>
                            <td>{{ sound.num_downloads.toLocaleString() }}</td>
                        </tr>
                        <tr v-if="sound.license">
                            <th>License</th>
                            <td>{{ sound.license }}</td>
                        </tr>
                        <tr v-if="sound.geotag?.lat">
                            <th>Geotag</th>
                            <td>{{ sound.geotag.lat.toFixed(4) }}, {{ sound.geotag.lon.toFixed(4) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Download -->
                <div class="mb-4">
                    <a
                        :href="sound.download"
                        target="_blank"
                        class="btn btn-success btn-lg"
                        v-if="sound.download"
                    >
                        ⬇ Download from Freesound
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Waveform -->
                <div v-if="sound.images?.waveform_l" class="mb-3">
                    <h5>Waveform</h5>
                    <img :src="sound.images.waveform_l" alt="Waveform" class="img-fluid rounded" />
                </div>

                <!-- Spectrum -->
                <div v-if="sound.images?.spectrum_l" class="mb-3">
                    <h5>Spectrum</h5>
                    <img :src="sound.images.spectrum_l" alt="Spectrum" class="img-fluid rounded" />
                </div>

                <!-- Similar Sounds -->
                <div v-if="similar?.length">
                    <h5>Similar Sounds</h5>
                    <div v-for="s in similar" :key="s.id" class="card mb-2">
                        <div class="card-body py-2">
                            <Link
                                :href="route('freesound.show', s.id)"
                                class="text-decoration-none"
                            >
                                <strong>{{ s.name }}</strong>
                            </Link>
                            <br />
                            <small class="text-muted">
                                {{ s.username }} · {{ formatDuration(s.duration) }}
                            </small>
                            <audio
                                v-if="s.previews?.['preview-lq-mp3']"
                                :src="s.previews['preview-lq-mp3']"
                                controls
                                class="w-100 mt-1"
                                preload="none"
                            ></audio>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Layouts/Layout.vue'

const props = defineProps({
    sound: { type: Object, required: true },
    similar: { type: Array, default: () => [] },
})

function formatDuration(seconds) {
    if (!seconds) return 'N/A'
    const mins = Math.floor(seconds / 60)
    const secs = Math.floor(seconds % 60)
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-GB', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}
</script>
