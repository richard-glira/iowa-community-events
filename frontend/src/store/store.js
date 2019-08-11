import Vue from "vue";
import Vuex from "vuex";
import * as event from "@/store/modules/event.js";
import * as notification from "@/store/modules/notification.js";
import * as user from "@/store/modules/user.js";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    event,
    notification,
    user
  },
  state: {
    // Future feature: add CRUD functionality for categories.
    categories: [
      "Sustainability",
      "Nature",
      "Animal Welfare",
      "Housing",
      "Education",
      "Food",
      "Community"
    ]
  }
});
