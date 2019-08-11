<template>
  <div class="pt-3">
    <h1 class="text-xs-center">Events for <em>{{ user.user.name }}</em></h1>
    <EventCard v-for="event in event.events" :key="event.id" :event="event" />
    <template v-if="page != 1">
      <router-link :to="{ name: 'event-list', query: { page: page - 1 } }" rel="prev">Prev Page</router-link>
      <template v-if="hasNextPage"> | </template>
    </template>
    <v-btn
      outlined
      fab
      class="event-pagination" 
      color="success"
    ><router-link v-if="hasNextPage" class="event-pagination-links" :to="{ name: 'event-list', query: { page: page + 1 } }" rel="next"><v-icon>arrow_forward</v-icon></router-link>
    </v-btn>
  </div>
</template>4

<script>
  import { mapState } from 'vuex';
  import store from "@/store/store.js";
  import EventCard from "@/components/EventCard.vue";

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
    components: {
      EventCard
    },
    beforeRouteEnter(routeTo, routeFrom, next) {
      getPageEvents(routeTo, next)
    },
    beforeRouteUpdate(routeTo, routeFrom, next) {
        getPageEvents(routeTo, next)
    },
    created() {
      this.$store.dispatch('user/fetchUser');
      // console.log(this.event);
      this.$store.dispatch('event/fetchEvents', {
        perPage: this.perPage,
        page: this.page
      })
    },
    computed: {
      hasNextPage() {
        console.log(this.event.eventsTotal, this.page, this.event.perPage);
        return this.event.eventsTotal > this.page * this.event.perPage;
      },
      ...mapState(['event', 'user'])
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
</style>

