<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Track Manager</h1>

    <div class="max-w-4xl mx-auto">
      <TrackForm 
        :track="currentTrack" 
        @form-submitted="handleFormSubmitted" 
        class="mb-8"
      />

      <h2 class="text-2xl font-bold mb-4">Tracks</h2>
      <TrackList />

      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
        <p>{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTrackStore } from './stores/trackStore';
import TrackForm from './components/TrackForm.vue';
import TrackList from './components/TrackList.vue';

const trackStore = useTrackStore();

const currentTrack = computed(() => trackStore.currentTrack);
const error = computed(() => trackStore.error);

function handleFormSubmitted() {
  // Refresh the track list after form submission
  trackStore.fetchTracks();
}
</script>

<style>
/* Global styles can go here */
</style>
