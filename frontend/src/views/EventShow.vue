<template>
    <div>
        <div class="event-header">
            <span class="eyebrow">@{{ event.event_time ? event.event_time : event.time }} on {{ event.event_date ? event.event_date : event.date | date }}</span>
            <h1 class="title">{{ event.event_name ? event.event_name : event.title }}</h1>
            <h5>Organized by {{ event.organizer ? event.organizer.name : '' }}</h5>            
            <!-- <h5>Organized by {{ event.organizer ? event.organizer.name : '' }}</h5> -->
            <h5>Category: {{ event.category }}</h5>
        </div>
        <!-- <BaseIcon name="map"><h2>Location</h2></BaseIcon> -->
        <address>{{ event.location }}</address>
        <h2>Event details</h2>
        <p>{{ event.description }}</p>
        <h2>Attendees
            <span class="badge -fill-gradient">{{ event.attendees ? event.attendees.length : 0 }}</span>
        </h2>
        <ul class="list-group">
            <li v-for="(attendee, index) in event.attendees" :key="index" class="list-item">
                <b>{{ attendee.name }}</b>
            </li>
        </ul>
    </div>
</template>

<script>
// import { mapState, mapActions } from 'vuex';
import store from '@/store/store' // Work around when $this is out of scope, import store to call fetchEvent located in event namespaced module

// beforeRouteEnter() is a In-Component Route Guard methods to incorporate a NProgress bar and can be used for may other functionality that is required either beforeRouteEnter/beforeRouteUpdate/beforeRouteLeave.

export default {
    props: {
        event: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
    .location {
        margin-bottom: 0;
    }
    .location > .icon {
        margin-left: 10px;
    }
    .event-header > .title {
        margin: 0;
    }
    .list-group {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .list-group > .list-item {
        padding: 1em 0;
        border-bottom: solid 1px #e5e5e5;
    }
</style>