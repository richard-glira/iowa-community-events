import Vue from "vue";
import Vuetify from "vuetify";
import App from "./App.vue";
import router from "./router";
import store from "./store/store";
import upperFirst from "lodash/upperFirst";
import camelCase from "lodash/camelCase";
import Vuelidate from "vuelidate";
import DateFilter from "@/filters/date.js";

import "nprogress/nprogress.css";
import "vuetify/dist/vuetify.min.css"; // Ensure you are using css-loader

Vue.filter("date", DateFilter);

Vue.use(Vuetify);

Vue.use(Vuelidate);

// This is a global mixin, BECAREFUL WHEN USING GLOBAL MIXINS

// Vue.mixin({
//   mounted() {
//     console.log("I am mixed into every component")
//   }
// })

const requireComponent = require.context(
  "./components",
  false,
  /Base[A-Z]\w+\.(vue|js)$/
);

requireComponent.keys().forEach(fileName => {
  const componentConfig = requireComponent(fileName);

  const componentName = upperFirst(
    camelCase(fileName.replace(/^\.\/(.*)\.\w+$/, "$1"))
  );

  Vue.component(componentName, componentConfig.default || componentConfig);
});

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
