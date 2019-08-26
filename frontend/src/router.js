import Vue from "vue";
import Router from "vue-router";
import CreateEvent from "@/views/CreateEvent.vue";
import EventList from "@/views/EventList.vue";
import EventShow from "@/views/EventShow.vue";
import Login from "@/views/Login.vue";
import NetworkIssue from "@/views/NetworkIssue.vue";
import NotFound from "@/views/NotFound.vue";
import Register from "@/views/Register.vue";
import NProgress from "nprogress";
import store from "@/store/store";

Vue.use(Router);

const router = new Router({
  mode: "history",
  base: process.env.BASE_URL,
  routes: [
    {
      path: "/",
      name: "event-list",
      component: EventList,
      props: true
    },
    {
      path: "/login",
      name: "login",
      component: Login
    },
    {
      path: "/register",
      name: "register",
      component: Register
    },
    {
      path: "/event/create",
      name: "create-event",
      component: CreateEvent
    },
    {
      path: "/event/:id",
      name: "event-show",
      component: EventShow,
      props: true,
      beforeEnter(routeTo, routeFrom, next) {
        store
          .dispatch("event/fetchEvent", routeTo.params.id)
          .then(event => {
            routeTo.params.event = event;
            next();
          })
          .catch(error => {
            if (error.response && error.response.status === 404) {
              next({ name: "404", params: { resource: "event" } });
            } else {
              next({ name: "network-issue" }); // You can redirect with the folliwing line and using the route name property to specify which component to route to.
            }
          });
      }
    },
    {
      // This is route that can directed to, if a valid url is error'd 404 or 400
      path: "/404",
      name: "404",
      component: NotFound,
      props: true // I added this so we can receive the param as a prop
    },
    {
      // Here's the new catch all route (this includes catching any invalid routes)
      path: "*",
      redirect: { name: "404", params: { resource: "page" } } // The resource "page" is missing because of an invalid URL e.g. /api/v1/asdf*&&*, we are then using this resource in our 404 NotFound component to be specific
    },
    {
      path: "/network-issue",
      name: "network-issue",
      component: NetworkIssue
    }
    // {
    // path: "/about",
    // name: "about",
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    //   component: () =>
    //     import(/* webpackChunkName: "about" */ "./views/About.vue")
    // }
  ]
});

router.beforeEach((routeTo, routeFrom, next) => {
  NProgress.start();
  next();
});

router.afterEach(() => {
  NProgress.done();
});

export default router;
