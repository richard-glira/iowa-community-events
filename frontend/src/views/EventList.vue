<template>
  <div class="pt-3">
    <h1 class="text-xs-center event-title">Events for <em>{{ user.user.name }}</em></h1>
    <EventCard v-for="event in event.events" :key="event.id" :event="event" :user="user" />
    <v-tooltip bottom>
      <template v-if="page != 1" v-slot:activator="{ on }">
        <v-btn
          v-if="page !== 1"
          fab
          small
          class="event-pagination" 
          color="success float-left"
          v-on="on"><router-link class="event-pagination-links" :to="{ name: 'event-list', query: { page: page - 1 } }" rel="prev"><v-icon>arrow_back</v-icon></router-link></v-btn>
        <template v-if="hasNextPage"> | </template>
      </template>
      <span>Previous Page</span>
    </v-tooltip>
    <v-tooltip bottom>
      <template v-slot:activator="{ on }">
        <v-btn
          v-if="hasNextPage"
          fab
          small
          class="event-pagination" 
          color="success float-right"
          v-on="on"
        ><router-link v-if="hasNextPage" class="event-pagination-links" :to="{ name: 'event-list', query: { page: page + 1 } }" rel="next"><v-icon>arrow_forward</v-icon></router-link></v-btn>
      </template>
      <span>Next Page</span>
    </v-tooltip>
  </div>
</template>4

<script>
  import { mapState } from 'vuex';
  import store from "@/store/store.js";
  import EventCard from "@/components/EventCard.vue";
  import { filter } from 'lodash';

  function getPageEvents(routeTo, next) {
    const currentPage = parseInt(routeTo.query.page) || 1

    store.dispatch('event/fetchEvents', {
        page: currentPage
    }).then(() => {
        routeTo.params.page = currentPage; // Sends the currentPage into the component as a prop
        next();
    });
  }

  export default {
    props: {
      page: {
        type: Number,
        required: true
      }
    },
    beforeRouteEnter(routeTo, routeFrom, next) {
      getPageEvents(routeTo, next)
    },
    beforeRouteUpdate(routeTo, routeFrom, next) {
        getPageEvents(routeTo, next)
    },
    components: {
      EventCard
    },
    async created() {      
      await this.$store.dispatch('user/fetchUser');

      await this.$store.dispatch('event/fetchEvents', {
        page: this.page,
      });
    },
    computed: {
      hasNextPage() {
        return this.event.eventsTotal > this.page * this.event.perPage;
      },
      loggedUser() {
        return this.user.user.id;
      },
      ...mapState(['event', 'user'])
    }, 
    data() {
      return {
        fetchedEvents: {},
        userEvents: ''
      }
    }
  }
</script>

<style scoped>
  h1 .event-list-header {
    padding-top: 10px!important;
  }
  .event-pagination {
    border-radius: 100px;
  }
  .event-pagination-links {
    text-decoration: none;
    color: white;
  }
  .event-title {
    color: #00897B;
    font-family: "Comic Sans MS", cursive, sans-serif;
  }
</style>

