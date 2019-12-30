<template>
	<div>
		<transition-group name="list">
			<div id="list-refreshing" key="loading" class="icon-loading-small" :class="{refreshing: refreshing}" />
			<Envelope
				v-for="env in envelopes"
				:key="env.uid"
				:data="env"
				:folder="folder"
				@delete="$emit('delete', env.uid)"
				:selected="isEnvelopeSelected(envelopes.indexOf(env))"
				@select="onEnvelopeSelected"
			/>
			<div id="load-more-mail-messages" key="loadingMore" :class="{'icon-loading-small': loadingMore}" />
		</transition-group>
	</div>
</template>

<script>
import Envelope from './Envelope'

export default {
	name: 'EnvelopeList',
	components: {
		Envelope,
	},
	props: {
		account: {
			type: Object,
			required: true,
		},
		folder: {
			type: Object,
			required: true,
		},
		envelopes: {
			type: Array,
			required: true,
		},
		refreshing: {
			type: Boolean,
			required: true,
			default: true,
		},
		loadingMore: {
			type: Boolean,
			required: true,
		},
	},
	data() {
		return {
			shortkeys: {
				del: ['del'],
				flag: ['s'],
				next: ['arrowright'],
				prev: ['arrowleft'],
				refresh: ['r'],
				unseen: ['u'],
			},
			selection: [],
		}
	},
	methods: {
		isEnvelopeSelected(idx) {
			if (this.selection.length == 0) {
				return false
			} 
			
			return this.selection.includes(idx)
		},
		onEnvelopeSelected(envelope, shiftKey) {
			const idx = this.envelopes.indexOf(envelope)	

			// If this is the first selected envelope, or the shift key is not pressed, simply add the envelope ID to the selection array
			if (!shiftKey || this.selection.length == 0) {
				this.selection.push(idx)
				return
			}

			// Otherwise, add all envelopes between the last selected envelope and this one
			const lastEnv = this.selection[this.selection.length - 1]
			var start = lastEnv + 1
			var last = idx
			if (lastEnv > idx) {
				start = idx
				last = lastEnv - 1
			}
			for (var i = start; i <= last; i++) {
				this.selection.push(i)
			}

			return
		},
	},
}
</script>

<style scoped>
#load-more-mail-messages {
	margin: 10px auto;
	padding: 10px;
	margin-top: 50px;
	margin-bottom: 200px;
}

/* TODO: put this in core icons.css as general rule for buttons with icons */
#load-more-mail-messages.icon-loading-small {
	padding-left: 32px;
	background-position: 9px center;
}

#list-refreshing {
	overflow-y: hidden;
	min-height: 0;
	transition-property: all;
	transition-duration: 0.5s;
	transition-timing-function: ease-in-out;
}

#list-refreshing.refreshing {
	min-height: 32px;
}

.list-enter-active,
.list-leave-active {
	transition: all var(--animation-quick);
}

.list-enter,
.list-leave-to {
	opacity: 0;
	height: 0px;
	transform: scaleY(0);
}
</style>
