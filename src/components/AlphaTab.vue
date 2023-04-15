<template>
	<div>
		<b>AlphaTab</b>
		<div id="alphaTab"
			ref="alphaTab"
			data-useworkers="false"
			data-tex="true">
			\title "Hello alphaTab"
			.
			:4 0.6 1.6 3.6 0.5 2.5 3.5 0.4 2.4 |
			3.4 0.3 2.3 0.2 1.2 3.2 0.1 1.1 |
			3.1.1
		</div>
	</div>
</template>

<script>

// import alphaTab from '@coderline/alphatab'

export default {
	name: 'AlphaTab',
	mounted() {
		const element = this.$refs.alphaTab
		// eslint-disable-next-line
    const api = new alphaTab.AlphaTabApi(element)
	},
}

</script>
