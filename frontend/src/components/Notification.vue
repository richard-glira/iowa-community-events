<template>
    <div>
        <v-alert 
            dismissible
            :type="notificationTypeClass"
            value="true"
        >
        {{ notification.message }}</v-alert>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        props: {
            notification: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                timeout: null
            }
        },
        mounted() {
            this.timeout = setTimeout(() => this.remove(this.notification), 100000)
        },
        beforeDestroy() {
            clearTimeout(this.timeout)
        },
        computed: {
            notificationTypeClass() {
                return this.notification.type
            }
        },
        methods: mapActions('notification', ['remove'])
    }
</script>

<style scoped>
.notification-bar {
    margin: 1em 0 1em;
}
</style>