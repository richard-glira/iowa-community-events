<template>
    <router-link class="event-link" :to="{ name: 'event-show', params: { id: event.id } }">
        <div class="event-card -shadow">
            <h4 class="pb-1 title">{{ event.event_name }}</h4>
            <span class="event-info eyebrow">@{{ event.event_time }} on {{ event.event_date | date }}</span>
            <div>
              <span class="float-right">
                <v-icon class="pt-1">people_outline</v-icon><span class="mb-2"><b>{{ Math.floor(Math.random() * 10) }}</b></span>
              </span>
            </div>
            <!-- <BaseIcon name="users">{{event.attendees.length }} attending</BaseIcon> -->
        </div>
    </router-link>
</template>

<script>
    export default {
        props: {
            event: Object,
        },
    }   
</script>

<style scoped>
    .event-card {
      padding: 20px;
      margin-bottom: 24px;
      transition: all 0.2s linear;
      cursor: pointer;
    }
    .event-card:hover {
      transform: scale(1.01);
      box-shadow: 0 3px 12px 0 rgba(0, 0, 0, 0.2), 0 1px 15px 0 rgba(0, 0, 0, 0.19);
    }
    .event-card > .title {
      margin: 0;
    }
    
    .event-link {
      color: black;
      text-decoration: none;
      font-weight: 100;
    }

    .event-info {
      color: #00897B;
      font-family: "Comic Sans MS", cursive, sans-serif;
    }
</style>