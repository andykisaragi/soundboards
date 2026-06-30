<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    img: {
        type: String,
        required: true,
    },
})

const isPlaying = ref(false)
const currentTime = ref(0)
const duration = ref(0)

let audio = null

const progressPercent = computed(() => {
    if (!duration.value) return 0
    return (currentTime.value / duration.value) * 100
})

onMounted(() => {
    audio = new Audio(props.src)

    audio.addEventListener('loadedmetadata', () => {
        duration.value = audio.duration
    })

    audio.addEventListener('timeupdate', () => {
        currentTime.value = audio?.currentTime
    })

    audio.addEventListener('ended', () => {
        isPlaying.value = false
        currentTime.value = 0
    })
})

onUnmounted(() => {
    if (audio) {
        audio.pause()
        audio.src = ''
        audio = null
    }
})

const togglePlay = () => {
    if (!audio) return

    if (isPlaying.value) {
        audio.pause()
        audio.currentTime = 0
        isPlaying.value = false
    } else {
        audio.play()
        isPlaying.value = true
    }
}
</script>

<template>
    <div class="relative overflow-hidden rounded-full">
        <button
            @click="togglePlay"
            class="relative w-full block"
            :class="isPlaying
                ? 'bg-red-500 hover:bg-red-600'
                : 'bg-green-500 hover:bg-green-600'"
        >
            <img :src="img" class="w-full block" alt="waveform - click to play"/>

            <!-- Progress line positioned by percentage -->
            <div
                class="absolute inset-0 pointer-events-none"
            >
                <div v-if="isPlaying"
                    class="absolute top-0 bottom-0 w-[3px] bg-white shadow-[0_0_8px_3px_rgba(255,255,255,0.7)]"
                    :style="{ left: `${progressPercent}%` }"
                ></div>
            </div>
        </button>
    </div>
</template>
