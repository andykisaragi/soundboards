<script setup>
import { onUnmounted, provide, ref, watch} from 'vue'
import {Head, router, useForm} from '@inertiajs/vue3'
import Layout from '@/Layouts/Layout.vue'
import Sound from "../../components/Sound.vue";
import SavedBoards from "../../components/SavedBoards.vue";

const props = defineProps({
    results: { type: Object, default: null },
    filters: { type: Object, default: () => ({}) },
    boards: { type: Array, default: () => [] },
})

const sounds = ref(null)
const hasSearched = ref(false)
const searching = ref(false)
const saving = ref(false)

const form = useForm({
    query: props.filters.query || '',
    sort: props.filters.sort || 'score',
    page_size: props.filters.page_size || 15,
    filter: props.filters.filter || '',
})

const search = async () => {
    hasSearched.value = true
    searching.value = true
    setTimeout(() => {
        form.get(route('freesound.search'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                searching.value = false
                sounds.value = pickRandom(props.results.results, 9)
                console.log(sounds.value);
            },
            finally: () => {
                searching.value = false
            }
        })
    }, 500);
}

let intervalId = null
watch(searching, (isSearching) => {
    if (isSearching) {
        intervalId = setInterval(() => {
            if (props.results?.results) {
                sounds.value = pickRandom(props.results.results, 9)
            }
        }, 50)
    } else {
        if (intervalId) {
            clearInterval(intervalId)
            intervalId = null
        }
    }
})

const pickRandom = (arr, count) => {
    if (count >= arr.length) return [...arr]

    const result = [...arr]
    for (let i = result.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1))
        ;[result[i], result[j]] = [result[j], result[i]]
    }
    return result.slice(0, count)
}

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId)
    }
})

/**
 * Prompt for a title and save the current results as a SoundBoard.
 */
const promptSaveBoard = () => {
    const title = window.prompt('Enter a title for this SoundBoard:')
    if (!title || !title.trim()) return

    saving.value = true

    router.post(route('freesound.soundboards.store'), {
        title: title.trim(),
        search_term: form.query,
        sounds: sounds.value.map(sound => ({
            id: sound.id,
            name: sound.name,
            username: sound.username,
            previews: sound.previews || null,
            images: sound.images || null,
        })),
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false
        },
        onError: (errors) => {
            saving.value = false
            console.error('Failed to save SoundBoard:', errors)
            alert('Failed to save SoundBoard: ' + (errors.title || errors.sounds || 'Unknown error'))
        },
    })
}

const loadBoard = (board) => {
    if (!board.sounds?.length) return

    sounds.value = board.sounds.map(sound => ({
        id: parseInt(sound.freesound_id),
        name: sound.name,
        username: sound.username,
        previews: {
            'preview-lq-mp3': sound.preview_url,
        },
        images: {
            waveform_m: sound.waveform_url,
        },
    }))

    form.query = board.search_term
    hasSearched.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

provide('loadBoard', { loadBoard })

</script>

<template>
    <Layout class="font-mono">
        <Head title="Random Sound Board" />

        <div v-if="$page.props.errors?.api" class="alert alert-danger">
            {{ $page.props.errors.api }}
        </div>

        <div  class="row flex gap-4">
            <div  class="basis-1/4">
                <form @submit.prevent="search" class="mb-4">

                    <label for="query" class="form-label">Find sounds:</label>
                    <div class="row g-3 flex">
                        <div class="basis-3/4">
                            <input
                                type="text"
                                id="query"
                                v-model="form.query"
                                class="form-control form-control-lg border rounded-lg shadow-sm p-2"
                                placeholder="e.g. piano, rain, footsteps, synth pad..."
                                autofocus
                            />
                        </div>

                        <div class="basis-1/4">
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg px-5"
                                :disabled="searching"
                            >
                                {{ searching ? '🔍 ...' : '🔍 Go' }}
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Results Count -->
                <div v-if="sounds?.length && !searching" class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0">
                        Found <strong>{{ results.count?.toLocaleString() }}</strong> sounds.
                    </p>

                    <button
                        v-if="sounds?.length"
                        @click="promptSaveBoard"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        💾 Save SoundBoard
                    </button>
                </div>
            </div>

            <div class="rounded-lg shadow-sm border p-4 sticky top-4 basis-1/2">
                <div v-if="sounds?.length"  class="row flex flex-wrap">
                    <Sound
                        v-for="sound in sounds"
                        :key="sound.id"
                        :sound="sound"
                        class="col-md-4 mb-4 basis-1/3 p-2"
                    />
                </div>
                <div v-else-if="hasSearched && !searching" class="alert alert-info">
                    No sounds found. Try a different query!
                </div>
                <div v-else class="alert alert-info text-lg font-mono">
                    Search for sounds 👈<span v-if="boards.length">, or load a saved board 👉</span>
                </div>
            </div>
            <div  class="basis-1/4">
                <SavedBoards :boards="boards" />
            </div>
        </div>
    </Layout>
</template>
