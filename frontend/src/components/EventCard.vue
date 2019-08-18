<template>
    <router-link class="event-link" :to="{ name: 'event-show', params: { id: event.id } }">
        <div class="event-card -shadow">
            <h3 v-if="isEventOrganizer" class="text-xs-right font-color">*You're the event organizer.</h3>
            <h4 class="event-info pb-1 title">{{ event.event_name }}</h4>
            <span class="date-info event-info eyebrow">@{{ event.event_time }} on {{ event.event_date | date }}</span>
            <div>
              <span class="float-right">
                <v-icon class="pt-1">people_outline</v-icon><span class="event-info mb-2"><b class="pl-1">{{ event.attendees.length }} attending</b></span>
              </span>
            </div>
        </div>
    </router-link>
</template>

<script>
    import { mapActions } from 'vuex';
    export default {
        props: {
            event: {
              type: Object,
              required: true
            },
            user: {
              type: Object,
              required: true
            }
        },
        computed: {
          isEventOrganizer() {
             return this.event.user_id !== this.user.user.id ? false : true;
          }
        }
    }   
</script>

<style scoped>
    .date-info {
      color: #00897B;
    }
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
      font-family: "Comic Sans MS", cursive, sans-serif!important;
    }
    .font-color {
      color: #00c853!important;
    }
</style>