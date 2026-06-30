<script setup>
import {router} from '@inertiajs/vue3'
import {inject} from "vue";

const props = defineProps({
    boards: { type: Array, default: () => [] },
})


const { loadBoard } = inject('loadBoard')

const deleteBoard = (boardId) => {
    if (!confirm('Delete this SoundBoard?')) return

    router.delete(route('freesound.soundboards.destroy', boardId), {
        preserveState: true,
        preserveScroll: true,
    })
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

</script>
<template>
<aside class="w-80 flex-shrink-0">
<div class="bg-white rounded-lg shadow-sm border p-4 sticky top-4">
    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
        Saved SoundBoards
    </h2>

    <!-- Empty State -->
    <div v-if="!boards?.length" class="text-gray-400 text-sm text-center py-8">
        No saved boards yet.<br />
        Search for sounds and click<br />
        "Save as SoundBoard" to create one!
    </div>

    <!-- Board List -->
    <div v-else class="space-y-3">
        <div
            v-for="board in boards"
            :key="board.id"
            class="border rounded-lg p-3 hover:bg-gray-50 "

        >
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0 cursor-pointer hover:italic transition-all" @click="loadBoard(board)">
                    <span class="font-medium text-sm truncate">{{ board.title }}</span>
                    <span class="text-xs text-gray-500 mt-0.5">
                                    "{{ board.search_term }}" |
                                </span>
                    <span class="text-xs text-gray-400 mt-0.5">
                                    {{ formatDate(board.created_at) }}
                                </span>
                </div>

                <button
                    @click="deleteBoard(board.id)"
                    class="text-gray-400 hover:text-red-500 transition-colors ml-2 flex-shrink-0"
                    title="Delete board"
                >
                    ✕
                </button>
            </div>

            <div v-if="board.sounds?.length" class="flex gap-1 mt-2 flex-wrap">
                <div
                    v-for="sound in board.sounds"
                    :key="sound.id"
                    class="group relative"
                >
                    <img
                        v-if="sound.waveform_url"
                        :src="sound.waveform_url"
                        :alt="sound.name"
                        class="w-6 h-6 rounded object-cover bg-gray-100 "
                        :title="sound.name"
                    />
                    <div
                        v-else
                        class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500"
                    >
                        🎵
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</aside>
</template>
