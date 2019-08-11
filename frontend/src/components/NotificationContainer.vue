<template>
    <div class="notification-container">
        <Notification
            v-for="notification in notifications"
            :key="notification.id"
            :notification="notification"
         />
    </div>
</template>

<script>
import Notification from '@/components/Notification.vue';
import { mapState } from 'vuex';

export default {
    components: {
        Notification
    },
    computed: mapState('notification', ['notifications'])
}
</script>

<style scoped>
.notification-container {
    position: fixed;
    bottom: 0;
    right: 0;
    padding-bottom: 20px;
    padding-right: 40px;
}
</style>

