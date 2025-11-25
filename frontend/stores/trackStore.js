import { defineStore } from 'pinia';
import axios from 'axios';

export const useTrackStore = defineStore('track', {
    state: () => ({
        tracks: [],
        loading: false,
        error: null,
        currentTrack: null
    }),

    actions: {
        async fetchTracks() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get('http://localhost:8000/api/tracks');
                this.tracks = response.data;
            } catch (error) {
                this.error = error.response?.data?.errors || 'Failed to fetch tracks';
                console.error('Error fetching tracks:', error);
            } finally {
                this.loading = false;
            }
        },

        async createTrack(trackData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post('http://localhost:8000/api/tracks', trackData);
                this.tracks.push(response.data);
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.errors || 'Failed to create track';
                console.error('Error creating track:', error);
                return {
                    success: false,
                    errors: error.response?.data?.errors || ['An error occurred while creating the track']
                };
            } finally {
                this.loading = false;
            }
        },

        async updateTrack(trackData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.put(`http://localhost:8000/api/tracks/${trackData.id}`, trackData);

                // Update the track in the tracks array
                const index = this.tracks.findIndex(t => t.id === trackData.id);
                if (index !== -1) {
                    this.tracks[index] = response.data;
                }

                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.errors || 'Failed to update track';
                console.error('Error updating track:', error);
                return {
                    success: false,
                    errors: error.response?.data?.errors || ['An error occurred while updating the track']
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteTrack(trackId) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`http://localhost:8000/api/tracks/${trackId}`);

                // Remove the track from the tracks array
                const index = this.tracks.findIndex(t => t.id === trackId);
                if (index !== -1) {
                    this.tracks.splice(index, 1);
                }

                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.error || 'Failed to delete track';
                console.error('Error deleting track:', error);
                return {
                    success: false,
                    error: error.response?.data?.error || 'An error occurred while deleting the track'
                };
            } finally {
                this.loading = false;
            }
        },

        setCurrentTrack(track) {
            this.currentTrack = track ? { ...track } : null;
        },

        clearCurrentTrack() {
            this.currentTrack = null;
        }
    }
});