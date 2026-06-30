<script setup>
import {Link} from "@inertiajs/vue3";
import AudioPlayer from "./AudioPlayer.vue";

const props = defineProps({
    sound: { type: Object, default: null },
})

const formatDuration = (seconds) => {
    if (!seconds) return 'N/A'
    const mins = Math.floor(seconds / 60)
    const secs = Math.floor(seconds % 60)
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

</script>

<template>
    <div class="col-md-4 mb-4 ">
        <div class="card relative group">
            <div class="card-body position-relative z-10 ">
                <div class="mt-auto">
                    <AudioPlayer :src="sound.previews['preview-lq-mp3']" :img="sound.images?.waveform_m"/>
                </div>
                <!-- Tooltip -->
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block z-10">
                    <div class="bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                        {{ sound.name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    transition: transform 0.15s ease;
}
.card:hover {
    transform: translateY(-3px);
}
</style>
