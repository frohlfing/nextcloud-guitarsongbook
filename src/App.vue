<template>
    <!--
    SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<NcContent app-name="guitarsongbook">
    <NcAppNavigation>
      <NcAppNavigationNew v-if="!loading"
                          :text="t('guitarsongbook', 'New note')"
                          :disabled="false"
                          button-id="new-guitarsongbook-button"
                          button-class="icon-add"
                          @click="newNote" />
      <ul>
        <NcAppNavigationItem v-for="note in notes"
                             :key="note.id"
                             :title="note.title ? note.title : t('guitarsongbook', 'New note')"
                             :class="{active: currentNoteId === note.id}"
                             @click="openNote(note)">
          <template slot="actions">
            <NcActionButton v-if="note.id === -1"
                            icon="icon-close"
                            @click="cancelNewNote(note)">
              {{ t('guitarsongbook', 'Cancel note creation') }}
            </NcActionButton>
            <NcActionButton v-else
                            icon="icon-delete"
                            @click="deleteNote(note)">
              {{ t('guitarsongbook', 'Delete note') }}
            </NcActionButton>
          </template>
        </NcAppNavigationItem>
      </ul>
    </NcAppNavigation>
    <NcAppContent>
      <div class="controls-wrapper">
        <div data-v-e345c782="" class="location-wrapper">
          <h2 class="location">Neuen Song anlegen</h2>
        </div>
      </div>
      <!--<my-todo-item></my-todo-item>-->
      <div class="main-wrapper">
        <div v-if="currentNote">
          <input ref="title"
            v-model="currentNote.title"
            type="text"
            :disabled="updating">
          <textarea ref="content" v-model="currentNote.content" :disabled="updating" />
          <input type="button"
            class="primary"
            :value="t('guitarsongbook', 'Save')"
            :disabled="updating || !savePossible"
            @click="saveNote">
        </div>
        <div v-else id="emptycontent">
          <div class="icon-file" />
          <h2>{{
           t('guitarsongbook', 'Create a note to get started') }}</h2>
        </div>
      </div>
    </NcAppContent>
  </NcContent>
</template>

<script>

import NcContent from '@nextcloud/vue/dist/Components/NcContent'
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import MyTodoItem from './components/MyTodoItem'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
    NcContent,
    NcAppNavigation,
    NcAppNavigationItem,
    NcAppNavigationNew,
    NcAppContent,
    NcActionButton,
    MyTodoItem,
	},
	data() {
		return {
			notes: [],
			currentNoteId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/guitarsongbook/notes'))
			this.notes = response.data
		} catch (e) {
			console.error(e)
			showError(t('notestutorial', 'Could not fetch notes'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote)
			} else {
				this.updateNote(this.currentNote)
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.notes.push({
					id: -1,
					title: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/guitarsongbook/notes'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not create the note'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNote(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/guitarsongbook/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not update the note'))
			}
			this.updating = false
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNote(note) {
			try {
				await axios.delete(generateUrl(`/apps/guitarsongbook/notes/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
				}
				showSuccess(t('guitarsongbook', 'Note deleted'))
			} catch (e) {
				console.error(e)
				showError(t('guitarsongbook', 'Could not delete the note'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}

  .main-wrapper {
    width: 100%;
    padding: 1rem;
  }

  /* AppControls */

  .controls-wrapper {
    --nc-button-size: 44px;
    --vertical-padding: 4px;
    position: sticky;
    z-index: 2;
    top: 0;
    display: flex;
    width: 100%;
    min-height: calc(44px + 2 * var(--vertical-padding));
    flex-direction: row;
    padding: var(--vertical-padding) 1rem var(--vertical-padding) calc(44px + 2 * var(--vertical-padding));
    border-bottom: 1px solid var(--color-border);
    background-color: var(--color-main-background);
    gap: 8px;
  }

  .location-wrapper {
     display: flex;
     flex: 1;
     flex-direction: column;
     justify-content: center;
   }

  .location {
    width: 100%;
    margin: 0;
    font-size: 1.2em;
    line-height: 1em;
    overflow-x: clip;
    overflow-y: visible;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
</style>
